<?php

class Form{
    /**
     * @var Loader
     */
    private $parent ;

    public $id ;
    public $data = array() ;
    public $data_posted = array() ;

    function initData(){
        foreach($this->parent->dbFields as $k){
            $this->data[$k]	 = '';
        }
    }
    function initDbData(){
        global $db ;
        $sql = 'SELECT ' . implode(NL.',',$this->parent->dbFields) . ' FROM ' . $this->parent->name . ' WHERE `'.$this->name.'`.id = '.$this->id  ;
        $res = $db->fetchRow($sql);
        if(count($res)){
            $this->data = $res;
        }else{
            $this->id = 0;
        }
    }
    function initPostData(){
        foreach($this->parent->dbFields as $k){
            if (isset($_POST[$k])) {
                $this->data_posted[$k]	 = post($k);
            }
        }
    }

    public function __construct(Loader &$p){
        $this->parent = $p ;
    }



}

?>