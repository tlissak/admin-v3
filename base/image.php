<?php
include 'config.php';
function get($v){ 	return isset($_GET[$v]) ? $_GET[$v] : '' ;}
new ImageResize(get('file') , array('height'=>(int) (get('height' )),'width'=>(int) (get('width')))  ) ;
?>