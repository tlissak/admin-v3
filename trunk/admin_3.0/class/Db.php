<?php
class Db{
	public $db ;
	public $errors = array() ;
	public $last_error =  "" ;
	private $pdo_type  ;
	private $pdo_dsn  ;
	function __construct($p_dsn = PDO_DSN , $p_type= PDO_TYPE ,$p_user = PDO_USER ,$p_pass = PDO_PASS  ){
		$this->pdo_type =  $p_type;
		$this->pdo_dsn = $p_dsn;
		if ($p_type == 'sqlite'){
			$this->db = new PDO( $p_dsn);	
			return ;
		}
		if($p_type == 'mysql'){
			$this->db = new PDO( $p_dsn,$p_user,$p_pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return ;
		}
		_die('PDO TYPE not defined');
	}
	function __destruct(){
		$this->db = null ;	
	}
	function fetch($q){	
		$this->last_error = "" ;
		DebugSql($q);
		if ($sth = $this->db->prepare($q) ){
			$sth->execute();	
			if ($res =  $sth->fetchall(PDO::FETCH_ASSOC)){
				return $res ;			
			}else{
				return array() ;
			}
		}else{
			$error =  implode($this->db->errorInfo(),', ') .' : '.  $q ;
			$this->last_error = $error ;	
			$this->errors[] = $error;
			return array() ;	
		}
	}
	function fetchRow($q){
		DebugSql($q);
		if ($sth = $this->db->prepare($q)){
			$sth->execute();	
			if ($res = $sth->fetch(PDO::FETCH_ASSOC) ){
				return $res ;
			}else{
				return array() ;	
			}
		}else{
			DebugError('Db', join($this->db->errorInfo(),', ') ) ;
			return array() ;	
		}
	}
	function q($q){
		DebugSql($q);
		$this->db->exec($q);
	}
	function query($q){
		DebugSql($q);
		$this->db->exec($q) ;
		if (strrpos($q,'INSERT' ) > -1)
			return 	$this->last_id() ;			
		return true ;		
	}
	function fields($tbl) {
		return $this->ctypes($tbl);
	}
	function ctypes($tbl) {
		if (PDO_TYPE == 'mysql'){
			$cols = $this->fetch("SHOW COLUMNS FROM $tbl");
			$output = array() ;
			foreach($cols as $cl) {
					$output[$cl['Field']] = strtoupper( preg_replace('/\(.+\)/','',$cl['Type'] ) );
			}
			return $output ;
		}
		if (PDO_TYPE == 'sqlite'){
			$sql = $this->fetchRow("SELECT sql FROM sqlite_master WHERE type='table' AND tbl_name='$tbl'") ;
					$_matches = false;
			preg_match_all("/\[(.+)\] (\w+)?/", $sql['sql'], $_matches, PREG_SET_ORDER);
			$sres = array();
			if ($_matches){
				foreach($_matches as $m){
					if (count($m) == 3){
						$sres[$m[1]] = $m[2]  ;
					}
				}	
			}
			return $sres ;
		}
	}
	function last_id(){		return $this->db->lastInsertId() ;	}
}


?>