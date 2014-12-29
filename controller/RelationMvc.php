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



    public function GetState(Relation $r,$data,$titleField){


        global $db ;
        $out = '';

        $out .= '<div class="state-cont">' ;

        if ( $this->parent->id) {

            if ($r->type == 'Simple' || $r->type == 'InnerSimple') {

                //p($r->name);

                $current_value = $data[$r->left_key];

                $sql = 'SELECT * FROM  `' . $r->name . '` WHERE id = ' . $current_value;

                //$out .= $this->wrap_input("sql",$sql) ;

                $row = $db->fetchRow($sql);

                if (count($row)) {
                    $out .= $this->wrap_input('<input type="radio" name="' . $r->left_key . '" value="' . $current_value . '" checked > ', $row[$titleField]);
                }
            }

            if ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect') {

                $sql = 'SELECT tbl.*,by_tbl.' . $r->right_key . ' AS right_key ';
                $sql .= ',by_tbl.' . $r->left_key . ' AS left_key ' ;
                $sql .= ' FROM  `' . $r->name . '` AS tbl,' . $r->by_tbl . ' AS by_tbl ';
                $sql .= ' WHERE tbl.id = by_tbl.' . $r->left_key;
                $sql .= ' AND  by_tbl.' . $r->right_key . ' = ' . $data['id'];

               // $out .= $this->wrap_input("sql",$sql) ;

                $results = $db->fetch($sql);
                foreach ($results as $row) {
                    $out .= $this->wrap_input('<input type="checkbox" name="' . $r->left_key . '[]" value="' . $row['left_key'] . '" checked >', $row[$titleField] );
                }
            }
        }

        //TODO : add $r->type == 'ManyToManySelect'
        //TODO : add $r->type == 'ManyToOneByKey'
        $out .= '</div>' ;

        $out .= '<script type="text/template">' ;
        if ($r->type == 'Simple' || $r->type == 'InnerSimple') {
            $out .= $this->wrap_input('<input type="radio" name="{$left_key}" value="{$value}" checked >', '{$title}');
        }elseif ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect') {
            $out .= $this->wrap_input('<input type="checkbox" name="{$left_key}" value="{$value}" checked >', '{$title}');
        }
        $out .= '</script>' ;

        $out = $this->parent->PanelMvc->RenderPanel('state-'.$r->RelatedTable->name,$out,'state',$r->RelatedTable->title . ' state','') ;
        return  $out ;

    }

    public function wrap_input($input,$title){
        return '
        <label>
        <div class="input-group">
            <div class="input-group-addon"><span class="cbr"> '.$input.'<i class="fa fa-check"></i></span></div>
            <div class="input-group-addon input-group-addon-clean"> '.$title.'</div>
        </div>
        </label>' ;
    }

    public function GetTabsCont(){

        $tabs_cont =array() ;

        foreach($this->parent->relations_instances as $r){



            $r->RelatedTable->tmpRelation = $r ;

            $tabs_cont[] = '<div class="tab-pane active" id="tab-relation-'.$r->name.(get("ajax") == 'form' ? '-ajax' : '').'">' ;


            $tabs_cont[] = $this->GetState($r, $this->parent->Form->data, $r->RelatedTable->titleField);
            //p($related_table->name  . '.' . $related_table->titleField );



            //TODO add panel with ajax form
            $tabs_cont[] = '<a data-href="?tbl='.$r->name.'&ajax=form" data-action="add" class="btn btn-danger" data-toggle="modal" data-target="#modal">ADD RELATION</a>' ;



            //set filter if relation is many to many ?
            if ($r->type == 'ManyToMany' && $r->type == 'ManyToOneByKey' ) {

            }else{
                $tabs_cont[] = $this->GetPanel($r->RelatedTable) ;
            }


            $tabs_cont[] = '</div>';

            $r->RelatedTable->tmpRelation = '' ;

        }
        return implode(NL,$tabs_cont) ;
    }

    public function GetPanel(Loader &$r){

        return $this->parent->PanelMvc->RenderPanel('listing-'.$this->parent->name,$r->ListingMvc->GetHeader()
            ,'relationlist',$this->parent->title.' R list','glyphicon glyphicon-list') ;
    }

}

?>