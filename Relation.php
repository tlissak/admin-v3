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

    public function __construct($name,$data){
        $this->name = $name ;
        $this->data = $data ;
    }

    public function Load(Loader &$parent){
        $this->parent = $parent ;

        $related_table = Loader::Get($this->name) ;


        if($this->data['type'] == 'Simple' 	|| $this->data['type'] == 'InnerSimple' ){

            $this->alias = 'left_join_'.$this->name.'_'.$this->data['left_key'];

            $this->view_field = $this->data['left_key'].'_inner' ;

            $this->view_fields[$this->view_field] = $this->alias .'.'.$this->parent->titleField ;

            $this->field = $this->alias .'.'. $related_table->titleField  ;
            $this->field_alias = $this->field .' AS '.$this->view_field ;



        }
    }



}

?>