<?

class AdminModule{
	//dispatcher
	//js ui inludes
	//hookers
	
	public static $MODULES = array();
	
	//load all avaiable modules 
	
	function __construct(){
		$dirs = glob(P_ADMIN .'modules'.DS .'*') ;
		foreach( $dirs as $d ){
			if (is_dir($d)){
				$res = parse_ini_file($d . DS . 'config.txt' ) ;
				$res['module_path'] = $d .DS ;
				self::$MODULES[] = $res;
			}	
		}
		
		foreach(self::$MODULES as &$m){
			if (isset($m['core'])){
				include $m["module_path"] . $m['core'].'.php' ;
				$m['class'] = new $m['core'] ;
			}
		}
		unset($m);		
	}
	
	function dispachers(){
		foreach(self::$MODULES as $m){
			if (isset($m['core'])){
				if (method_exists($m['class'] , 'dispacher') )
					$m['class']->dispacher() ;
			}
		}
	}
	
	function includers(){
		$ui_js = array();
		foreach(self::$MODULES as $m){
			if (isset($m['core'])){
				if (method_exists($m['class'] , 'includer') ){
					echo "<script src='modules/". $m['name'] .'/' .$m['class']->includer() ."'></script>" ;
				}
			}
		}
	}
		
}

?>