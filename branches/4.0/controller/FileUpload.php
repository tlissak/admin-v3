<?php

class FileUpload{
    public $upload_path  ;
    public $virtual_path ;

    private $p_surfix ;
    private $u_surfix ;

    public $files = array() ;

    public function create_dir($p_surfix){
        $psd = explode(DS,$p_surfix);
        //TODO add some protection
        $root =P_PHOTO ;
        foreach($psd as $dir){
            $root .= $dir .DS ;
            if (!is_dir($root)){
                mkdir($root);
            }
        }
    }

    public function __construct($p_surfix = ''){

        $this->u_surfix 				= ($p_surfix ? str_replace(DS,'/',$p_surfix) : '');

        $this->upload_path 				= 	P_PHOTO . $p_surfix ;

        if (!is_dir($this->upload_path)){
            $this->create_dir($p_surfix);
        }
        if (stripos($this->upload_path, DS) !== 0 ){
            $this->upload_path .= DS ;
        }

        $this->virtual_path					= U_PHOTO ;
        $this->files 						= $this->get_normalized_files();

        if (count($this->files) == 0){
            $this->error = 'no files !' ;
            p($_FILES);
        }
       // p($this->files);

        foreach($this->files as &$photos){
            foreach($photos as &$photo){
                if ($photo['error'] ===  0){
                  //  if ($this->is_image($photo)){
                        if ($uploaded = $this->upload_image($photo)){
                            $photo['uploaded'] = $uploaded ;
                        }else{
                            $photo['error'] = "998 - May permissions or folder not exists" ;
                            $photo['error_msg'] = 'UploadError' ;
                        }
                    //}else{
                    //    $photo['error'] = "999 - May MIME file type not allowed" ;
                     //   $photo['error_msg'] = 'FileTypeNotAllowed' ;
                    //}
                    unset($photo['tmp_name']) ;
                }else{
                    $photo['error'] = '997- May php upload size limit' ;
                    $photo['error_msg'] = 'UploadError' ;

                }
            }
        }
        //p( $this->files ) ;
        //die ;

        return json_encode( $this ) ;

    }

    public function get_normalized_files(){
        //p($_FILES);
        //return $_FILES ;
        $newfiles = array();
        foreach($_FILES as $k => $v){

            foreach($v as $paramname => $paramvalue)
                foreach((array)$paramvalue as $index => $value)
                    $newfiles[$k][$index][$paramname] = $value;
        }
        return $newfiles;
    }

    public function files_identical($fn1 , $fn2){
        if(@filetype($fn1) !== @filetype($fn2)){	return FALSE;		}
        if(filesize($fn1) !== filesize($fn2)){		return FALSE;		}
        return true;
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