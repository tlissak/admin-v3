<?php


class Relation {
    public $name  ;
    public $data ;

    /**
     * @var array
     */
    public $type  ;
    public $left_key ;
    public $right_key ;
    public $by_tbl ;

    public $view_fields = array();

    public $field;
    public $field_alias;

    public $alias ;

    /**
     * @var Loader
     */
    public $RelatedTable ;

    public $id_html ;

    /**
     * @var String RADIO|CHECKBOX
     */
    public $view_type = 'RADIO' ;

    public function __construct($name,$data,$id){

        $this->name = $name ;
        $this->data = $data ;
        $this->id_html = $name . '_'.$id . (get("ajax") == 'form' ? '-ajax' : '');

        if (!isset($data['type'])){
            p('Relation dosent contain type value : '.$this->name);
        }
        if (!isset($data['left_key'])){
            p('Relation dosent contain left_key value : '.$this->name);
        }

        $this->type = $data['type'] ;
        $this->left_key = $data['left_key'] ;


        if ($this->type == 'Simple' || $this->type == 'InnerSimple') {
            $this->view_type = 'RADIO';
        }
        if ($this->type == 'ManyToMany' || $this->type == 'ManyToOneByKey' || $this->type == 'ManyToManySelect') {
            $this->view_type = 'CHECKBOX';

            if (!isset($data['by_tbl'])){
                p('Relation dosent contain by_tbl value : '.$this->name);
            }
            if (!isset($data['right_key'])){
                p('Relation dosent contain right_key value : '.$this->name);
            }

            $this->right_key    = isset($data['right_key']) ? $data['right_key'] : null ;
            $this->by_tbl       = isset($data['by_tbl']) ? $data['by_tbl'] : null ;
        }
        if ($this->type == 'ManyToOne') {
            p('ManyToOne type not implemented yet');
            p('See RelationMvc GetState and GetTabsCont if need listing ');
            die ;
        }

    }

    public function Load(Loader &$parent)
    {
        $this->parent = &$parent;

        $this->RelatedTable = &Loader::Get($this->name);

        if($this->type == 'Simple' 	|| $this->type == 'InnerSimple' ){

            $this->alias = 'left_join_'.$this->name.'_'.$this->left_key;

            $this->view_field = $this->left_key.'_inner' ;

//            p($this->parent->titleField);
            $this->view_fields[$this->view_field] = $this->alias .'.'.$this->parent->titleField ;

            $this->field = $this->alias .'.'. $this->RelatedTable->titleField  ;
            $this->field_alias = $this->field .' AS '.$this->view_field ;



        }
    }





}

?>