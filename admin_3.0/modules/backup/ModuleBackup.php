<?
class ModuleBackup{	
	
	function dispacher(){
			if (get('backup')==1){
				$this->backup();
				die ;
			}
	}	

	public function backup(){		
		ini_set('max_execution_time', 100);
		set_time_limit(100);	
		
		// other database type that based file wil be zipped 
		if (PDO_TYPE == 'mysql'){ 
			$db_back = new DbBackup(PDO_DSN,PDO_DB,PDO_USER,PDO_PASS) ;
			$backup = $db_back->backup();
			if(!$backup['error']){
				$dst =  time() . "_". PDO_DB.".sql"   ;
				$fp = fopen(P_BACKUP . $dst,'a+');
				fwrite($fp, $backup['msg']);
				fclose($fp); 				//the dump file will ziped				//header('Location: '.U_BACKUP . $dst) ;
			} else {
				echo 'An error has ocurred. '. $backup['msg'];
			}
		}
		$dst =  time() . "_". basename('site_backup.zip')  ;
		new FileBackup(P_BASE, P_BACKUP . $dst ) ;
		header('Location: '.U_BACKUP . $dst) ;
	}
	
}
?>