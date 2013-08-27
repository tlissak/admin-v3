<?
class ModuleBackup{	
	
	function includer(){
		return 'UI.backup.js';
	}
	
	function dispacher(){
			if (get('backup')==1){
				$this->backup(); 	die ;
			}
	}

	public function backup(){
		echo  'Backup Process Start please wait ..<br />'		 ;
		ini_set('max_execution_time', 100);
		set_time_limit(100);	
		
		// other database based file (sqlite) includs in archive file backup
		if (PDO_TYPE == 'mysql'){ 
			$db_back = new DbBackup(PDO_DSN,PDO_DB,PDO_USER,PDO_PASS) ;
			$backup = $db_back->backup();
			if(!$backup['error']){
				$dst =  time() . "_". PDO_DB.".sql"   ;
				$fp = fopen(P_BACKUP . $dst,'a+');
				fwrite($fp, $backup['msg']);
				fclose($fp); 				//the dump file will ziped in archive file backup
				echo  'Backup database sucess ..<br />'		 ;
			} else {
				echo 'An error has ocurred. '. $backup['msg'];
			}
		}
		echo  'Files backup start please waite ..<br />'		 ;
		$dst =  time() .'_site_backup.zip'  ;
		new FileBackup(P_BASE, P_BACKUP . $dst ) ;
		
		$file = P_BACKUP . $dst; 
		
		if (file_exists($file) && is_readable($file) )  {			
			echo 'Backup files sucess ..<br /> file will be available in 5sec<br />' ;
			echo 'cron job : <br />http://'.U_HOST . U_URI . '&_token='.  AdminController::generate_token() ;				
			header( "refresh:5;url=".U_BACKUP . $dst ); 
      }
	}	
}
?>