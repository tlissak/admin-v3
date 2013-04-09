<?php
define('DS', DIRECTORY_SEPARATOR);
define('P_BASE', dirname(dirname(__FILE__)).DS);
define('P_ADMIN',P_BASE.'admin_3.0'.DS) ; // admin version
define('P_SITE',P_ADMIN.'site' .DS . 'atrium.php'); //per site configuration

/*P:PATH allways end with DS*/
define('P_ROOT',$_SERVER["DOCUMENT_ROOT"]) ; //shuld be test at sevrel environements
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
define('U_HOST',  (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']) );
define('U_SELF',$_SERVER['PHP_SELF']); // used to gate page name
define('U_URI',$_SERVER['REQUEST_URI'] );
define('U_ROOT' ,  dirname(dirname(str_replace(P_ROOT,'',P_CONF)))) ;// str_replace('.','',);
define('U_BASE' , 'http://' . U_HOST .'/'. (U_ROOT ? U_ROOT .'/' :'') ) ;
define('U_BACKUP' , U_BASE .'backup/' ) ;
define('U_PHOTO' , U_BASE .'photos/' ) ;

define('IP',$_SERVER['REMOTE_ADDR']) ;
define('DEV_MODE',(IP == '127.0.0.1')); 

define('PDO_TYPE','sqlite');
define('PDO_DB',P_BASE.DS.'db'.DS.'atrium.s3db');
define('PDO_DSN','sqlite:'. PDO_DB);

/*define('PDO_TYPE','mysql');
define('PDO_DB','admin_sqlite');
define('PDO_DSN','mysql:host=127.0.0.1;dbname='.PDO_DB.';charset=utf8');
define('PDO_USER','root');
define('PDO_PASS','metallica');*/

define('V2_IMG' , true ) ;

define('_CIPHER_ALGORITHM_',true);
define('_BLOWFISH_KEY_', '43S6COfkycXO38GNCHdSMIV5142Ran4gUvC7bj3ukwPo91jgubLJFN91');
define('_BLOWFISH_IV_', 'wk5Irzqb');
define('_RIJNDAEL_KEY_', 'v1VXChCyUPUVEJuVAEyM80zgaYp3Jr9H');
define('_RIJNDAEL_IV_', '7B6YhaP5en6B6lcxD5l3Bg==');
if (!defined('PHP_VERSION_ID')){    $version = explode('.', PHP_VERSION);    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));}

define('_EMAIL_FROM_', 'contact@weeatkosher.com');
define('_EMAIL_TO_', 'contact@weeatkosher.com');
define('_EMAIL_HEADER_', 'from:' . _EMAIL_FROM_ ."\nMIME-version: 1.0\nContent-type: text/html; charset=utf-8\n");
@ini_set('date.timezone','Europe/Paris');


if (U_ROOT && !isset($_GET['U_ROOT'])){
	if ( stripos(U_URI,U_ROOT) !==  strpos(U_URI,U_ROOT) ){
		header('Location: '. str_ireplace(U_ROOT,U_ROOT,U_URI) . ((strpos(U_URI,'?')>-1) ? '&' : '?') ."U_ROOT=1" . (count($_POST) ? '&LOSS_POST_DATA=1' : '' ));
		die('redirecting....') ;
	}
}

function __autoload($ClassName){	
	if (is_file(P_CLASS_OVER.$ClassName.'.php')){
		include P_CLASS_OVER .$ClassName.'.php' ;
	}elseif(is_file(P_CLASS.$ClassName.'.php') ){
		include P_CLASS .$ClassName.'.php' ;
	}else{
		echo '__autoload('. P_CLASS.$ClassName.').php not found ' .__FILE__ .__LINE__; 	die ;	
	}
}
?>