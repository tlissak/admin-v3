<?
class RelationMvc{
    /**
     * @var Loader
     */
    private $parent ;

    public function __construct(&$p)    {
        $this->parent = $p;
    }
    public function GetTabs(){
        $tabs = array();
        foreach($this->parent->relations_instances as $r){
            $tabs[ ] = '<li><a href="#tab-relation-'.$r->name . (get("ajax") == 'form' ? '-ajax' : '').'" data-toggle="tab">' ;
            $tabs[ ] = Loader::Get($r->name)->title ;
            $tabs[ ] = '</a></li>';
        }
        return implode(NL,$tabs) ;
    }

    private function filePreview(Relation $r,&$row) {
        if (count($r->RelatedTable->fileField) && in_array($r->RelatedTable->titleField,$r->RelatedTable->fileField)){
            if (is_image(P_PHOTO . $row['title_field'])){
                $row['title_field'] = '<img src="'.U_PHOTO .$row['title_field'] .'">' ;
            }
        }
    }

    public function GetState(Relation $r,$data){


        global $db ;
        $out = '';
        $titleField = $r->RelatedTable->titleField ;

        $out .= '<div class="state-cont">' ;

        if ( $this->parent->id) {
            if ($r->type == 'Simple' || $r->type == 'InnerSimple') {
                $current_value = isset($r->parent->Form->data[$r->left_key]) ? $r->parent->Form->data[$r->left_key] : 0 ;

                $sql = 'SELECT tbl.`'.$titleField.'` AS title_field FROM  `' . $r->name . '` AS tbl WHERE tbl.id = ' . $current_value;
                //$out .= $this->wrap_input("sql",$sql) ;
                $row = $db->fetchRow($sql);
                if (count($row)) {
                    $this->filePreview($r,$row) ;
                    $out .= $this->wrap_input('<input type="radio" name="' . $r->left_key . '" value="' . $current_value . '" checked > ', $row['title_field']);
                }
            }
            if ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect') {
                $sql = 'SELECT tbl.`'.$titleField.'` AS title_field ,by_tbl.`' . $r->right_key . '` AS right_key ';
                $sql .= ',by_tbl.`' . $r->left_key . '` AS left_key ' ;
                $sql .= ' FROM  `' . $r->name . '` AS tbl,' . $r->by_tbl . ' AS by_tbl ';
                $sql .= ' WHERE tbl.id = by_tbl.`' . $r->left_key . '`';
                $sql .= ' AND  by_tbl.`' . $r->right_key . '` = ' . $data['id'];

               // $out .= $this->wrap_input("sql",$sql) ;

                $results = $db->fetch($sql);
                foreach ($results as $row) {
                    $this->filePreview($r,$row) ;
                    $out .= $this->wrap_input('<input type="checkbox" readonly name="' . $r->left_key . '[]" value="' . $row['left_key'] . '" checked >', $row['title_field'] );
                }
            }

            //TODO important add $r->type == 'ManyToOneByKey'

            if ($r->type == 'ManyToOne'){
                //$sql = 'SELECT tbl.`'.$titleField.'` AS title_field FROM  `' . $r->name . '` AS tbl WHERE tbl.id = ' . $current_value;

                //' WHERE `'. $obj->keys['left_key'] .'` =  ' . $this->id ;
            }
            if ($r->type == 'ManyToOneByKey') {

                $current_value = isset($r->parent->Form->data[$r->right_key]) ? $r->parent->Form->data[$r->right_key] : 0;
                $FLD = strpos($titleField , 'concat_ws') !== false ? $titleField : 'tbl.`' . $titleField . '`';
                $sql = 'SELECT '.$FLD.' AS title_field,id AS left_key FROM `' . $r->by_tbl . '` AS tbl WHERE tbl.`' . $r->left_key . '` = ' . $current_value;

                $results = $db->fetch($sql);
                foreach ($results as $row) {
                    $this->filePreview($r,$row) ;
                    $out .= $this->wrap_input('<input type="checkbox" readonly name="' . $r->left_key . '[]" value="' . $row['left_key'] . '" checked >', $row['title_field'],$r->RelatedTable->readonly);
                }
            }
        }



        $out .= '</div>' ;

        $out .= '<script type="text/template">' ;
        if ($r->type == 'Simple' || $r->type == 'InnerSimple') {
            $out .= $this->wrap_input('<input type="radio" name="{$left_key}" value="{$value}" checked >', '{$title}');
        }elseif ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect') {
            $out .= $this->wrap_input('<input type="checkbox" readonly name="{$left_key}" value="{$value}" checked >', '{$title}');
        }
        $out .= '</script>' ;

        $out = $this->parent->PanelMvc->RenderPanel('state-'.$r->RelatedTable->name,$out,'state',$r->RelatedTable->title . ' state',$r->RelatedTable->icon) ;
        return  $out ;

    }

    public function wrap_input($input,$title){
        return '
        <label>
        <div class="input-group test alert">
            <div class="input-group-addon"><span class="cbr"> '.$input.'<i class="fa fa-check"></i></span></div>
            <div class="input-group-addon input-group-addon-clean"> '.$title.'</div>
            <div class="input-group-addon input-group-addon-clean" >
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" aria-hidden="true">&times;</button>
            </div>
        </div>
        </label>' ;
    }

    public function GetTabsCont(){

        $tabs_cont =array() ;

        foreach($this->parent->relations_instances as $r){


            $cont = $this->GetState($r, $this->parent->Form->data);

            if ($r->type == 'ManyToMany' || $r->type == 'ManyToOneByKey' ) {

                $cont .=  '<div class="panel-relationlist">
                                <div class="table"  data-title-field="'.$r->RelatedTable->titleField.'"
                                data-left-key="'.$r->left_key.'" data-selection-type="CHECKBOX"></div></div>' ;

            }else{
                $r->RelatedTable->tmpRelation = $r ;
                $cont .= $r->RelatedTable->ListingMvc->GetHeader() ;
                $r->RelatedTable->tmpRelation = '' ;
            }

            $tabs_cont[] = '<div class="tab-pane" id="tab-relation-'.$r->name.(get("ajax") == 'form' ? '-ajax' : '').'">' ;
            $tabs_cont[] =  $this->parent->PanelMvc->RenderPanel('listing-'.$r->RelatedTable->name, $cont  ,'relationlist'
                ,$r->RelatedTable->title.' R list ',$r->RelatedTable->icon ,'<a data-toggle="modal" data-target="#modal" class="pull-right btn" data-href="?tbl='.$r->name.'&ajax=form" data-action="add"><i class="icon ion-plus"></i></a>') ;
            $tabs_cont[] = '</div>';

        }
        return implode(NL,$tabs_cont) ;
    }



}

?>