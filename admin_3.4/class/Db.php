<?php
class Db{

	public $db ;
	public $errors = array() ;
	public $querys = array() ;
	public $last_error =  "" ;
	public $pdo_type  ;
	public $pdo_dsn  ;
	public $columns = array();

	/**
	 * @param $p_dsn
	 * @param $p_type
	 * @param string $p_user
	 * @param string $p_pass
	 */
	function __construct($p_dsn , $p_type  ,$p_user="",$p_pass=""){
		$this->pdo_type 	= $p_type;
		$this->pdo_dsn 		= $p_dsn;
		try {
			$this->db = new PDO( $p_dsn,$p_user,$p_pass);	
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			if ($this->pdo_type == 'mysql'){	$this->db->query("SET NAMES 'utf8'");} //$this->db->exec("set names utf8");
			return ;	
		}catch (PDOException $err) {
			die('Unable to connect to database '.$err );
		}
	}

	function __destruct(){
		$this->db = null ;	
	}

	static function v2txt($str){ 		return "'". SQLite3::escapeString($str) ."'";	}
	static function v2int($str){ if (is_numeric($str)){ return $str ;} return intval($str); }

	static function iso2utf8(&$value, $key){
		$value = iconv('ISO-8859-1','UTF-8', $value);
	}

	function debug($sql){
		$this->querys[] = $sql ;	
	}

	function error($e,$q){
        $er  = (implode(', ',$e) .' :: '. $q );
        $this->last_error = $er ;
        $this->errors[] = $er;
		if (DEV_MODE) {
			p($er);
			p($q);
			die;
		}
	}

	function fetch($q){
		$this->debug($q);
		if ($sth = $this->db->prepare($q) ){
			$sth->execute();	
			if ($res =  $sth->fetchall(PDO::FETCH_ASSOC)){
				if ($this->pdo_type == 'odbc') foreach($res as &$r){	array_walk($r,'Db::iso2utf8' );	}
				return $res;
			}else{
				return array() ;
			}
		}else{
			$this->error($this->db->errorInfo() , $q) ;
			return array() ;	
		}
	}

	function fetchRow($q){
		$this->debug($q);
		if ($sth = $this->db->prepare($q)){
			$sth->execute();	
			if ($res = $sth->fetch(PDO::FETCH_ASSOC) ){
				//if ($this->pdo_type == 'odbc') array_walk($res,'Db::iso2utf8' );
				return $res ;
			}else{
				return array() ;	
			}
		}else{
			$this->error($this->db->errorInfo() , $q) ;
			return array() ;	
		}
	}
	function query($q){
		$this->debug($q);
		if ($sth = $this->db->prepare($q)){
			if($sth->execute()){
				if (strrpos($q,'INSERT' ) === 0)
					return $this->db->lastInsertId() ;
				return true ;
			}else{
				$this->error($this->db->errorInfo() , $q) ;
				return false;
			}
		}else{
			$this->error($this->db->errorInfo() , $q) ;
			return false;	
		}		
	}

	function ctypes($tbl) {
		if (isset($this->columns[$tbl] )){			return $this->columns[$tbl] ;		}
		if ($this->pdo_type == 'mysql'){
			$columns = $this->fetch("SHOW COLUMNS FROM `$tbl`");
			$output = array() ;
			foreach($columns as $cl) {
					$output[$cl['Field']] = strtoupper( preg_replace('/\(.+\)/','',$cl['Type'] ) );
			}
			$this->columns[$tbl] = $output ;
			return $output ;
		}
		if ($this->pdo_type == 'sqlite'){
			$columns = $this->fetch("PRAGMA table_info($tbl)");	
			$output = array();		
			foreach($columns as $cl){
				$output[$cl['name']] = strtoupper( preg_replace('/\(.+\)/','',$cl['type'] ) );
			}
			$this->columns[$tbl] = $output ;
			return $output ;
		}
		die("Db::ctypes($tbl) : db pdo type get fields not handled ". PDO_TYPE );
	}
	static function strpos_arr($haystack, $needle) {	if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) { 	if(($pos = strpos($haystack, $what))!==false) return $pos;}	return -1;
	}

	public $types = array() ;

	function build($type,$tbl,$pairs  = array(),$id=0) {
		$types_int = array('BOOLEAN' ,'BIT' ,'INTEGER','FLOAT','NUMERIC' ,'REAL','DOUBLE','DECIMAL') ;
		$sql_pairs 		= array() ;
		$sql_values		= array() ;
		$pairs_txt 		= array() ;
		$pairs_int 		= array() ;
		if (!isset($this->types[$tbl])) {
			$this->types[$tbl] = $this->ctypes($tbl);
		}
		foreach($pairs as $key=>$val){
			if(self::strpos_arr( $this->types[$tbl][$key] , $types_int ) == 0){
				$pairs_int[$key] = $val ;
			}else{
				$pairs_txt[$key] = $val ;
			}
		}
		if ($type == 'UPDATE'){
			$sql = 'UPDATE `'.$tbl.'` SET ' ;
			foreach($pairs_txt as $key=>$val){$sql_pairs[] = '`'.$key.'`='.self::v2txt($val);}
			foreach($pairs_int as $key=>$val){$sql_pairs[] = '`'.$key.'`='. self::v2int($val);}
			$sql .=  join($sql_pairs,','). ' WHERE id = '.$id ;
		}elseif($type=='INSERT'){
			$sql = 'INSERT INTO `'.$tbl .'`' ;
			foreach($pairs_txt as $key=>$val){$sql_pairs[] = '`'.$key.'`'; $sql_values[]=self::v2txt($val);}
			foreach($pairs_int as $key=>$val){$sql_pairs[] = '`'.$key.'`'; $sql_values[]=self::v2int($val);}
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