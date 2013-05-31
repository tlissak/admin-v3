<?php
class SQL{
	static $types_txt = array(
	//slqite
	'VARCHAR' ,'TEXT','TIME','DATE','TIMESTAMP','NVARCHAR','BLOB','NCHAR' ,'DATETIME'
	//mysql
	 ,'LONGTEXT' 
	) ;
	static $types_int = array('BOOLEAN' ,'BIT' ,'INTEGER','FLOAT','NUMERIC' ,'REAL','DOUBLE','DECIMAL') ;
	//TODO :
	//VERIFY IF ALL 
	
	static function v2txt($out){			
			/* \' */	$out = str_replace("\\'","'",$out) ;
			/* \  */	$out = str_replace("\\","",$out) ;	
			/* "  */ $out = str_replace('""','"',$out) ;
			$out =SQLite3::escapeString($out) ;			
			$out =  "'". $out ."'" ;
			return $out;
	}
	static function v2int($str){ if (is_numeric($str)){ return $str ;} return intval($str);}	
	static function strpos_arr($haystack, $needle) {	if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) { 	if(($pos = strpos($haystack, $what))!==false) return $pos;}	return -1;
	}
	static function build($type,$tbl,$pairs  = array(),$id=0) {
		global $db ;
		$sql_pairs 		= array() ;	
		$sql_values		= array() ;	
		$pairs_txt 		= array() ;
		$pairs_int 		= array() ;		
		$types 				= $db->ctypes($tbl );
		foreach($pairs as $key=>$val){
			if(self::strpos_arr( $types[$key] , self::$types_int ) == 0){
					$pairs_int[$key] = $val ;
			}else{
					$pairs_txt[$key] = $val ;	
			}			
		}
		if ($type == 'UPDATE'){
			$sql = 'UPDATE `'.$tbl.'` SET ' ;
			foreach($pairs_txt as $key=>$val){$sql_pairs[] = '`'.$key.'`='.SQL::v2txt($val);}
			foreach($pairs_int as $key=>$val){$sql_pairs[] = '`'.$key.'`='. SQL::v2int($val);}
			$sql .=  join($sql_pairs,','). ' WHERE id = '.$id ;
		}elseif($type=='INSERT'){
			$sql = 'INSERT INTO `'.$tbl .'`' ;
			foreach($pairs_txt as $key=>$val){$sql_pairs[] = '`'.$key.'`'; $sql_values[]=SQL::v2txt($val);}
			foreach($pairs_int as $key=>$val){$sql_pairs[] = '`'.$key.'`'; $sql_values[]=SQL::v2int($val);}			
			$sql .= '('.    join($sql_pairs,',')  . ') VALUES ('.    join($sql_values,',')     .') ' ;				
		}elseif($type=='DUPLICATE'){
			foreach($pairs_txt as $key=>$val){$sql_pairs[] = '`'.$key.'`'; }
			foreach($pairs_int as $key=>$val){$sql_pairs[] = '`'.$key.'`'; }
			$sql = 'INSERT INTO `'.$tbl.'`  ('. join($sql_pairs,',') .')  
						SELECT '. join($sql_pairs,',') .' FROM `'.$tbl.'`	WHERE id = '. $id  ;
		}
		return $sql ;
	}
}

?>