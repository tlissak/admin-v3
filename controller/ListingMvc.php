<?php

class ListingMvc{
    /**
     * @var Loader
     */
    public $parent;



    public function __construct(Loader &$p){
        $this->parent = &$p;
    }

    public  function GetList(){

        $this->parent->Listing->getList() ;

        if (get('relation')){
            foreach($this->parent->Listing->_list as &$r){
                $r['_id'] = $r['id'] ;
            }
        }

        if (count($this->parent->fileField)  ){
            foreach($this->parent->Listing->_list as &$r){
                foreach($this->parent->fileField as $f){
                    if (in_array($f,array_keys($this->parent->viewFields))) {
                        if ($file = $r[$f]) {
                            if (is_image(P_PHOTO . $file)) {
                                $r[$f] = '<img src="' . U_PHOTO . $file . ',width=100" class="image_preview">';
                            }
                        }
                    }
                }
            }
        }

        $out = array('sql'=>$this->parent->Listing->sql_rows,'total'=> $this->parent->Listing->num_results,"status"=>200 ,'rows'=>$this->parent->Listing->_list);

        return json_encode($out);

    }

    public function GetPanel(){

        return $this->parent->PanelMvc->RenderPanel('listing-'.$this->parent->name,$this->GetHeader(),'mainlist'
            ,$this->parent->title.' list'
            ,'glyphicon glyphicon-list'
            ,'<a  class="pull-right btn add-new" href="?tbl='.$this->parent->name.'" ><i class="icon ion-plus"></i></a>') ;
    }

    public function GetHeader(){
        //p($this->parent); die;
        $fields = array();
        $opts = array('silent' => ''

            , 'select-item-name' => '' // should stay empty or with table prefix to evite while selecting input remove checked in diffrent table
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


        if ($this->parent->tmpRelation) {

            $opts['tbl'] = $this->parent->tmpRelation->name ;
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

        if ($this->parent->tmpRelation) { //TODO : add if table ->readonly
            $fields[] = '<th data-field="operate" class="oprate" data-halign="center" data-align="center" >-</th>';
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