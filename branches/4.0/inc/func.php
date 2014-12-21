<?php
function c($_s = '',$o = false){ 
	if ($_s == '' ) return ;
	if (!$o) {echo $_s ; return ;}
	if ($o == 1) {echo l($_s);}
	if ($o == 2) echo conv($_s,CONVERT::NL2BR )  ;
	if ($o == 3) echo conv($_s,CONVERT::HTTP )  ;
	if ($o == 4) echo conv($_s,CONVERT::NUM )  ;
	if ($o == 5) echo conv($_s,CONVERT::ARR )  ;
	if ($o == 6) echo conv($_s,CONVERT::STAR )  ;
	if ($o == 7)  echo conv($_s,CONVERT::NUM_INT )  ;
	if ($o == 8)  echo conv($_s,CONVERT::STRIP_100 )  ; 
	if ($o == 9) 	echo conv($_s,CONVERT::STRIP )  ;  
	if ($o == 10) echo conv($_s,CONVERT::STRIP_TAG )  ;
	if ($o == 11) echo conv($_s,CONVERT::NUM_API )  ;  
	if ($o == 13) echo conv($_s,CONVERT::UCWORDS )  ; 
}
function l($_s){global $_LNG ; return isset($_LNG[$_s]) ? $_LNG[$_s] : $_s  ;}
function p($o){ if (DEV_MODE || IP == IP_DEV){ echo '<pre class="debug-pre">' ;
	 $xx = debug_backtrace() ; 	echo  basename($xx[2]['file']).':' . $xx[2]['line'] ;
	var_dump($o) ; /*print_r($o);*/  echo '</pre><hr />'; } } ;
function coded($str){ 	return htmlentities($str, ENT_QUOTES , "UTF-8"); }  // depracted
function strtoidate($str){	return date("d/m/Y",$str) ;}
function redirect($uri){ header('Location: '.$uri);}
function get($v){ 	return isset($_GET[$v]) ? $_GET[$v] : '' ;}
function post($v){return isset($_POST[$v]) ? $_POST[$v] : '' ;}
function file_extension($filename){ $path_info = pathinfo($filename); return $path_info['extension'];}
function is_email($s) {return preg_match('/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,7}$/',$s);}
function is_tel($s){ return preg_match('/(^[0-9\-\(\)\.]{9,15}$)/',$s);}
function is_data($s){ return strlen(trim($s)) > 1;}
function is_image($simg){	return (is_file($simg) && (in_array(strtolower(file_extension($simg)),array("jpg","png","jpeg","gif")))) ;}
function is_pdf($simg){	return (is_file($simg) && (in_array(strtolower(file_extension($simg)),array("pdf")))) ;}
function ip2int($_ip){$p=explode(".",$_ip);if (count($p)!=4) return 0; return 16777216*(int)($p[0])+65536 *(int)($p[1])+256*(int)($p[2])+(int)($p[3]) ;}
function curl($url,$params=array(),$method='GET'){
$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
if ($method=='POST'){	curl_setopt($ch,CURLOPT_POST,count($params));	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));}
$result = curl_exec($ch);	curl_close($ch);	return $result;	}

interface CONVERT{
	const LNG 			=1;
	const NL2BR			=2;
	const HTTP			=3;
	const NUM			=4;
	const NUM_INT		=7;
	const NUM_API		=11;
	const ARR				=5;
	const STAR			=6;
	const STRIP_100	=8;
	const STRIP			=9;
	const STRIP_TAG	=10;
	const CLEANUP	=12;
	const CLEAN			=20;
	const UCWORDS	=13;
	const DATE			=14;
	const TO_HTML 	=15;
	const FILE_EXT	=16;
	const IP2INT		=17;
	const EOL				=18;
	const BR				=19;
	const URL_ENCODE	=21;
	const URL_DECODE	=22;
	const B64_ENCODE	=23;
	const B64_DECODE	=24;
	const MD5_FILE	=25;
	const MD5			=26;
}
function conv($_s,$o=0){
	switch($o){
		case CONVERT::LNG			:	global $_LNG ; return isset($_LNG[$_s]) ? $_LNG[$_s] : $_s ; /* $_LANG[$x] */ 
		case CONVERT::NL2BR		:	return nl2br($_s )  ;//new<br />line
		case CONVERT::HTTP			:	return $_s ? ( strpos($_s,'http') == 0 ? $_s  : 'http://'. $_s ) : ''  ; // http://www.google.com	
		case CONVERT::NUM			:	return number_format($_s, 2, ',', ' ')    ; // 4 253,05;
		case CONVERT::NUM_INT	:	return number_format($_s, 0, ',', ' ')  ;	 // 4 253 ;
		case CONVERT::NUM_API	:	return number_format($_s, 2, '.', '')  ; // 2000.00 for many payment apis
		case CONVERT::ARR			:	return implode( ', ',$_s)    ; //  banana,orange,cherry aspect $_s to be array !
		case CONVERT::STAR			:	for($_o='',$i=1;$i<=$_s;$i++) $_o .= '*' ; return $_o;    // ****
		case CONVERT::STRIP_100	:	return strlen($_s) >100 ? substr($_s,0,100) .'...' :  $_s ; //strip larger text more then 100 chars
		case CONVERT::STRIP		:	preg_match('/^.{0,160}(?:.*?)\b/iu', $_s, $matches); return (isset($matches[0]) ? $matches[0] : '' ).'...';//strip large text with no word / tag cut...
		case CONVERT::STRIP_TAG:	return strip_tags ($_s )	; // &lgt;span&rgt;
		case CONVERT::CLEANUP	:	return preg_match('/^[0-9a-zA-Z \-\.]$/',$_s); // cleanup non numeric/alphabetic charac ex removes : ($^@'(çà\_(|é&")...
		case CONVERT::CLEAN		: 	return preg_replace('/[^\p{Latin}\d ]/u', '', $_s);// cleanup non numeric non alphabetic and non latin charact and non space 
		case CONVERT::UCWORDS	:	return ucwords(strtolower($_s) ) ; // UpEr CASE to Upper Case ;
		case CONVERT::DATE			:	return date("d/m/Y",$_s) ; //espect int as param
		case CONVERT::TO_HTML	:	return htmlentities($_s, ENT_QUOTES , "UTF-8"); 
		case CONVERT::FILE_EXT	:	$path_info = pathinfo($_s); return $path_info['extension']; // get file ext
		case CONVERT::IP2INT		:	$p=explode(".",$_s);if (count($p)!=4) return 0; return 16777216*(int)($p[0])+65536 *(int)($p[1])+256*(int)($p[2])+(int)($p[3]) ; // ip to int
		case CONVERT::EOL			:	return $_s. "\r\n" ; // end of line
		case CONVERT::BR				:	return $_s. '<br />'."\r\n" ; // line break 
		case CONVERT::URL_ENCODE : return rawurlencode($_s) ;
		case CONVERT::URL_DECODE : return rawurldecode($_s) ;
		case CONVERT::B64_ENCODE : return base64_encode($_s) ;
		case CONVERT::B64_DECODE : return base64_decode($_s) ;
		case CONVERT::MD5_FILE 	: 	return md5_file($_s) ;
		case CONVERT::MD5 			: 	return md5($_s) ;		
		default : 	return $_s;
	}
}


?>