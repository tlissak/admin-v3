<?php
class DbBackup {

	private $user;
	private $password;
	private $dsn;
	private $tables = array();
	private $handler;
	private $error = array();
	private $final;

	public function DbBackup($DSN,$DBNAME,$USER,$PASS){		
		$this->user =$USER;
		$this->password = $PASS;
		$this->dbName = $DBNAME;
		$this->final = '-- date ' .date('Y-m-d H:i:s').";\n\n";
		$this->final .= 'CREATE DATABASE ' . $this->dbName.";\n\n";
		$this->dsn = $DSN ;
		$this->connect();
		$this->getTables();
		$this->generate();
	}

	public function backup(){ 
		if(count($this->error)>0){
			return array('error'=>true, 'msg'=>$this->error);
		}
		return array('error'=>false, 'msg'=>$this->final);
	}

	private function generate(){
		foreach ($this->tables as $tbl) {
			$this->final .= '--CREATING TABLE '.$tbl['name']."\n";
			$this->final .= $tbl['create'] . ";\n\n";
			$this->final .= '--INSERTING DATA INTO '.$tbl['name']."\n";
			$this->final .= $tbl['data']."\n\n\n";
		}
		$this->final .= '-- THE END'."\n\n";
	}


	private function connect(){
		try {
			$this->handler = new PDO($this->dsn, $this->user, $this->password);
		} catch (PDOException $e) {
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}

	private function getTables(){
		try {
			$stmt = $this->handler->query('SHOW TABLES');
			$tbs = $stmt->fetchAll();
			$i=0;
			foreach($tbs as $table){
				$this->tables[$i]['name'] = $table[0];
				$this->tables[$i]['create'] = $this->getColumns($table[0]);
				$this->tables[$i]['data'] = $this->getData($table[0]);
				$i++;
			}
			unset($stmt);
			unset($tbs);
			unset($i);

			return true;
		} catch (PDOException $e) {
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}

	private function getColumns($tableName){
		try {
			$stmt = $this->handler->query('SHOW CREATE TABLE '.$tableName);
			$q = $stmt->fetchAll();
			$q[0][1] = preg_replace("/AUTO_INCREMENT=[\w]*./", '', $q[0][1]);
			return $q[0][1];
		} catch (PDOException $e){
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}


	private function getData($tableName){
		try {
			$stmt = $this->handler->query('SELECT * FROM '.$tableName);
			$q = $stmt->fetchAll(PDO::FETCH_NUM);
			$data = '';
			foreach ($q as $pieces){
				foreach($pieces as &$value){
					$value = htmlentities(addslashes($value));
				}
				$data .= 'INSERT INTO '. $tableName .' VALUES (\'' . implode('\',\'', $pieces) . '\');'."\n";
			}
			return $data;
		} catch (PDOException $e){
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}
}
?>