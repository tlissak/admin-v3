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

    public $post ;

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
        $this->post = is_array($this->post) ?  $this->post  : $this->parse_post_input( file_get_contents("php://input") ) ;
        foreach($this->parent->dbFields as $k){
            if (isset($this->post[$k])) {
                $this->data_posted[$k]	 = $this->post[$k];
            }
        }
    }

    public function post($k){
        $this->post = is_array($this->post) ?  $this->post  : $this->parse_post_input( file_get_contents("php://input") ) ;
        if (isset($this->post[$k])) { return $this->post[$k] ; }
        return "" ;
    }

    function parse_post_input($str) {
        if ($str === '') return array();
        $arr = array();    $pairs = explode('&', $str);
        foreach ($pairs as $i) {
            list($name,$value) = explode('=', $i, 2);
            $name = urldecode($name);
            $value = urldecode($value) ;
            if( isset($arr[$name]) ) {
                if( is_array($arr[$name]) ) {
                    $arr[$name][] = $value ;
                }else {
                    $arr[$name] = array($arr[$name], $value);
                }
            }else {
                if (strpos(strrev($name), strrev('[]')) === 0){
                    $arr[$name] = array($value);
                }else {
                    $arr[$name] = $value;
                }
            }
        }
        return $arr;
    }


}

?>