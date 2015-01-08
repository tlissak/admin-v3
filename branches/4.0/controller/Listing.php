<?php

class Listing{
    /**
     * @var Loader
     */
    public $parent ;

    /**
     * @var Db
     */
    private $db ;

    public $sql_count = '' ;
    public $sql_rows = '' ;
    public $_list = array();
    public $num_results = 0;

    private $sql = array('left_joins'=>'','inner_joins'=>'','search'=>'');

    private $selected_db_fileds = array();

    function __construct(Loader &$p){
        $this->parent   = &$p;
        $this->db       = &$p->db ;
    }

    public function getList() {
        $this->initSql();
        $list_count = $this->db->fetchRow($this->sql_count);
        $this->num_results = (int)($list_count["cn"]);
        $this->_list = $this->db->fetch($this->sql_rows);
    }
    
    public function initSql(){

        foreach($this->parent->viewFields as $k=>$t ){
            if (in_array($k,$this->parent->dbFields)){
                $this->selected_db_fileds[] = '`'.$this->parent->name .'`.'.$k ;
            }
        }

        $this->sql['fields'] 			= implode(NL .',',$this->selected_db_fileds ).NL;
        $this->sql['tables'] 			= ' `'.$this->parent->name.'` ' ;

        /* RELATION */
        foreach($this->parent->relations_instances as &$rel){
            if($rel->type == 'Simple' 	|| $rel->type == 'InnerSimple' ){
                $this->sql['fields']        .= ','. $rel->field_alias . NL;
                $this->sql['left_joins']    .= ' LEFT JOIN `'. $rel->name . '` AS '.$rel->alias .' '.NL;
                $this->sql['left_joins'] .= ' ON '.$rel->alias.'.id  = `'. $this->parent->name.'`.'.  $rel->left_key  .' '.NL;
            }
        }

        /* FILTER */
        if (get('search')) {
            $this->sql['search'] = ' WHERE (`' . $this->parent->name . '`.id LIKE ' . SQL::v2txt( intval(get('search')) . '%') .NL;
            foreach ($this->selected_db_fileds  as $fld) {
                $this->sql['search'] .= ' OR '.$fld. ' LIKE ' . SQL::v2txt('%' . get('search') . '%').NL;
            }
            foreach($this->parent->relations_instances as &$rel) {
                $this->sql['search'] .= ' OR ' . $rel->field . ' LIKE ' . SQL::v2txt( get('search') . '%') .NL;
            }
            $this->sql['search'] .= ') ';
        }

        /* SORT */
        if (get('sort') &&  get('order')) {
            $this->sql['order']  = ' ORDER BY ' . get('sort') . ' ' . get('order'); //`'.$this->parent->name.'`.
        }

        /* PAGING */
        if (get('offset') !='' && get('limit') !='') {
            $this->sql['limit'] = ' LIMIT ' . get('offset') . ',' . get('limit') ;
        }

        $this->sql_count = 'SELECT COUNT(*) AS cn ' .NL             //. $this->sql['extra_fields']  .NL
            . ' FROM '. $this->sql['tables']  .NL
            . $this->sql['left_joins'] .NL
            . $this->sql['inner_joins'] .NL
            . $this->sql['search'] .NL ;

        $this->sql_rows=  'SELECT ' . $this->sql['fields'] .NL            //. $this->sql['extra_fields']  .NL
            . ' FROM '. $this->sql['tables']  .NL
            . $this->sql['left_joins'] .NL
            . $this->sql['inner_joins'] .NL
            . $this->sql['search'] .NL
            . $this->sql['order']  .NL
            . $this->sql['limit']  .NL ;

    }
}

?>