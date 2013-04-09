<?php

function pTrace($obj){
	p(FullTrace()) ;
	p($obj);	
}
function Debug($x){
	fb($x,FirePHP::TRACE) ;
	Debug::$info[] = FullTrace();
	Debug::$info[] = $x ;	
}
function _die($x){
	p($x);
	p( FullTrace() );
	die;
}
function DebugSql($q){ 	
//	fb($q,FirePHP::LOG) ; 
	Debug::$sql[] = $q;	
}
function DebugError($src,$inf){ 	
	fb( "[$src] $inf ",FirePHP::TRACE) ; 
	Debug::$error[] = '<span style="color:red">' . "[$src] $inf " ."</span>" .  '<span style="color:green">' . Trace() .'</span>';
}
function Todo($inf,$impotance=1){ 	
	//fb($inf,FirePHP::TRACE) ;
	Debug::$todo[] = $impotance . ' : ' . $inf . ' '.  '<span style="color:green">'. Trace() .'</span>';
}

function Trace(){ 	$xx = debug_backtrace() ; 	return  basename($xx[1]['file']).':' . $xx[1]['line'] ;}
function FullTrace(){ 
	$arr = array();
	$val = debug_backtrace() ;	
	unset($val[0]);	
	$val[1] = array('file'=>$val[1]['file'], 'line'=>$val[1]['line']);
	
	foreach($val as $v){
			$out ="";
			if (isset($v["class"]))
				$out .=  $v['class'] . "->" ;
			if (isset($v["function"]))
				$out .=  $v['function'] ;
			if (isset($v["args"])){
				$out .= "(";
				foreach($v["args"] as $arg){
					$out .=  $arg.",";
				}
				$out .= ")";
			}
			
			$out .= ' - '. basename($v['file']) . ':' . $v['line'] ;
			$arr[] = $out;
	}
	return $arr ;
}

class Debug{
	
	public static $debug_time 			= false ;	
	public static $debug_info		 		= true ;
	public static $debug_todo	 		= true ;	
	public static $debug_sql		 		= false ;
	public static $debug_error	 		= true ;
	
	public static $column_count  ;
	
	public static $time = array() ;
	public static $info = array() ;
	public static $todo = array() ;
	public static $sql = array() ;
	public static $error = array() ;	
	
	function __construct(){
		if (!self::$debug_error) return ;
		set_error_handler('developpementErrorHandler');
		ini_set('html_errors', 'on');
		ini_set('display_errors', 'on');
		error_reporting(E_ALL );	
	}
	
	static function pre($arr,$title,$print_type = 0){
		if (count($arr) == 0) return;
		$width = 100 / (self::$column_count ? self::$column_count : 1) ;
		echo '<div style="width:'.$width.'%; float:left; ">';
		echo '<h1>'. $title .'</h1>' ;
		//
		if ($print_type == 1)
			echo '<pre style="white-space:pre-line;font-size:12px;line-height:12px;">' ;
		elseif ($print_type == 3)
			echo '<pre style="white-space:pre-line;font-size:10px;">' ;
		else
			echo '<pre style="white-space:pre-line;font-size:11px;">' ;
		foreach($arr as $itm){
			if ($print_type == 2){
				print_r($itm);
				echo '<br />' ;			
			}elseif($print_type == 1 ||$print_type == 3 ){
				var_dump($itm) ;
			}else{
				echo '<li>' . $itm .'</li>';
			}
		}
		
		if ($print_type == 1)
			echo '</pre>' ;
		elseif ($print_type == 3)
			echo '</pre>' ;
		else
			echo '<pre>' ;
		echo '</div>';
	}
	
	public static function p(){		
		 if  ($_SERVER['REMOTE_ADDR'] != '127.0.0.1'){ 			return;	 		}		 
			self::$column_count = 0;
			if (self::$debug_error && count(self::$error)){		self::$column_count++ ;			}
			if (self::$debug_info && count(self::$info)){			self::$column_count++ ;			}
			if (self::$debug_sql && count(self::$sql)){				self::$column_count++ ;			}
			if (self::$debug_todo && count(self::$todo)){			self::$column_count++ ;			}
			
			if (self::$debug_error){			
				self::pre(self::$error , 'Errors');
			}
			if (self::$debug_info){
				self::pre(self::$info , 'Debug',1);
				
			}
			if (self::$debug_sql){
				self::pre(self::$sql , 'Sql');
			}
			if (self::$debug_todo){
				self::pre(self::$todo , 'Todo');
			}	
	}
}


function developpementErrorHandler($errno, $errstr, $errfile, $errline){
	
	if (!(error_reporting() & $errno))		return false;
	switch($errno)	{
		case E_ERROR:			$err =  '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_WARNING:			$err =  '[PHP Warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_PARSE:			$err =  '[PHP Parse #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_NOTICE:			$err =  '[PHP Notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_CORE_ERROR:			$err =  '[PHP Core #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_CORE_WARNING:			$err =  '[PHP Core warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_COMPILE_ERROR:			$err =  '[PHP Compile #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_COMPILE_WARNING:			$err =  '[PHP Compile warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_USER_ERROR:			$err =  '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_USER_WARNING:			$err =  '[PHP User warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_USER_NOTICE:			$err =  '[PHP User notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_STRICT:			$err =  '[PHP Strict #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		case E_RECOVERABLE_ERROR:			$err =  '[PHP Recoverable error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';			break;
		default:			$err =  '[PHP Unknown error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';	}
	DebugError('PHP', $err );
	return true;
}

?>