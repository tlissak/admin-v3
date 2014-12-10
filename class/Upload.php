<?
class Upload{
	public $upload_path  ;
	public $virtual_path ;
	
	private $p_surfix ;
	private $u_surfix ;
	
	public $files = array() ;
	
	public function json_output(){
		header('Content-type: application/json');
		echo json_encode( $this ) ;
		die ;
	}
	
	public function __construct($p_surfix = ''){
		
		$this->u_surfix 						= ($p_surfix ? str_replace(DS,'/',$p_surfix) : '');
		
		$this->upload_path 				= 	P_PHOTO . $p_surfix ;
		$this->virtual_path					= 	U_PHOTO  ;
		$this->files 								= $this->get_normalized_files();
		
		if (V2_IMG){   
			$this->u_surfix  = 'photos/' . $this->u_surfix ; 
			$this->virtual_path = '../' ;
		}
		
		foreach($this->files as &$photos){
			foreach($photos as &$photo){
				if ($photo['error'] ===  0){
					if ($this->is_image($photo)){
						if ($uploaded = $this->upload_image($photo)){
							$photo['uploaded'] = $uploaded ;
						}else{
							$photo['error'] = "998 - May permissions or folder not exists" ;
							$photo['error_msg'] = 'UploadError' ;		
						}
					}else{
						$photo['error'] = "999 - May MIME file type not allowed" ;
						$photo['error_msg'] = 'FileTypeNotAllowed' ;
					}
					unset($photo['tmp_name']) ;
				}else{
					$photo['error'] = '997- May php upload size limit' ;
					$photo['error_msg'] = 'UploadError' ;
					
				}
			}
		}
		return $this->files;
	}

	public function get_normalized_files(){
		$newfiles = array();
		foreach($_FILES as $k => $v){		
			foreach($v as $paramname => $paramvalue)
				foreach((array)$paramvalue as $index => $value)
					$newfiles[$k][$index][$paramname] = $value;
		}
		return $newfiles;
	}
	
	public function get_ext($name){
		return  strtolower(substr(strrchr($name, "."), 1));
	}
		
	public function is_image( $img ){
		if ( ! isset($img['type']) || !isset($img['size']) || ! isset($img['name'])){ return false ;}
		if ( $img['size'] == 0) return false ;
		if ( $ext = $this->get_ext( $img['name']) ){
			if(!in_array($ext, array('jpg', 'jpeg', 'png', 'gif','bmp','doc','docx','pdf','xls','xlsx','zip' ,'avi','mpeg','mp3','csv','txt'))){
					return false ; 
			}
			// verify mime type ;
		}else{
			return false;
		}
		return true ;
	}
	
	public function files_identical($fn1 , $fn2){
		if(@filetype($fn1) !== @filetype($fn2)){	return FALSE;		}
		if(filesize($fn1) !== filesize($fn2)){		return FALSE;		}
		return true;
	}
	
	public function real_files_identical($fn1 , $fn2){
		if(filetype($fn1) !== filetype($fn2)){	return FALSE;		}
		if(filesize($fn1) !== filesize($fn2)){		return FALSE;				}
		if(!$fp1 = fopen($fn1, 'rb')){				return FALSE;  }	
		if(!$fp2 = fopen($fn2, 'rb')) {	 			fclose($fp1);			return FALSE;		}
		$same = TRUE; 
		while (!feof($fp1) and !feof($fp2)){			if(fread($fp1, 4096) !== fread($fp2, 4096)) {					$same = FALSE;	break;	}	}
		if(feof($fp1) !== feof($fp2))	{			$same = FALSE;	 }
		fclose($fp1);	fclose($fp2);	
		return $same;
	}
	
	public function upload_image($photo){
		$file_name = trim(strtolower(preg_replace('/[^a-z0-9_\.\-\(\)]/i', '_',$photo['name'] ) )) ;		
		if (file_exists($this->upload_path . $file_name )){
			if ($this->files_identical($this->upload_path . $file_name ,$photo['tmp_name'])) 	{
				return $this->u_surfix . $file_name ;
			}else{
				$i = 1 ;
				do{
					$new_file_name	 = '('. $i .')_'. $file_name ;
					$i++ ;
					if ($this->files_identical($this->upload_path . $new_file_name ,$photo['tmp_name'])) 	{
						return $this->u_surfix . $new_file_name ;
					}
				}while(  file_exists( $this->upload_path .  $new_file_name )) ;
				$file_name = $new_file_name ;
			}
		}
		
		$move_file = @move_uploaded_file($photo['tmp_name'], $this->upload_path.$file_name);
		if($move_file){
			return $this->u_surfix .$file_name ;
		}else{
			@unlink($photo['tmp_name']);
			return false; 
		}
	}
}

?>