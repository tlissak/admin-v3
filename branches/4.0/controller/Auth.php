<?php

/**
 * Class Auth
 * @depracted
 */
class Auth{

    public $ALLOWED_IPS = array();
    //public $USERS = array();

    /**
     * @var Config
     */
    public $config ;
    public $cb = false ;

    public function __construct(&$conf){
        $this->config = $conf ;
    }



	function generate_token(){ 	return hash('sha256',_RIJNDAEL_KEY_ .P_SCRIPT);	}
	function validate_token($_token){	return ( strcmp($_token, $this->generate_token())==0)  ;	}

    public function isValidToken(){
        if (get('_token')){
            if ($this->validate_token(get('_token'))){
                return true ;
            }
        }
    }

    public function isAllowedIP(){
        if ( count($this->ALLOWED_IPS) ) {
            foreach($this->ALLOWED_IPS as $ip){
                if (strpos($ip,'-') > 0){
                    list($begin, $end) = explode('-', $ip);
                    $begin = ip2long($begin); $end = ip2long($end); $_ip = ip2long(IP);
                    if ($_ip >= $begin && $_ip <= $end){
                        return true ;
                    }
                }else{
                    if ($ip == IP){
                        return true ;
                    }
                }
            }
        }
    }
}


?>