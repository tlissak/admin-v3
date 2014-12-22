<?php


class Relation {
    public $name  ;
    public $data ;

    /**
     * @var array
     */
    public $view_fields = array();

    public $field;
    public $field_alias;

    public $alias ;

    /**
     * @var Loader
     */
    public $RelatedTable ;


    /**
     * @var String RADIO|CHECKBOX
     */
    public $view_type = 'RADIO' ;

    public function __construct($name,$data){
        $this->name = $name ;
        $this->data = $data ;

        if ($this->data['type'] == 'Simple' || $this->data['type'] == 'InnerSimple') {
            $this->view_type = 'RADIO';
        }
        if ($this->data['type'] == 'ManyToMany' || $this->data['type'] == 'ManyToOneByKey' || $this->data['type'] == 'ManyToManySelect') {
            $this->view_type = 'CHECKBOX';
        }
    }

    public function Load(Loader &$parent)
    {
        $this->parent = $parent;

        $this->RelatedTable = &Loader::Get($this->name);

        if($this->data['type'] == 'Simple' 	|| $this->data['type'] == 'InnerSimple' ){

            $this->alias = 'left_join_'.$this->name.'_'.$this->data['left_key'];

            $this->view_field = $this->data['left_key'].'_inner' ;

//            p($this->parent->titleField);
            $this->view_fields[$this->view_field] = $this->alias .'.'.$this->parent->titleField ;

            $this->field = $this->alias .'.'. $this->RelatedTable->titleField  ;
            $this->field_alias = $this->field .' AS '.$this->view_field ;



        }
    }





}

?>