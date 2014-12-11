<?php

class ListingMvc{
    /**
     * @var Loader
     */
    public $parent;

    public function __construct(&$p){
        $this->parent = $p;
    }

    public function GetPanel(){
        $pnl = array('pull'=>'pull-left-lg' , 'title'=> $this->parent->title ,'icon'=> 'list-alt','cont'=>$this->GetHeader());
        return $this->parent->Mvc->RenderPanel('listing',$pnl) ;
    }

    public function GetHeader(){
        //p($this->parent); die;
        $fields = array();
        $opts = array('silent' => ''
        , 'select-item-name' => 'id'
        , 'sort-name' => $this->parent->sort_name ? $this->parent->sort_name : 'id'
        , 'sort-order' => $this->parent->sort_order ? $this->parent->sort_order : 'ASC'
        , 'striped' => 'true'
        , 'toggle' => "table"
        , 'height' => $this->parent->table_height ? $this->parent->table_height : 560
        , 'page-size'=>$this->parent->table_count ? $this->parent->table_count : 14

        , 'url' => '?ajax=list&tbl=' . $this->parent->name
        , 'page-number'=>1
        , 'cache' => 'false'
        , 'classes' => 'table table-condensed'
        , 'page-list' => '[5, 10, 20, 50, 100, 200]'
        , 'pagination' => 'true'
            ,'search-align'=>'center'
            ,'toolbar-align'=>'center'
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