<?php
class Db{
	public $db ;
	public $errors = array() ;
	public $querys = array() ;
	public $last_error =  "" ;
	private $pdo_type  ;
	private $pdo_dsn  ;
	function __construct($p_dsn = PDO_DSN , $p_type= PDO_TYPE ){
		$this->pdo_type 	= $p_type;
		$this->pdo_dsn 	= $p_dsn;
		try {
			$p_user = defined("PDO_USER") ? PDO_USER : "" ;
			$p_pass = defined("PDO_PASS") ? PDO_PASS : "" ;
			$this->db = new PDO( $p_dsn,$p_user,$p_pass);	
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			if ($this->pdo_type == 'mysql'){	$this->db->query("SET NAMES 'utf8'");} //$this->db->exec("set names utf8");
			return ;	
		}catch (PDOException $err) {
			die('Unable to connect to database');
		}
	}
	function __destruct(){
		$this->db = null ;	
	}
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
				if ($this->pdo_type == 'odbc') array_walk($res,'Db::iso2utf8' );	
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
			$sth->execute();
			if (strrpos($q,'INSERT' ) === 0)
				return $this->db->lastInsertId() ;
			return true ;	
		}else{
			$this->error($this->db->errorInfo() , $q) ;
			return false;	
		}		
	}
	function ctypes($tbl) {
		if (PDO_TYPE == 'mysql'){
			$columns = $this->fetch("SHOW COLUMNS FROM `$tbl`");
			$output = array() ;
			foreach($columns as $cl) {
					$output[$cl['Field']] = strtoupper( preg_replace('/\(.+\)/','',$cl['Type'] ) );
			}
			return $output ;
		}
		if (PDO_TYPE == 'sqlite'){			
			$columns = $this->fetch("PRAGMA table_info($tbl)");	
			$output = array();		
			foreach($columns as $cl){
				$output[$cl['name']] = strtoupper( preg_replace('/\(.+\)/','',$cl['type'] ) );
			}
			return $output ;
		}
		die("Db::ctypes($tbl) : db pdo type get fields not handled ". PDO_TYPE );
	}
}


?>