<?php


function get($s){ return isset($_GET[$s]) ? $_GET[$s]: '' ;} ;
if (get('error') == 404 ) {
	die( "File not found !" );
}
if (get('error') == 403 ) {
	die( "Forbidden" );
}

include('ImageResize.php') ;

$get_file 			= get('file') ;
$get_width 		= (int) (get('width'));
$get_height 	= (int) (get('height' ));
$get_gray	 	= get('gray') ?  true : false ;
$dim					= array('height'=>$get_height,'width'=>$get_width) ;

new ImageResize($get_file, $dim,$get_gray ) ;
?>