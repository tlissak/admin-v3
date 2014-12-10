<?php

class Listing{


    public $sql_count = '' ;
    public $sql_rows = '' ;

    public $_list = array();
    public $num_results = array();

    private $sql = array('left_joins'=>'','inner_joins'=>'','search'=>'');
    /**
     * @var Loader
     */
    public $parent ;

    private $selected_db_fileds = array();

    function __construct(Loader &$obj){

        $this->parent = $obj;


    }
    public function initSql(){


        foreach($this->parent->viewFields as $k=>$t ){
            if (in_array($k,$this->parent->dbFields)){
                $this->selected_db_fileds[] = '`'.$this->parent->name .'`.'.$k ;
            }
        }
        $this->sql['fields'] 			= implode("\r\n".',',$this->selected_db_fileds )."\r\n" ;
        //$this->sql_fields 			= '`'.$obj->name .'`.* ';
        $this->sql['tables'] 			= ' `'.$this->parent->name.'` ' ;

        /* RELATION */


        foreach($this->parent->relations_instances as &$rel){
            if($rel->data['type'] == 'Simple' 	|| $rel->data['type'] == 'InnerSimple' ){
                $this->sql['fields']        .= ','. $rel->field_alias;

                $this->sql['left_joins']    .= ' LEFT JOIN `'. $rel->name . '` AS '.$rel->alias .' '."\r\n"  ;
                $this->sql['left_joins'] .= ' ON '.$rel->alias.'.id  = `'. $this->parent->name.'`.'.  $rel->data['left_key']  .' '."\r\n"  ;
            }
        }



        /* FILTER */
        if (get('search')) {
            $this->sql['search'] = ' WHERE (`' . $this->parent->name . '`.id LIKE ' . SQL::v2txt( intval(get('search')) . '%') ."\r\n";



            foreach ($this->selected_db_fileds  as $fld) {
                $this->sql['search'] .= ' OR '.$fld. ' LIKE ' . SQL::v2txt(get('search') . '%')."\r\n";
            }


            foreach($this->parent->relations_instances as &$rel) {
                $this->sql['search'] .= ' OR ' . $rel->field . ' LIKE ' . SQL::v2txt(get('search') . '%') ."\r\n";
            }

            $this->sql['search'] .= ') ';
        }



        /* SORT */
        if (get('sort') &&  get('order')) {
            $this->sql['order']  = ' ORDER BY ' . get('sort') . ' ' . get('order');
        }

        /* PAGING */
        if (get('offset') !='' && get('limit') !='') {
            $this->sql['limit'] = ' LIMIT ' . get('offset') . ',' . get('limit') ;
        }

        $this->sql_count = 'SELECT COUNT(*) AS cn ' ."\r\n"
            //. $this->sql['extra_fields']  ."\r\n"
            . ' FROM '. $this->sql['tables']  ."\r\n"
            . $this->sql['left_joins'] ."\r\n"
            . $this->sql['inner_joins'] ."\r\n"
            . $this->sql['search'] ."\r\n" ;

        $this->sql_rows=  'SELECT ' . $this->sql['fields'] ."\r\n"
            //. $this->sql['extra_fields']  ."\r\n"
            . ' FROM '. $this->sql['tables']  ."\r\n"
            . $this->sql['left_joins'] ."\r\n"
            . $this->sql['inner_joins'] ."\r\n"
            . $this->sql['search'] ."\r\n"
            . $this->sql['order']  ."\r\n"
            . $this->sql['limit']  ."\r\n" ;

    }
    public function getList(){

        global $db;
        //p(array_keys($this->parent->relations_instances));
        //p($this->sql);
        //return ;

        $this->initSql();

        $list_count = $db->fetchRow($this->sql_count);
        $this->num_results = (int)($list_count["cn"]) ;
        $this->_list = $db->fetch(  $this->sql_rows ) ;

        //foreach ($this->_list as $l) {   //$oh[] = $this->v4_getTableRow($l);  }
    }
}

?>