<?php

function Loader($name,$title=""){  $Loader =  new Loader($name,$title); Loader::$instances[$name] = & $Loader ;  return $Loader ;}

class Loader{
    /**
     * @var Loader[]
     */
    public static $instances = array();

    public $name = '';

    /**
     * @var get('id')
     */
    public $id ;

    public $tmpRelation ;

    public $current = false;

    public $formFields = array();
    public $fileField = array();
    public $formPanel = array();
    public $dbFields = array();
    public $viewFields = array();
    public $relationFields = array();

    public $titleField = false;
    //Html();

    public $relation_data = array();
    /**
     * @var Relation[]
     */
    public $relations_instances = array();

    public $loaded = false ;
    private $attr = array();

    public function __construct($name,$title ){        $this->name = $name ; if ($title)  $this->titleField = $title ;   }

    /**
     * @param $key
     * @param $value
     *
     * Set Attribute
     */
    public function Attr($key,$value){        $this->attr[$key] = $value ; return $this ;    }

    /**
     * @param $key attriubte
     * @return bool|string as Attribute value
     */
    public function __get($key){
        if (isset($this->attr[$key]))
            return $this->attr[$key] ;
        else
            return false ;
    }

    /*View no need id field its default*/
    public function View($st= array()){ $st = array_merge(array('id'=>'id'),$st) ; $this->viewFields = $st;  return $this ;   }
    public function Relation($name,$st=array()){ $this->relations_instances[$name] = new Relation($name,$st)  ; return $this;}


    /**
     * @param $type
     * @param $name
     * @param $title
     * @param array $opts
     * @return $this
     */
    public function Panel($id,$title,$icon){
        $this->formPanel[$id] = array('title'=>$title,'icon'=>$icon,"cont"=>''/*,'pull'=>$pull*/) ;
        return $this;
    }
    /**
     * @param $type
     * @param $name
     * @param $title
     * @param array $opts
     * @return $this
     */
    public function FormControl($type,$name,$title,$opts=array()){
        $field = array('name'=>$name,'title'=>$title,'type'=>$type) ; $field['opts'] = count($opts) > 0 ?   $opts : false ;
        $this->formFields[] = $field ;
        return $this;
    }

    /**
     * @var PanelMvc Shared objects
     */
    public $PanelMvc ;

    /**
     * @var Listing
     */
    public $Listing ;
    /**
     * @var ListingMvc
     */
    public $ListingMvc ;

    /**
     * @var Form
     */
    public $Form ;
    /**
     * @var FormMvc
     */
    public $FormMvc ;

    /**
     * @var RelationMvc
     */
    public $RelationMvc ;

    public static function Load(){

        global $db;

        foreach(self::$instances as &$loader){
            $loader->loaded = true ;

            if ($loader->name == get('tbl')){
                $loader->id = get('id');
                $loader->current = true ;
            }

            if (! $loader->icon){                 $loader->icon = 'fa fa-circle-o';            }
            if (! $loader->title){                $loader->title = ucfirst( $loader->name );            }

            $loader->dbFields = array_keys( $db->ctypes( $loader->name ) ) ;


            if (! $loader->titleField) {
                if (in_array('title',$loader->dbFields))
                    $loader->titleField = 'title' ;
                elseif (in_array('title_fr',$loader->dbFields))
                    $loader->titleField = 'title_fr' ;
                elseif (in_array('name',$loader->dbFields))
                    $loader->titleField = 'name' ;
                elseif (in_array('name_fr',$loader->dbFields))
                    $loader->titleField = 'name_fr' ;
                else
                    die('Loader::Load() No titleField for table '. $loader->name) ;
            }

            // title field is obligatory
            //if (! $loader->titleField) {                $loader->titleField = 'id' ;            }

            // if loader dosent have any view fields add the id and the titleField
            if (count($loader->viewFields) == 0)
                $loader->View(array($loader->titleField=>'title'));

            foreach($loader->formFields as $f){
                if ($f['type'] == 'file'){
                    $loader->fileField[] = $f['name'] ;
                }
            }

        }

        foreach(self::$instances as &$loader) {
            foreach ($loader->relations_instances as &$relation) {
                $relation->Load($loader);
            }
        }



        //relation need to be loaded
        foreach(self::$instances as &$loader) {
            $loader->PanelMvc   = new PanelMvc($loader);
            $loader->Listing    = new Listing($loader);
            $loader->ListingMvc = new ListingMvc($loader);
            $loader->Form       = new Form($loader);
            $loader->FormMvc    = new FormMvc($loader);
            $loader->RelationMvc = new RelationMvc($loader);
        }

    }

    /**
     * @var Postback;
     */
    public $Postback ;
    public function Submit(){
        $this->Postback = new Postback($this);
        $this->Postback->VIRTUAL_MODE = false ;
        return $this->Postback->Set();
    }

    public function GetBreadcrumb(){
        return '<li><a href="?">Admin</a></li> <li><a href="?tbl='.$this->name.'">' . $this->title . '</a></li> <li class="active">'.( $this->id ? 'Edit #'.$this->id : 'Add') . '</li>' ;
    }

    public function GetListing(){
        return $this->ListingMvc->GetList();
    }

    /**
     * @var Loader
     * @param string $table
     * @return Loader
     */
    public static function &Get($table){
        if (!isset(self::$instances[$table])){
            die('Table is missing in loader instance Loader("'.$table.'"); ' );
        }
        return self::$instances[$table] ;
    }

    public static function Current(){
        $ret = false;
        return (get('tbl')) ? self::Get(get('tbl')) : $ret ;
    }
}
?>