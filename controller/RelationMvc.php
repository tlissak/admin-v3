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

        //TODO: beutify state selections

        //if (! $data['id']){            return ;        }

        global $db ;
        $out = '';

        if ( $this->parent->id) {

            if ($r->data['type'] == 'Simple' || $r->data['type'] == 'InnerSimple') {

                $current_value = $data[$r->data['left_key']];

                $sql = 'SELECT * FROM  `' . $r->name . '` WHERE id = ' . $current_value;

                $out .= $sql;

                $row = $db->fetchRow($sql);

                if (count($row))
                    $out .= '<input type="radio" name="' . $r->data['left_key'] . '" value="' . $current_value . '" checked > ' . $row[$titleField];


            }

            if ($r->data['type'] == 'ManyToMany') {

                $sql = 'SELECT tbl.*,by_tbl.' . $r->data['right_key'] . ' AS right_key ';
                $sql .= ' FROM  `' . $r->name . '` AS tbl,' . $r->data['by_tbl'] . ' AS by_tbl ';
                $sql .= ' WHERE tbl.id = by_tbl.' . $r->data['left_key'];
                $sql .= ' AND  by_tbl.' . $r->data['right_key'] . ' = ' . $data['id'];

                //$out .= $sql ;
                $results = $db->fetch($sql);
                foreach ($results as $row) {
                    $out .= '<input type="checkbox" name="' . $r->data['left_key'] . '[]" value="' . $row['right_key'] . '" checked >' . $row[$titleField] . ' <br />';
                }
            }
        }
        //TODO : add $r->data['type'] == 'ManyToManySelect'
        //TODO : add $r->data['type'] == 'ManyToOneByKey'

        $out = $this->parent->PanelMvc->RenderPanel('state_'.$r->RelatedTable->name,$out,'state',$r->RelatedTable->title . ' state','') ;
        return  $out ;

    }

    public function GetTabsCont(){

        $tabs_cont =array() ;

        foreach($this->parent->relations_instances as $r){



            $r->RelatedTable->tmpView = $r->view_type ;

            $tabs_cont[] = '<div class="tab-pane" id="tab-relation-'.$r->name.'">' ;


            $tabs_cont[] = $this->GetState($r, $this->parent->Form->data, $r->RelatedTable->titleField);
            //p($related_table->name  . '.' . $related_table->titleField );



            //set filter if relation is many to many

            if ($r->data['type'] != 'ManyToMany' && $r->data['type'] != 'ManyToOneByKey' ) {
                $tabs_cont[] = $r->RelatedTable->ListingMvc->GetPanel();
            }


            $tabs_cont[] = '</div>';

            $r->RelatedTable->tmpView = '' ;

        }
        return implode(NL,$tabs_cont) ;
    }

}

?>