<?php

function Loader($name){  $Loader =  new Loader($name); Loader::$instances[$name] = & $Loader ;  return $Loader ;}

class Loader{
    /**
     * @var Loader[]
     */
    public static $instances = array();

    public $name = '';


    public $formFields = array();
    public $dbFields = array();
    public $viewFields = array();
    public $relationFields = array();

    public $titleField = array();
    //Html();

    public $relation_data = array();
    /**
     * @var Relation[]
     */
    public $relations_instances = array();

    public $loaded = false ;
    private $attr = array();

    public function __construct($name,$title = ''){        $this->name = $name ; if ($title)  $this->titleField = $title ;   }

    /**
     * @param $key
     * @param $value
     *
     * Set Attribute
     */
    public function Attr($key,$value){        $this->attr[$key] = $value ;    }

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

    function AddFormField($type,$name,$title,$opts=array()){
        $field = array('name'=>$name,'title'=>$title,'type'=>$type) ; count($opts) > 0 ?  $filed['opts'] = $opts : null ;
        $this->formFields[] = $field ;
        return $this;
    }
    /**
     * @var Listing
     */
    public $Listing ; //Listing

    /**
     * @var Mvc
     */
    public $Mvc ; //Listing

    public static function Load(){



        global $db;

        foreach(self::$instances as &$loader){
            $loader->loaded = true ;

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
            }

            // if loader dosent have any view fields add the id and the titleField
            if (count($loader->viewFields) == 0)
                $loader->View(array($loader->titleField=>'title'));



        }

        foreach(self::$instances as &$loader) {
            foreach ($loader->relations_instances as &$relation) {
                $relation->Load($loader);
            }
        }

        //relation need to be loaded
        foreach(self::$instances as &$loader) {
            $loader->Listing = new Listing($loader);
            $loader->Mvc = new Mvc($loader);
        }
    }

    /**
     * @var Loader
     * @param string $table
     * @return Loader
     */
    public static  function Get($table){
        if (!isset(self::$instances[$table])){
            die('Table is missing in loader instance Loader("'.$table.'"); ' );
        }
        return self::$instances[$table] ;
    }
}
?>