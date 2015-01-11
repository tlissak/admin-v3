<?php
define('P_ADMIN',P_BASE ) ;
define('P_SITE',P_BASE .DS . 'Config.php'); //per site configuration
define('PDO_TYPE','mysql');
define('PDO_DB','glasman_x');
define('PDO_USER','root');
define('PDO_PASS','metallica');
define('DEV_MODE',(IP == '127.0.0.1' || IP == '::1'  || substr(IP_SERVER,0,7) == '192.168' || IP_SERVER == '89.83.9.16'));
define('IP_DEV','88.161.66.141') ;
define('ATOS', P_BASE . "atos_payment".DS);
define('ATOS_BIN_REQUEST' , ATOS  . 'bin'.DS.'request.exe');
define('ATOS_BIN_RESPONSE' , ATOS  . 'bin'.DS.'response.exe');
define('ATOS_LOG_EMAIL','tlissak@gmail.com,glasman.fr@gmail.com') ;
if (DEV_MODE){
	define('PDO_DSN','mysql:host=127.0.0.1;port=3306;dbname='.PDO_DB.';charset=utf8');
	define('_EMAIL_TO_', 'tlissak@gmail.com');
	ini_set("sendmail_from", "contact@lissak.fr");
	define('ATOS_MERCHANT_ID','011223344551111');
	define('ATOS_PATHFILE' , ATOS  . 'param'. DS.'pathfile.'.ATOS_MERCHANT_ID.'.txt');
	define("ATOS_RETURN", "http://g.lissak.fr/php.glasman.fr/return.php");
}else{
	define('PDO_DSN','mysql:host=127.0.0.1;port=103306;dbname='.PDO_DB.';charset=utf8');
	define('_EMAIL_TO_', 'glasman.fr@gmail.com, laurent.glasman@wanadoo.fr,tlissak@gmail.com');
	ini_set("sendmail_from", "contact@glasman.fr");
	define('ATOS_MERCHANT_ID','058205172800019');
	define('ATOS_PATHFILE' , ATOS  . 'param'. DS.'pathfile.'.ATOS_MERCHANT_ID.'.prod.txt');
	define('ATOS_RETURN','http://www.glasman.fr/return.php') ;
}

define('P_BACKUP',P_BASE. 'backup'.DS)  ;
define('P_PHOTO', P_BASE.'photos'.DS);
//if (! is_dir(P_BACKUP)){ 		mkdir(P_BACKUP); }
//if (! is_dir(P_PHOTO)){ 		mkdir(P_PHOTO); }
define('U_BASE' , U_PROTOCOLE . U_HOST .'/'. (U_ROOT != "/" ? U_ROOT :'') ) ;
define('U_BACKUP' , U_BASE .'backup/' ) ;
define('U_PHOTO' , U_BASE .'photos/' ) ;
define('V2_IMG' , false ) ;
define('_EMAIL_FROM_', 'contact@glasman.fr');
define('_EMAIL_HEADER_', 'from:' . _EMAIL_FROM_ ."\nMIME-version: 1.0\nContent-type: text/html; charset=utf-8\n");
@ini_set('date.timezone','Europe/Paris');
define('LNG','fr') ;
//$dfns = get_defined_constants(true) ;var_dump($dfns['user']);die ;
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
?>