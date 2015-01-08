<?php

class Form{
    /**
     * @var Loader
     */
    private $parent ;

    /**
     * @var Db
     */
    private $db ;

    public function __construct(&$p)    {
        $this->parent   = $p;
        $this->db       = &$p->db ;
    }

    public $data = array() ;
    public $data_posted = array() ;

    function initData(){
        foreach($this->parent->dbFields as $k){
            $this->data[$k]	 = '';
        }
    }
    function initDbData(){
        if (! $this->parent->id )
            return ;
        $sql = 'SELECT ' . implode(NL.',',$this->parent->dbFields) . ' FROM `' . $this->parent->name . '` WHERE `'.$this->parent->name.'`.id = '.$this->parent->id  ;
        $res = $this->db->fetchRow($sql);
        if(count($res)){
            $this->data = $res;
        }else{
            $this->parent->id = 0;
        }
    }
    function initPostData(){
        foreach($this->parent->dbFields as $k){
            if (isset($_POST[$k])) {
                $this->data_posted[$k]	 = post($k);
            }
        }
    }



}

?>