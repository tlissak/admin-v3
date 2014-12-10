<?php
define('DS', DIRECTORY_SEPARATOR);
define('P_BASE', dirname(dirname(__FILE__)).DS);
define('P_ADMIN',P_BASE ) ; // admin version
define('P_SITE',P_BASE .DS . 'glasman.php'); //per site configuration

/*P:PATH allways end with DS*/
define('D_ROOT',$_SERVER["DOCUMENT_ROOT"]) ;
define('P_ROOT',D_ROOT .( substr(D_ROOT, -1) !== '/' ? '/' : '' )) ; 
define('P_CONF', str_replace('\\','/',__FILE__));
define('P_SCRIPT',$_SERVER["SCRIPT_FILENAME"]) ;
define('P_SELF',$_SERVER["SCRIPT_NAME"]) ;
define('P_INC',P_BASE. 'inc'.DS)  ;
define('P_CONTROLS',P_BASE. 'controller'.DS)  ; 
define('P_CLASS',P_BASE. 'class'.DS)  ; 
define('P_CLASS_OVER',P_ADMIN. 'class'.DS)  ; 
define('P_BACKUP',P_BASE. 'backup'.DS)  ;
define('P_PHOTO', P_BASE.'photos'.DS);
if (! is_dir(P_BACKUP)){ 		mkdir(P_BACKUP); }
if (! is_dir(P_PHOTO)){ 		mkdir(P_PHOTO); }

/*U:URL allways ends with / */
define('U_HOST',  (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') );
define('U_SELF',$_SERVER['PHP_SELF']); // used to gate page name
define('U_URI',$_SERVER['REQUEST_URI'] );
define('U_DIRURI',dirname(dirname(str_replace(P_ROOT,'',P_CONF))) );
define('U_ROOT' , U_DIRURI == "." ? "/" : U_DIRURI  .( substr(U_DIRURI, -1) !== '/' ? '/' : '' ) ) ;
define('U_PROTOCOLE', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||  $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" );
define('U_BASE' , U_PROTOCOLE . U_HOST .'/'. (U_ROOT != "/" ? U_ROOT :'') ) ;
define('U_BACKUP' , U_BASE .'backup/' ) ;
define('U_PHOTO' , U_BASE .'photos/' ) ;

define('IP_SERVER',$_SERVER['SERVER_ADDR']) ;
define('IP',$_SERVER['REMOTE_ADDR']) ;
define('DEV_MODE',(IP == '127.0.0.1' || IP == '::1'  || substr(IP_SERVER,0,7) == '192.168' || IP_SERVER == '89.83.9.16'));
define('IP_DEV','88.161.66.141') ;

define('PDO_TYPE','mysql');
if (DEV_MODE){
define('PDO_DB','glasman_x');
define('PDO_DSN','mysql:host=127.0.0.1;port=3306;dbname='.PDO_DB.';charset=utf8');
define('PDO_USER','root');
define('PDO_PASS','metallica');
}else{
define('PDO_DB','glasman_x');
define('PDO_DSN','mysql:host=127.0.0.1;port=103306;dbname='.PDO_DB.';charset=utf8');
define('PDO_USER','root');
define('PDO_PASS','metallica');
}

define('V2_IMG' , false ) ;

define('_CIPHER_ALGORITHM_',true);
define('_BLOWFISH_KEY_', '43S6COfkycXO38GNCHdSMIV5142Ran4gUvC7bj3ukwPo91jgubLJFN91');
define('_BLOWFISH_IV_', 'wk5Irzqb');
define('_RIJNDAEL_KEY_', 'v1VXChCyUPUVEJuVAEyM80zgaYp3Jr9H');
define('_RIJNDAEL_IV_', '7B6YhaP5en6B6lcxD5l3Bg==');
if (!defined('PHP_VERSION_ID')){    $version = explode('.', PHP_VERSION);    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));}

define('_EMAIL_FROM_', 'contact@glasman.fr');
if (DEV_MODE)
	define('_EMAIL_TO_', 'tlissak@gmail.com');
else
	define('_EMAIL_TO_', 'glasman.fr@gmail.com, laurent.glasman@wanadoo.fr,tlissak@gmail.com');
define('_EMAIL_HEADER_', 'from:' . _EMAIL_FROM_ ."\nMIME-version: 1.0\nContent-type: text/html; charset=utf-8\n");
@ini_set('date.timezone','Europe/Paris');
if (DEV_MODE)
	ini_set("sendmail_from", "contact@lissak.fr");
else
	ini_set("sendmail_from", "contact@glasman.fr");


define('LNG','fr') ;
if(DEV_MODE){
	define('ATOS_MERCHANT_ID','011223344551111');
	//define('ATOS_MERCHANT_ID','058205172800019');
}else{
	define('ATOS_MERCHANT_ID','058205172800019');
}
define('ATOS', P_BASE . "atos_payment".DS);
define('ATOS_BIN_REQUEST' , ATOS  . 'bin'.DS.'request.exe');
define('ATOS_BIN_RESPONSE' , ATOS  . 'bin'.DS.'response.exe');
if(DEV_MODE){
	define('ATOS_PATHFILE' , ATOS  . 'param'. DS.'pathfile.'.ATOS_MERCHANT_ID.'.txt');
	//define("ATOS_RETURN", "http://p.lissak.fr/glasman_php/return.php");
    define("ATOS_RETURN", "http://g.lissak.fr/php.glasman.fr/return.php");
}else{
	define('ATOS_PATHFILE' , ATOS  . 'param'. DS.'pathfile.'.ATOS_MERCHANT_ID.'.prod.txt');
	define('ATOS_RETURN','http://www.glasman.fr/return.php') ;
}
define('ATOS_LOG_EMAIL','tlissak@gmail.com,glasman.fr@gmail.com') ;






if (U_ROOT && !isset($_GET['U_ROOT'])){
	if ( stripos(U_URI,U_ROOT) !==  strpos(U_URI,U_ROOT) ){
		header('Location: '. str_ireplace(U_ROOT,U_ROOT,U_URI) . ((strpos(U_URI,'?')>-1) ? '&' : '?') ."U_ROOT=1" . (count($_POST) ? '&LOSS_POST_DATA=1' : '' ));
		die('redirecting....') ;
	}
}

header_remove("X-Powered-By");








ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 2048);














function __autoload($ClassName){	
	if (is_file(P_CLASS.$ClassName.'.php')){
		include P_CLASS .$ClassName.'.php' ;
	}elseif(is_file(P_CLASS_OVER.$ClassName.'.php') ){
		include P_CLASS_OVER .$ClassName.'.php' ;
	}else{
		echo '__autoload('. P_CLASS.$ClassName.').php not found ' .__FILE__ .__LINE__; 	die ;	
	}
}

//$dfns = get_defined_constants(true) ;var_dump($dfns['user']);die ;

?>