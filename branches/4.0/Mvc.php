<?php

class Mvc{
    /**
     * @var Loader
     */
    public $parent;

    public function __construct(&$p){
        $this->parent = $p;
    }

    public function GetHeader(){
        //p($this->parent); die;
        $fields = array();
        $opts = array('silent' => ''
        , 'select-item-name' => 'id'
        , 'sort-name' => 'id'
        , 'sort-order' => 'desc'
        , 'striped' => 'true'
        , 'toggle' => "table"
        , 'height' => 500
        , 'url' => '?ajax=list&tbl=' . $this->parent->name

        , 'cache' => 'false'
        , 'classes' => 'table table-condensed'
        , 'page-list' => '[5, 10, 20, 50, 100, 200]'
        , 'pagination' => 'true'
        , 'search' => 'true'
        , 'show-columns' => 'true'
        , 'show-refresh' => 'true'
        , 'show-toggle' => 'true'
        , 'show_export' => 'true'
        , 'side-pagination' => 'server'
        );

        $this->parent->view_type = '-' ;


        if ($this->parent->view_type == 'CHECKBOX' ) {
            $fields[] = '<th data-field="id"  data-visible="true" data-checkbox="true">-</th>';
            $opts['click-to-select'] ="true" ;
        }elseif ($this->parent->view_type == 'RADIO' ) {
            $fields[] = '<th data-field="id"  data-visible="true" data-radio="true">-</th>' ;
            $opts['click-to-select'] ="true" ;
        }

        foreach( $this->parent->viewFields as $key=>$title) {
            $fields[] = '<th data-field="'.$key.'" data-sortable="true" '. ($key == 'id' ? ' data-visible="false" ' : '' ).' >' . $title .'</th>' ;
        }

        $out =  '<table ' ;
        foreach ($opts as $k =>$v){
            $out .= ' data-'.$k . '="'.$v.'"'.NL;
        }
        $out .= ' ><thead><tr>'.NL;

        foreach ($fields as $f){
            $out .= $f . NL;
        }
        $out .=  '</tr></thead></table>' ;
        return $out ;
    }



}

?>