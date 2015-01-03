<?

class FileBackup{
	
	private $ziph ;
	
	private $base ;
	private $root ;

	//TODO verify empty folders
	function __construct($path, $zfile){		
		$this->ziph = new ZipArchive();		
		if(is_file($zfile)) {
		 die( " $zfile allready exists !" );		 
		}else{
		  if($this->ziph->open($zfile, ZIPARCHIVE::CREATE) !== TRUE)    {
			die("Could not Create $zfile");
		  }
		}
		$this->base = $path ;
		$this->root = basename($path);		
		$this->addDir($path) ;
		$this->ziph->close();
	}
	
	function addFile($f){
		if (!preg_match("/.*?\.zip$/", $f)){
			$this->ziph->addFile($f,$this->root  .str_replace($this->base, '', $f));
		}
	}
	
	function addDir($path){
		$this->ziph->addEmptyDir($this->root . str_replace($this->base, '', $path));	
		$fs = glob($path . '/*');
		foreach ($fs as $f) {		
			if (is_dir($f)) {
				$this->addDir($f,$this->base);
			} else if (is_file($f))  {
				$this->addFile($f);
			}
		}
	}
}

?> 