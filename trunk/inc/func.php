<?php
function c($_s = '',$o = false){ 
	if ($_s == '' ) return ;
	if (!$o) {echo $_s ; return ;}
	if ($o == 1) {echo l($_s);}
	if ($o == 2) echo nl2br($_s )  ;
	if ($o == 3) echo $_s ? ( strpos($_s,'http') == 0 ? $_s  : 'http://'. $_s ) : ''  ;
	if ($o == 4) echo number_format($_s, 2, ',', ' ')    ;
	if ($o == 5) echo implode( ', ',$_s)    ;
	if ($o == 6) for($i=1;$i<=$_s;$i++) echo '*'    ;
	if ($o == 7) echo strip_tags ($_s )	;
	if ($o == 8)  echo strlen($_s) >100 ? substr($_s,0,100) .'...' :  $_s ;
	if ($o == 9) {  preg_match('/^.{0,160}(?:.*?)\b/iu', $_s, $matches); echo $matches[0] .'...'; }
	if ($o == 10)  echo number_format($_s, 0, ',', ' ')  ;
	if ($o == 11)  echo number_format($_s, 2, '.', '')  ;
}
function l($_s){global $_LNG ; return isset($_LNG[$_s]) ? $_LNG[$_s] : $_s  ;}
function p($o){ echo '<pre style="font:11px/14px verdana;  z-index:999; ">' ; var_dump($o) ; echo '</pre><hr />'; } ;
function coded($str){ 	return htmlentities($str, ENT_QUOTES , "UTF-8");} 
function strtoidate($str){	return date("d/m/Y",$str) ;}
function redirect($uri){ header('Location: '.$uri);}
function get($v){ 	return isset($_GET[$v]) ? $_GET[$v] : '' ;}
function post($v){return isset($_POST[$v]) ? $_POST[$v] : '' ;}
function file_extension($filename){ $path_info = pathinfo($filename); return $path_info['extension'];}
function is_email($s) {return preg_match('/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,7}$/',$s);}
function is_tel($s){ return strlen($s) > 9;}
function is_data($s){ return strlen($s) > 2;}
function is_image($simg){	return (is_file($simg) && (in_array(strtolower(file_extension($simg)),array("jpg","png","jpeg","gif")))) ;}
function is_pdf($simg){	return (is_file($simg) && (in_array(strtolower(file_extension($simg)),array("pdf")))) ;}
function ip2int($_ip){$p=explode(".",$_ip);if (count($p)!=4) return 0; return 16777216*(int)($p[0])+65536 *(int)($p[1])+256*(int)($p[2])+(int)($p[3]) ;}
function curl($url,$params=array(),$method='GET'){
$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
if ($method=='POST'){	curl_setopt($ch,CURLOPT_POST,count($params));	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));}
$result = curl_exec($ch);	curl_close($ch);	return $result;	}
?>