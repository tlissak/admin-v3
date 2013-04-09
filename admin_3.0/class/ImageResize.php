<?php
class ImageResize{
	public $input 		 ;
	public $output 		 ;
	public $jpg_compression 		= 100 ;	 //90
	
	function __construct($u_file,$dims){
		
		$file =  P_PHOTO . str_replace('/',DS,$u_file );		
		if (! is_file( $file )){		die('input file not found '.$file  );	}
		if ($dims['height'] == 0 && $dims['width'] == 0){ die('dimensions not seted');}
		
		$info 								= getimagesize($file);		
		$this->input['path']			= $file ;
		$this->input['width']		= $info[0];
		$this->input['height']		= $info[1];	
		$this->input['type']			= $info[2];
		$this->input['mime'] 		= $info['mime'] ;
		$this->input['length']		= (string)(filesize($file));
		
		if ($dims['width'] == 0) $factor = $dims['height']/$this->input['height'];
		elseif ($dims['height'] == 0) $factor = $dims['width']/$this->input['width'];
		else $factor 		= min ( $dims['width'] / $this->input['width'], $dims['height'] / $this->input['height']);  		
		$this->output['width'] 		= round ($this->input['width'] * $factor);
		$this->output['height'] 	= round ($this->input['height'] * $factor);
		
		$output_file_name = preg_replace('/\.(.+)$/','_'. $this->output['width'].'x'.$this->output['height'] .'.\\1' , basename($this->input['path'] ) ) ;		
		$base = dirname($this->input['path'] ) . DS ;		
		if ( ! is_dir($base . 'thumb' ))	{	mkdir($base . 'thumb' . DS) ; }
		$this->output['path'] =   $base .'thumb'.DS. $output_file_name ;
		
		if (! is_file($this->output['path'])){			
			if ($this->input['type'] == IMAGETYPE_GIF)
			$this->input['file'] = imagecreatefromgif($this->input['path']);
			elseif ($this->input['type'] == IMAGETYPE_JPEG)
			$this->input['file'] = imagecreatefromjpeg($this->input['path']);
			elseif ($this->input['type'] == IMAGETYPE_PNG)
			$this->input['file'] = imagecreatefrompng($this->input['path']);			
			$this->output['file'] = imagecreatetruecolor( $this->output['width'], $this->output['height'] );
			if ( $this->input['type'] == IMAGETYPE_GIF)     {
				$trnprt_indx = imagecolortransparent($this->input['file']);
				if ($trnprt_indx >= 0) {
						$trnprt_color    = imagecolorsforindex($this->input['type'], $trnprt_indx);
						$trnprt_indx    	= imagecolorallocate($this->output['file'], $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
						imagefill($this->output['file'], 0, 0, $trnprt_indx);
						imagecolortransparent($this->output['file'], $trnprt_indx);
				}
			}elseif($this->input['type'] == IMAGETYPE_PNG) {
				imagealphablending($this->output['file'], false);
				$color = imagecolorallocatealpha($this->output['file'], 0, 0, 0, 127);
				imagefill($this->output['file'], 0, 0, $color);
				imagesavealpha($this->output['file'], true);
			}
			imagecopyresampled(	$this->output['file'], $this->input['file'], 0, 0, 0, 0, $this->output['width'], $this->output['height'] , $this->input['width'], $this->input['height']);	
			imagedestroy($this->input['file']) ;
			$this->output['type'] 		= $this->input['type'] ;				
			if( $this->output['type'] == IMAGETYPE_JPEG ) 
			imagejpeg($this->output['file'],$this->output['path'],$this->jpg_compression);
			elseif( $this->output['type'] == IMAGETYPE_GIF ) 
			imagegif($this->output['file'],$this->output['path']); 
			elseif( $this->output['type'] == IMAGETYPE_PNG )
			imagepng($this->output['file'],$this->output['path']);	
			imagedestroy($this->output['file']) ;	
		}
		
		$info 								= getimagesize($this->output['path']);
		$this->output['width']		= $info[0];
		$this->output['height']		= $info[1];	
		$this->output['type']		= $info[2];
		$this->output['mime'] 		= $info['mime'] ;
		$this->output['length']		= (string)(filesize($this->output['path'])) ;			
		header('Accept-Ranges: bytes');
		header('Content-Length: '.$this->output['length']);
		header("Content-Transfer-Encoding: binary");
		header('Content-Type:'.image_type_to_mime_type($this->output['type']));		
		//ob_clean();flush();set_time_limit(0);
		readfile($this->output['path']);
		exit ;
		
	}
}
?>