<?

class FileBrowser{
	
	private $root = P_PHOTO ;
	
	function link($link,$title){  return '<a href="?path='.$link.'"  >'.$title.'</a>'; }
	
	public $bc = array();
	public $dirs = array() ;
	public $files = array();
	
	function get_item($f){
		$rel = str_replace(DS,'/',str_replace($this->root,'',$f)) ;
		return array(
			'full'=>$f ,
			'relative'=>$rel
			,'name'=>basename($f)
			,'uri'=>U_PHOTO. $rel 
		);
	}
	
	function __construct($path){
		$path = str_replace('/',DS,$path ) ;
		$path = str_replace('..'.DS,'',$path ) ;
		$path = str_replace('.'.DS,'',$path ) ;
		if ($path == DS) $path = '';
		
		$this->bc[] = $this->get_item($this->root)  ;	
		
		$spath = explode(DS,str_replace($this->root, '', $path) )  ;
		if (! $spath[0]) unset($spath[0]) ;		
		$curr = "" ;
		foreach($spath as $f){
			$curr .= '/' . $f ;
			$this->bc[] =  $this->get_item($curr)	 ;
		}
		
		$current_dir = $this->root . $path ;
		//echo $br ;
		$fs = glob( $current_dir . '/*'); array_reverse($fs);
		foreach ($fs as $f){if (is_dir($f)){
				$this->dirs [] = $this->get_item($f );
			}else if (is_file($f)){
				$this->files[] = $this->get_item($f ) ;
			}
		}
		return $this;
		foreach($this->dirs as $f){ 
			echo $f['link'].$br ; 
		}
		echo "<hr />" ;
		foreach($this->files as $f){ 
			echo $f['link'].$br ; 			
		}
		
		
		
	}
}
?>