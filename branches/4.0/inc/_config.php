<?php
define('DS', DIRECTORY_SEPARATOR);
define('P_BASE', dirname(dirname(__FILE__)).DS);

/*P:PATH end with DS*/
define('D_ROOT',$_SERVER["DOCUMENT_ROOT"]) ;
define('P_ROOT',D_ROOT .( substr(D_ROOT, -1) !== '/' ? '/' : '' )) ; 
define('P_CONF', str_replace('\\','/',__FILE__));
define('P_SCRIPT',$_SERVER["SCRIPT_FILENAME"]) ;
define('P_SELF',$_SERVER["SCRIPT_NAME"]) ;
define('P_INC',P_BASE. 'inc'.DS)  ;
define('P_CONTROLS',P_BASE. 'controller'.DS)  ; 
define('P_CLASS',P_BASE. 'class'.DS)  ; 

/*U:URL ends with / */
define('U_HOST',  (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') );
define('U_SELF',$_SERVER['PHP_SELF']); // used to gate page name
define('U_URI',$_SERVER['REQUEST_URI'] );
define('U_DIRURI',dirname(dirname(str_replace(P_ROOT,'',P_CONF))) );
define('U_ROOT' , U_DIRURI == "." ? "/" : U_DIRURI  .( substr(U_DIRURI, -1) !== '/' ? '/' : '' ) ) ;
define('U_PROTOCOLE', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||  $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" );

define('IP_SERVER',$_SERVER['SERVER_ADDR']) ;
define('IP',$_SERVER['REMOTE_ADDR']) ;

define('NL',"\r\n") ;

define('_CIPHER_ALGORITHM_',true);
define('_BLOWFISH_KEY_', '43S6COfkycXO38GNCHdSMIV5142Ran4gUvC7bj3ukwPo91jgubLJFN91');
define('_BLOWFISH_IV_', 'wk5Irzqb');
define('_RIJNDAEL_KEY_', 'v1VXChCyUPUVEJuVAEyM80zgaYp3Jr9H');
define('_RIJNDAEL_IV_', '7B6YhaP5en6B6lcxD5l3Bg==');
if (!defined('PHP_VERSION_ID')){    $version = explode('.', PHP_VERSION);    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));}

function __autoload($ClassName){	
	if (is_file(P_CLASS.$ClassName.'.php')){
		include P_CLASS .$ClassName.'.php' ;
	}else{
		echo '__autoload('. P_CLASS.$ClassName.').php not found ' .__FILE__ .__LINE__; 	die ;	
	}
}

?>