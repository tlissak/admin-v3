<?php
define('DS', DIRECTORY_SEPARATOR);
define('P_PHOTO', dirname(__FILE__).DS);

class ImageResize{
	public $input 		 ;
	public $output 		 ;
	public $jpg_compression 		= 100 ;	 //90
	
	function __construct($u_file,$dims){
		
		$file =  P_PHOTO . str_replace('/',DS,$u_file );		
		if (! is_file( $file )){		header("HTTP/1.0 404 Not Found"); die('input file not found ');	}
		if ($dims['height'] == 0 && $dims['width'] == 0){
            $this->output['path'] = $file;
            $this->output_file();
            return ;
        }
		
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
		if (($this->input['width'] * $factor)> $this->input['width'] && ($this->input['height'] * $factor) > $this->input['height']){
			$this->output['width'] 		= $this->input['width'] *1.3 ;
			$this->output['height'] 	= $this->input['height'] * 1.3;
		}else{
			$this->output['width'] 		= round ($this->input['width'] * $factor);
			$this->output['height'] 	= round ($this->input['height'] * $factor);
		}
		$output_file_name = preg_replace('/\.(.+)$/','_'. $this->output['width'].'x'.$this->output['height'] .'.\\1' , basename($this->input['path'] ) ) ;		
		$base = dirname($this->input['path'] ) . DS ;		
		if ( ! is_dir($base . 'thumb' ))	{	mkdir($base . 'thumb' . DS) ; }
		$this->output['path'] =   $base .'thumb'.DS. $output_file_name ;
		
		if (! is_file($this->output['path'])){			
			if ($this->input['type'] == IMAGETYPE_GIF){
			$this->input['file'] = imagecreatefromgif($this->input['path']);			
			}elseif ($this->input['type'] == IMAGETYPE_JPEG)
			$this->input['file'] = imagecreatefromjpeg($this->input['path']);
			elseif ($this->input['type'] == IMAGETYPE_PNG)
			$this->input['file'] = imagecreatefrompng($this->input['path']);			
			$this->output['file'] = imagecreatetruecolor( $this->output['width'], $this->output['height'] );
			if ( $this->input['type'] == IMAGETYPE_GIF)     {
				$trnprt_indx = imagecolortransparent($this->input['file']);
				if ($trnprt_indx >= 0) {
						$trnprt_color    = @imagecolorsforindex($this->input['file'], $trnprt_indx);
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
		
        $this->output_file();
		
	}

    public function output_file(){

        $info 						= getimagesize($this->output['path']);
        $this->output['width']		= $info[0];
        $this->output['height']		= $info[1];
        $this->output['type']		= $info[2];
        $this->output['mime'] 		= $info['mime'] ;
        $this->output['length']		= (string)(filesize($this->output['path'])) ;

        header('X-Robots-Tag: index,archive');
        header('X-Pad: avoid browser bug');
        header("Etag: ".sprintf('"%x-%x-%s"', base_convert($this->output['width']. 'x' . $this->output['height'] ,11,16) , $this->output['length'] ,base_convert($this->output['path'],10,16) ));

        header('Cache-control: max-age='.(60*60*24*365));
        header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
        header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime($this->output['path'])));

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            header('HTTP/1.1 304 Not Modified');
            die();
        }

        header('Accept-Ranges: bytes');
        header('Content-Length: '.$this->output['length']);
        header("Content-Transfer-Encoding: binary");
        header('Content-Type:'.image_type_to_mime_type($this->output['type']));
        //ob_clean();flush();set_time_limit(0);
        readfile($this->output['path']);
        exit ;
    }

    protected function sendHTTPCacheHeaders($cache_file_name, $check_request = false)  {
        $mtime = @filemtime($cache_file_name);
        if($mtime > 0) {
            $gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
            $etag = sprintf('%08x-%08x', crc32($cache_file_name), $mtime);

            header('ETag: "' . $etag . '"');
            header('Last-Modified: ' . $gmt_mtime);
            header('Cache-Control: private');
            // we don't send an "Expires:" header to make clients/browsers use if-modified-since and/or if-none-match
            if($check_request){
                if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && !empty($_SERVER['HTTP_IF_NONE_MATCH'])){
                    $tmp = explode(';', $_SERVER['HTTP_IF_NONE_MATCH']); // IE fix!
                    if(!empty($tmp[0]) && strtotime($tmp[0]) == strtotime($gmt_mtime)){
                        header('HTTP/1.1 304 Not Modified');
                        return false;
                    }
                }

                if(isset($_SERVER['HTTP_IF_NONE_MATCH'])){
                    if(str_replace(array('\"', '"'), '', $_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                        header('HTTP/1.1 304 Not Modified');
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
?>