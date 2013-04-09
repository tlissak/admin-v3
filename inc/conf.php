<?php
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('RP', dirname(dirname(__FILE__)));
define('BP', dirname($_SERVER['PHP_SELF']));
define('BU', substr(BP,0,strpos(BP.'/','/',1)));

define('_INC_DIR_',dirname(__FILE__))  ;
define('_ADMIN_DIR_',RP.DS.'admin_2.2'.DS)  ;
define('_ADMIN_INC_DIR_',_ADMIN_DIR_.'inc')  ;
define('_CLASS_DIR_',RP.DS.'class'.DS)  ;
define('_CONTROL_DIR_',RP.DS.'controller'.DS)  ;
define('_ADMIN_CLASS_DIR_',_ADMIN_DIR_.'class'.DS)  ;

//define("DB_SERVER",""); define("DB_USER","");define("DB_PASSWORD",""); define("DB_NAME","");

define("DB_FILE",RP.DS.'db'.DS.'atrium.s3db');
define("_ADMIN_CONFIG_FILE_",_ADMIN_DIR_.DS.'site' .DS . 'atrium.php');



define('_BASE_URI_',BU.'/'); 

define('_CIPHER_ALGORITHM_',true);
define('_BLOWFISH_KEY_', '43S6COfkycXO38GNCHdSMIV5142Ran4gUvC7bj3ukwPo91jgubLJFN91');
define('_BLOWFISH_IV_', 'wk5Irzqb');
define('_RIJNDAEL_KEY_', 'v1VXChCyUPUVEJuVAEyM80zgaYp3Jr9H');
define('_RIJNDAEL_IV_', '7B6YhaP5en6B6lcxD5l3Bg==');

if (!defined('PHP_VERSION_ID')){
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

define('_EMAIL_FROM_', 'contact@weeatkosher.com');
define('_EMAIL_TO_', 'contact@weeatkosher.com');
define('_EMAIL_HEADER_', 'from:' . _EMAIL_FROM_ ."\nMIME-version: 1.0\nContent-type: text/html; charset=utf-8\n");
@ini_set('date.timezone','Europe/Paris');


?>