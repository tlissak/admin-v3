<?php

class Config{

    public static $version = '3.4.2' ;

    private static $IV = 'IvCrypt34' ;
    /**
     * @var Db
     */
    public static $db = null ;
    public static $db_file = 'config.sqlite' ;

    /**
     * @var Db
     */
    public static $db_loader = null ;

    /**
     * @var Cookie
     */
    public static $cookie ;

    public $role_max_attamps = 5 ;
    public $role_max_attamps_time = 2000  ; // 20MIN

    public $show_login_message = false ;

    public $done_process = array();

    public function __construct($login_page = false ){
		if (!defined('SETTINGS')) die ("No settings loaded");
        if (!self::$db){             self::initDb();        }
        if (!self::$cookie){         self::$cookie =  new Cookie('admin_auth');       }
        $this->done_process[] = 'cookie and db loaded';
        //$this->auth = new Auth($this);

        if ($login_page) {
            if ($this->getLeftAttamps() < 1){
                self::Log(3,'BAN IP '.IP);
                echo ('Your ip ' . IP . ' is baned try again in '.$this->role_max_attamps_time / 100 . ' min ' );
                die ;
            }
            $this->Login();
            return;
        }

        $this->Logout();

        if (! $this->isAuth()){
            header('Location: login.php?no_auth');
            die ('no auth');
        };

        foreach (glob (P_ADMIN . 'module' . DS . '*.php')  as $module ){
            include($module) ;
        }
        $this->done_process[] = 'Modules loaded !' ;

        if (self::$cookie->id_user === "0" ){
            $this->LoadConfigAdmin() ;
        }else{
            $this->LoadConfig();
        }
        if (get('__debug'))
            p($this) ;
    }

    public function Logout(){
        if (get('logout') == '1'){
            self::Log(2,'LOGOUT');
            self::$cookie->user_title = null ;
            self::$cookie->auth = null ;
            self::$cookie->ga_key = null;
            self::$cookie->id_user = null ;
            self::$cookie->write();
            header('Location: login.php?is_logout=1');
            die;
        }
    }

    public function Login(){
        if (post("postback") == "login" &&  post('auth_user') && post("auth_pass") ) {
            $this->show_login_message = true ;
            $row = $this->getLoginRow(post('auth_user'),post("auth_pass"));
            if (count($row)>0){
                self::Log(2,'LOGIN');
                self::$cookie->user_title = $row['title'] ;
                self::$cookie->auth = true ;
                self::$cookie->ga_key = $row['ga_key'];
                self::$cookie->id_user = $row['id'] ;
                self::$cookie->write();
                header('Location: index.php?login=ok');
            }else{
                $this->addAttamp(post('auth_user') .'$'. post("auth_pass") );
            }
        }
    }

    public function GetGoogleAnalyticsKEY(){
        return self::$cookie->ga_key ;
    }

    public function isAuth(){
        //if ($this->auth->isAllowedIP()){return true ; }
        //if ($this->auth->isValidToken()) {return true ; }
        return (self::$cookie->auth == true) ; // == true can be === "1"
    }

    public function LoadConfigAdmin(){
        $this->done_process[] = 'before Admin Config load' ;
        Loader('config_users','user')->View(array('user'=>'user','valid'=>'valid','title'=>'title'))
            ->FormControl('text','title','title')
            ->FormControl('text','user','user')
            ->FormControl('text','pass','pass')
            ->FormControl("hidden","IV","IV",array('value'=>self::$IV))
            ->FormControl('html' ,'<a id="crypt-user-password" href="#" class="btn btn-danger"><i class="icon-invoice"></i> Crypter </a>' ,'Crypter Password')
            ->FormControl('textarea',"ga_key",'GA Key')
            ->FormControl('html','<a href="https://console.developers.google.com/project/ABC/apiui/credential?authuser=0" class="btn btn-danger" target="_blank">Generate</a>',"Generate GA Key")
            ->FormControl('check','valid','valid')
            ->Attr('icon','glyphicon glyphicon-cog')
            ->Attr('title',"Config Users");

        Hook::Add('js','<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>');
        Hook::Add('js','<script>$(function(){
                $("#crypt-user-password").click(function(e){
                    if (!$("#fld_pass").val()) {alert("password empty"); return; }
                    $("#fld_pass").val(CryptoJS.MD5($("#hdn_fld_IV").val() + $("#fld_pass").val()));
                    e.preventDefault();
                })
            })</script>') ;


