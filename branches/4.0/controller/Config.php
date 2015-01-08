<?php

class Config{
    /**
     * @var Db
     */
    public static  $db = null ;
    public static $db_file = 'config.sqlite' ;

    /**
     * @var Auth
     */
    public $auth ;

    public function __construct($login_page = false ){
        if (!self::$db){             self::initDb();        }
        $this->cookie = new Cookie('x_admin_user');
        $this->auth = new Auth($this);

        if ($login_page) {
            if ($this->getLeftAttamps() < 1){
                echo ('Your ip ' . IP . ' is baned try again in '.$this->role_max_attamps_time / 100 . ' min ' );
                die ;
            }
            $this->auth->Login();
            return;
        }

        $this->auth->Logout();

        if (! $this->isAuth()){
            header('Location: login.php?no_auth');
            die ;
        };

        if ($this->cookie->id_user === "0" ){
            $this->LoadConfigAdmin() ;
        }else{
            $this->LoadConfig();
        }
    }

    public function isAuth(){
        if ($this->auth->isAllowedIP()){return true ; }
        if ($this->auth->isValidToken()) {return true ; }
        return ($this->cookie->auth == true) ; // == true can be === "1"
    }

    public function LoadConfigAdmin(){
        Loader('config_ban','ip')->View(array('ip'=>'ip','date_time'=>'date_time'))
            ->FormControl('text','ip','ip')
            ->FormControl('date','date_time','date_time')
            ->FormControl('text','user_pass','user $ pass')
        ->Attr('title',"Config Attamps Ban");
        Loader('config_users','user')->View(array('user'=>'user','valid'=>'valid','title'=>'title'))
            ->FormControl('text','title','title')
            ->FormControl('text','user','user')
            ->FormControl('text','pass','pass')
            ->FormControl('html' ,'<a id="crypt-user-password" href="#" class="btn btn-danger"><i class="icon-invoice"></i> Crypter </a>' ,'Crypter Password')
            ->FormControl('textarea',"ga_key",'GA Key')
            ->FormControl('html','<a href="https://console.developers.google.com/project/ABC/apiui/credential?authuser=0" class="btn btn-danger" target="_blank">Generate</a>',"Generate GA Key")
            ->FormControl('check','valid','valid')
            ->Attr('title',"Config Users");

        Hook::Add('js','<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>');
        Hook::Add('js','<script>$(function(){
                $("#crypt-user-password").click(function(e){ $("#fld_pass").val(CryptoJS.MD5($("#fld_pass").val())); e.preventDefault(); })
            })</script>') ;


        Loader::Load(self::$db);

        $this->LoadConfig();
    }

    public function LoadConfig(){
        include(P_SITE);
        $db =  new Db(); //Info Config file
        Loader::Load($db);
    }

    public $role_max_attamps = 10 ;
    public $role_max_attamps_time = 2000  ; // 20MIN

    public function getLeftAttamps(){
        $row = self::$db->fetchRow('SELECT count(*) AS c FROM config_ban WHERE ip = '. SQL::v2txt(IP) . ' AND date_time > '. (date('YmdHis')  - $this->role_max_attamps_time) ) ;
        return $this->role_max_attamps = $this->role_max_attamps - $row['c'] ;
    }
    public function addAttamp($up){
        self::$db->query('INSERT INTO config_ban (ip,date_time,user_pass) VALUES ('.SQL::v2txt(IP).','.SQL::v2txt(date('YmdHis')) .','. SQL::v2txt( $up ) .');' );
    }
    public function getLoginRow($u,$p){
       return self::$db->fetchRow('SELECT * FROM config_users WHERE user = '.SQL::v2txt($u).' AND pass = '. SQL::v2txt(md5($p)) .' AND valid = 1' ) ;
    }

    public static function initDb(){
        if (!is_file(P_ADMIN . self::$db_file)) {
            $error = null ;
            try {
                $database = new SQLite3(P_ADMIN . self::$db_file);
                $database->exec(
                    'CREATE TABLE "config_ban" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"ip"  TEXT(100),
"date_time"  TEXT(100),
"user_pass"  TEXT(255)
);
') ;
                $database->exec('
CREATE TABLE "config_users" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"user"  TEXT,
"pass"  TEXT,
"valid"  INTEGER,
"ga_key"  TEXT,
"title"  TEXT
);
INSERT INTO "config_users" (id,user,pass,valid,title) VALUES (0,\'foxdanni@gmail.com\',\'3c2234a7ce973bc1700e0c743d6a819c\',1,\'Fox Danni Admin\') ;
') ;
            } catch (Exception $e) {                p($e);  die ;   }
        }
        self::$db = new Db('sqlite:'.P_ADMIN . self::$db_file,'sqlite') ;
    }

}

?>