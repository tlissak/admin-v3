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
            $tabs[ ] = '<li><a href="#tab-relation-'.$r->name.'" data-toggle="tab">' ;
            $tabs[ ] = Loader::Get($r->name)->title ;
            $tabs[ ] = '</a></li>';
        }
        return implode(NL,$tabs) ;
    }



    public function GetState(Relation $r,$data,$titleField){


        //if (! $data['id']){            return ;        }

        global $db ;
        $out = '';

        if ( $this->parent->id) {

            if ($r->data['type'] == 'Simple' || $r->data['type'] == 'InnerSimple') {

                //p($r->name);

                $current_value = $data[$r->data['left_key']];

                $sql = 'SELECT * FROM  `' . $r->name . '` WHERE id = ' . $current_value;

                //$out .= $this->wrap_input("sql",$sql) ;

                $row = $db->fetchRow($sql);

                if (count($row)) {
                    $out .= $this->wrap_input('<input type="radio" name="' . $r->data['left_key'] . '" value="' . $current_value . '" checked > ', $row[$titleField]);
                }
            }

            if ($r->data['type'] == 'ManyToMany') {

                $sql = 'SELECT tbl.*,by_tbl.' . $r->data['right_key'] . ' AS right_key ';
                $sql .= ' FROM  `' . $r->name . '` AS tbl,' . $r->data['by_tbl'] . ' AS by_tbl ';
                $sql .= ' WHERE tbl.id = by_tbl.' . $r->data['left_key'];
                $sql .= ' AND  by_tbl.' . $r->data['right_key'] . ' = ' . $data['id'];

               // $out .= $this->wrap_input("sql",$sql) ;

                $results = $db->fetch($sql);
                foreach ($results as $row) {
                    $out .= $this->wrap_input('<input type="checkbox" name="' . $r->data['left_key'] . '[]" value="' . $row['right_key'] . '" checked >', $row[$titleField] );
                }
            }
        }
        //TODO : add $r->data['type'] == 'ManyToManySelect'
        //TODO : add $r->data['type'] == 'ManyToOneByKey'

        $out = $this->parent->PanelMvc->RenderPanel('state-'.$r->RelatedTable->name,$out,'state',$r->RelatedTable->title . ' state','') ;
        return  $out ;

    }

    public function wrap_input($input,$title){
        return '
        <label>
        <div class="input-group">
            <div class="input-group-addon"> '.$input.'</div>
            <div class="input-group-addon input-group-addon-clean"> '.$title.'</div>
        </div>
        </label>' ;
    }

    public function GetTabsCont(){

        $tabs_cont =array() ;

        foreach($this->parent->relations_instances as $r){



            $r->RelatedTable->tmpView = $r->view_type ;

            $tabs_cont[] = '<div class="tab-pane active" id="tab-relation-'.$r->name.'">' ;


            $tabs_cont[] = $this->GetState($r, $this->parent->Form->data, $r->RelatedTable->titleField);
            //p($related_table->name  . '.' . $related_table->titleField );



            //set filter if relation is many to many

            if ($r->data['type'] != 'ManyToMany' && $r->data['type'] != 'ManyToOneByKey' ) {

                $tabs_cont[] = $this->GetPanel($r->RelatedTable) ;
            }


            $tabs_cont[] = '</div>';

            $r->RelatedTable->tmpView = '' ;

        }
        return implode(NL,$tabs_cont) ;
    }

    public function GetPanel(Loader &$r){

        return $this->parent->PanelMvc->RenderPanel('listing-'.$this->parent->name,$r->ListingMvc->GetHeader()
            ,'relationlist',$this->parent->title.' R list','glyphicon glyphicon-list') ;
    }

}

?>