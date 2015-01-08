<?php

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

    public function Logout(){
        if (get('logout') == '1'){
            $this->config->cookie->user_title = null ;
            $this->config->cookie->auth = null ;
            $this->config->cookie->ga_key = null;
            $this->config->cookie->id_user = null ;
            $this->config->cookie->write();
            header('Location: login.php?is_logout=1');
            die;
        }
    }

    public function Login(){
        if (post("postback") == "login" &&  post('auth_user') && post("auth_pass") ) {
            $this->cb = true ;
            $row = $this->config->getLoginRow(post('auth_user'),post("auth_pass"));
            if (count($row)>0){
                $this->config->cookie->user_title = $row['title'] ;
                $this->config->cookie->auth = true ;
                $this->config->cookie->ga_key = $row['ga_key'];
                $this->config->cookie->id_user = $row['id'] ;
                $this->config->cookie->write();
                header('Location: index.php?login=ok');
            }else{
                $this->config->addAttamp(post('auth_user') .'$'. post("auth_pass") );
            }
        }
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