        Loader('config_ban','ip')->View(array('ip'=>'ip','date_time'=>'date_time'))
            ->FormControl('text','ip','ip')
            ->FormControl('date','date_time','date_time')
            ->FormControl('text','user_pass','user $ pass')
            ->Attr('icon','glyphicon glyphicon-cog')
            ->Attr('title',"Config Attamps Ban");


        Loader('config_log','date_time')->View(array('date_time'=>'date_time','priority'=>'priority','id_config_users_inner'=>'id_config_user','event'=>'event'))
            ->Relation('config_users',array('type'=>'Simple','tbl'=>'config_users','left_key'=>'id_config_users'))
            ->FormControl('date','date_time','date_time',array("extends"=>' data-format="yyyy-MM-dd hh:mm:ss" '))
            ->FormControl('textarea',"event",'event')
            ->Attr('icon','glyphicon glyphicon-cog')
            ->Attr('title',"Config Log")
            ->Attr('sort_name','date_time')
            ->Attr('sort_order','DESC')
            ->Attr('protected',1);
         Loader::Load(self::$db);
        $this->done_process[] = 'Admin Config loaded' ;
        $this->LoadConfig();
    }

    public function LoadConfig(){
        if (get('__debug'))           p(P_SITE) ;
        $this->done_process[] = 'Before site config file load..' ;
        include(P_SITE);
        if (get('__debug'))           p($this) ;
        $this->done_process[] = 'After site config file loaded !';
        self::$db_loader =  new Db(PDO_DSN , PDO_TYPE , defined('PDO_USER') ? PDO_USER :'' ,defined('PDO_PASS') ? PDO_PASS : '');
        $this->done_process[] = 'site db loaded' ;
        Loader::Load(self::$db_loader);
        $this->done_process[] = 'After config load' ;
        if (get('__debug'))
            p($this) ;
    }



    public function getLeftAttamps(){
        $row = self::$db->fetchRow('SELECT count(*) AS c FROM config_ban WHERE ip = '. Db::v2txt(IP) . ' AND date_time > '. (date('YmdHis')  - $this->role_max_attamps_time) ) ;
        return $this->role_max_attamps = $this->role_max_attamps - $row['c'] ;
    }
    public function addAttamp($up){
        self::$db->query('INSERT INTO config_ban (ip,date_time,user_pass) VALUES ('.Db::v2txt(IP).','.Db::v2txt(date('YmdHis')) .','. Db::v2txt( $up ) .');' );
    }
    public function getLoginRow($u,$p){
       return self::$db->fetchRow('SELECT * FROM config_users WHERE user = '.Db::v2txt($u).' AND pass = '. Db::v2txt(md5(self::$IV.$p)) .' AND valid = 1' ) ;
    }

    public static function Log($priority,$event){
        if (!self::$cookie){         self::$cookie =  new Cookie('admin_auth');       }
        $sql = self::$db->build('INSERT','config_log',array('date_time'=>date('Y-m-d H:i:s'),'id_config_users'=>self::$cookie->id_user,'priority'=>$priority,'event'=>$event)) ;
        self::$db->query($sql) ;
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
INSERT INTO "config_users" (id,user,pass,valid,title) VALUES (0,\'foxdanni@gmail.com\',\'ba574e2410d58bfe074f5f83291770e3\',1,\'Fox Danni Admin\') ;
') ;
                $database->exec(
'CREATE TABLE "config_log" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"id_config_users"  INTEGER,
"date_time"  TEXT(100),
"event"  TEXT,
"priority" INTEGER
);
') ;
            } catch (Exception $e) {                p($e);  die ;   }
        }
        self::$db = new Db('sqlite:'.P_ADMIN . self::$db_file,'sqlite') ;
    }

}

?>