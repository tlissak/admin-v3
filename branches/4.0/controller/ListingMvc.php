<?php

class ListingMvc{
    /**
     * @var Loader
     */
    public $parent;



    public function __construct(&$p){
        $this->parent = $p;
    }

    public  function GetList(){

        $this->parent->Listing->getList() ;

        if (get('relation')){
            foreach($this->parent->Listing->_list as &$r){
                $r['_id'] = $r['id'] ;
            }
        }

        $out = array('sql'=>$this->parent->Listing->sql_rows,'total'=> $this->parent->Listing->num_results,"status"=>200 ,'rows'=>$this->parent->Listing->_list);

        return json_encode($out);

    }

    public function GetPanel(){

        return $this->parent->PanelMvc->RenderPanel('listing-'.$this->parent->name,$this->GetHeader(),'mainlist',$this->parent->title.' list','glyphicon glyphicon-list') ;
    }

    public function GetHeader(){
        //p($this->parent); die;
        $fields = array();
        $opts = array('silent' => ''

            , 'select-item-name' => '_id'
            , 'id-field'=>"_id"

            , 'title-field' => $this->parent->titleField
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

            // ,'click-to-select'=>'true'
        );

        // data-align="center" data-formatter="actionFormatter" data-events="actionEvents"
        //$this->parent->view_type = '-' ;

        if ($this->parent->tmpRelation) {

            $opts['selection-type'] = $this->parent->tmpRelation->view_type ;
            $opts['title-field'] = $this->parent->titleField ;
            $opts['left-key'] = $this->parent->tmpRelation->left_key ;

            if ($this->parent->tmpRelation->view_type == 'CHECKBOX') {
                $fields[] = '<th data-field="_id"  data-visible="true" data-checkbox="true">-</th>';
                $opts['click-to-select'] = "true";
                $opts['url'] .= '&relation=1';
            } elseif ($this->parent->tmpRelation->view_type == 'RADIO') {
                $fields[] = '<th data-field="_id"  data-visible="true" data-radio="true">-</th>';
                $opts['click-to-select'] = "true";
                $opts['url'] .= '&relation=1';
            }
        }

        foreach( $this->parent->viewFields as $key=>$title) {
            $fields[] = '<th data-field="'.$key.'" data-sortable="true" ' ;
            $fields[] = ($key == 'id' ? ' data-visible="false" ' : '' ) ;
            $fields[] =  ' >' . $title .'</th>' ;
        }

        $out =  '<table class="table" ' ;

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