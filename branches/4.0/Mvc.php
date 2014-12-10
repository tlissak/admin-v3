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
        //

        $out =  '<table
                data-click-to-select="true"
               data-cache="false"
               data-classes="table table-condensed"
               data-height="500"
               data-page-list="[5, 10, 20, 50, 100, 200]"
               data-pagination="true"
               data-search="true"
               data-select-item-name="id"
               data-show-columns="true"
               data-show-refresh="true"
               data-show-toggle="true"
               data-show_export="true"
               data-side-pagination="server"
               data-silent=""
               data-sort-name="id"
               data-sort-order="desc"
               data-striped="true"
               data-toggle="table"
               data-url="?ajax=list&tbl='.$this->parent->name.'"
                ><thead><tr>'."\r\n";

        $this->parent->view_type = '-' ;

        if ($this->parent->view_type == 'CHECKBOX' )
            $out .= '<th data-field="id"  data-visible="true" data-checkbox="true">-</th>' ."\r\n";
        elseif ($this->parent->view_type == 'RADIO' )
            $out .= '<th data-field="id"  data-visible="true" data-radio="true">-</th>' ."\r\n";
        //else $out .= '<th data-field="id"  data-visible="false" >#</th>' ."\r\n";

        foreach( $this->parent->viewFields as $key=>$title) {
            $out .= '<th data-field="'.$key.'" data-sortable="true" '. ($key == 'id' ? ' data-visible="false" ' : '' ).' >' . $title .'</th>' ."\r\n";


        }
        $out .=  '</tr></thead></table>' ;
        return $out ;
    }


    public function GetJsonBody(){
        $this->parent->Listing->getList();
        $out = array('sql'=>$this->parent->Listing->sql_rows,'total'=> $this->parent->Listing->num_results,"status"=>200 ,'rows'=>$this->parent->Listing->_list);
        //header('Content-type: application/json');
        echo json_encode($out);
        die ;
    }
}

?>