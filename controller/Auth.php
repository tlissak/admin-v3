<?php

class Auth{

    public $ALLOWED_IPS = array();
    //public $USERS = array();

    public $db_ban ;
    public $role = array('attamps'=>10,'time'=>2000) ;
    public $attamps_left = 0 ;
    public $cb = false ;
    private $cookie  ;

    public function __construct(){
        $this->cookie = new Cookie('x_admin_user');
    }

    public function isAuth(){
        $this->Logout();
        if ($this->isAllowedIP()){return true ; }
        if ($this->isValidToken()) {return true ; }
        if ($this->cookie->auth == true){ // == true can be == 1
            return true ;
        }
        header('Location: login.php?no_auth');
        die ;
    }

	public function isUserAuth($username,$password){
        global $USERS ;

		foreach($USERS as $user=>$pass){
			if ($username == $user && $password == $pass){	return true ;		}
		}
		return false ;
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

    public function Logout(){
        if (get('logout') == '1'){
            $this->cookie->auth = false ;
            $this->cookie->write();
            header('Location: login.php?is_logout=1');
            die;
        }
    }

    public function Login(){

        $this->db_ban = new Db('sqlite:'.P_ADMIN . 'ban.sqlite','sqlite') ;

        if (post("postback") == "login" &&  post('auth_user') && post("auth_pass") ) {

            $sql = 'SELECT count(*) AS c FROM ban WHERE ip = '. SQL::v2txt(IP)
                . ' AND date_time > '. (date('YmdHis')  - $this->role['time'])  ;
            $row  = $this->db_ban->fetchRow($sql  ) ;

            $this->cb = true ;

            $this->attamps_left = $this->role['attamps'] - $row['c'] ;

            if ($row['c'] >= $this->role['attamps']){
                echo ('Your ip ' . IP . ' is baned try again in '.$this->role['time'] / 100 . ' min ' );
                die ;
            }
            if ($this->isUserAuth(post('auth_user') , post("auth_pass") )  ){
                //$this->cookie->auth_user = post('auth_user') .'-' . time() .'-' .$this->USERS[post('auth_user')]  ;
                $this->cookie->auth = true ;
                $this->cookie->write();
                header('Location: index.php');
            }else{
                $this->db_ban->query('INSERT INTO ban (ip,date_time,user_pass)
              VALUES ('.SQL::v2txt(IP).','.SQL::v2txt(date('YmdHis'))
                    .','. SQL::v2txt(post('auth_user') .'$'. post("auth_pass") ) .');'  );
            }
        }
    }
}


?>