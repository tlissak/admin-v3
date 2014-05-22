<?php

include(dirname(__FILE__).'/config.php');
include(P_INC.'/func.php');
require_once(P_ADMIN .'inc/fb.php');
new Debug();
$db 		= new Db();

function rwlink($a,$p){  	if ($p == 'page') return 'detecteur-' . $a['url_alias'] ; }
				
				

?>