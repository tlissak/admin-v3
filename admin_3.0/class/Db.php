<?php
class Db{
	public $db ;
	public $errors = array() ;
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
	function error($e,$q){
			$er  = (implode(', ',$e) .' :: '. $q );
			$this->last_error = $er ;	
			$this->errors[] = $er;
			DebugError('Db', $er ) ;
	}
	function fetch($q){
		DebugSql($q);
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
		DebugSql($q);
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
		DebugSql($q);
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
			$cols = $this->fetch("SHOW COLUMNS FROM `$tbl`");
			$output = array() ;
			foreach($cols as $cl) {
					$output[$cl['Field']] = strtoupper( preg_replace('/\(.+\)/','',$cl['Type'] ) );
			}
			return $output ;
		}
		if (PDO_TYPE == 'sqlite'){
			$sql = $this->fetchRow("SELECT sql FROM sqlite_master WHERE type='table' AND tbl_name='$tbl'") ;
			$sql = $sql['sql'] ;
			$sql = str_replace("  "," ",str_replace("  "," ",$sql)); //
			$sql = str_replace("\r\n","",$sql);
			$sql = str_replace(',',"\r\n,",$sql);
			$sql = str_replace('(',"\r\n(\r\n",$sql);
			$sql = str_replace(')',"\r\n)",$sql);			
			$_matches = false;
			if (strpos($sql,'[') > 0) {
				preg_match_all("/\[(.+)\] (\w+)?/", $sql  , $_matches, PREG_SET_ORDER);
			}elseif (strpos($sql,'"') > 0){
				preg_match_all("/\"(.+)\" (\w+)?/", $sql  , $_matches, PREG_SET_ORDER); 
			}else{
				p('Error parsing fields from table see $db->ctypes');	
				p($_matches);
				p($sql);
				die();
			}			
			$sres = array();
			if ($_matches){
				foreach($_matches as $m){
					if (count($m) == 3){
						$sres[trim($m[1])] = trim($m[2] );
					}
				}	
			}
			return $sres ;
		}
		die("Db::ctypes($tbl) : db pdo type get fields not handled ". PSO_TYPE );
	}
}


?>