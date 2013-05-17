<?

interface PostAction {
	  const UNSETD 	= 0	;
	  const ADD 			= 1	;
	  const MOD 		= 2	;
	  const DEL 			= 3	;
	  const DUP 			= 4	;
}


class AdminController{	
	public static $_PREF = array() ;
	public static $_PREF_DEF = array(	'body_dir'=>'ltr' ) ;
	public static $_LNG = array() ;
	public static $_LNG_DEF 		= array(
					'backup'=>'Sauvgarde','logout'=>'Déconnxion','login'=>'Connexion',
					'save'=>'Enregistrer','new'=>'Nouveau',	'duplicate'=>'Dupliquer','delete'=>'Supprimer',
					'browse or drop file here'=>'Parcourir ou placer isi une image','user name'=>'Utilisateur','password'=>'Mot de passe',
					'sort direction A first'=>'Par ordre croisent','sort direction Z first'=>'Par ordre decroisent','results'=>'Resultats',' of '=>' sur '
					,'page'=>'Page','pages'=>'pages' , 'prev'=>'Précédente' ,'next'=>'Suivante'
	);
	/**
	 * return Global Preferences 
	 * @param $k key
	 * @return multiple (int, string, array, object)
	 */
	public static function PREF($k){
		return  (isset(static::$_PREF[$k]) ? static::$_PREF[$k] : (isset(static::$_PREF_DEF[$k]) ? static::$_PREF_DEF[$k] : '' ) );
	}
	public static $tableInstances = array();		

	public $action 	= PostAction::UNSETD ;	
	
	/**
	 * $PAGE_SIZE to be override  to give per site configuration
	 * @var mixed
	 */
	public $PAGE_SIZE = 30 ;
	
	//no usage to be removed ?	
	//public $callback = array();
	
	private $table ; 	
	
	/**
	 * $contextTable == new Table(global $contextTable = get('contexttbl'))
	 * @var Table
	 */
	public $contextTable = null ;  
	
	/**
	 * The Main function 
	 * Dispatch all uri / post requests to his propose handler
	 */
	function dispacher(){
		
		global $tbl ;
		global $ctrl ;
		global $contexttbl  ;
			
			if (get('browse') == 1){
				$out = new FileBrowser( get('path') );
				header('Content-type: application/json');
				echo json_encode($out) ;
				die ;
			}
			if (get('set_sql') == 1){
				$sql = trim(get('sql') );
				$out = array('sql'=> $sql ) ; 
				if (stripos($sql,'SELECT')  !== 0){
					$out['error'] = 'QueryNotAllowed' ;
				}else{
					global $db ;
					$out['list'] = $db->fetch($sql)	 ;
					$out['error'] = $db->last_error ;
				}
				header('Content-type: application/json');
				echo json_encode($out) ;
				die; 	
			}

			if (get('delete_file')==1){
				header('Content-type: application/json');
				$file = get('file') ;
				@unlink(P_PHOTO. $file);
				echo json_encode( "OK" ) ;
				die ;
			}
			
			if (get('upload')==1){
				header('Content-type: application/json');
				$fld = get('fld') ;
				if ($contexttbl && $tbl != $contexttbl)
					$p_surfix = $tbl . '_' . $contexttbl .'_'.  $fld . DS ;
				else
					$p_surfix = $tbl .'_'. $fld. DS ;
				if (! is_dir(P_PHOTO . $p_surfix)){ 		mkdir(P_PHOTO . $p_surfix); }				
				echo json_encode( new Upload($p_surfix) ) ;
				die ;
			}
			
			if (get('backup')==1){
				$this->backup();
				die ;
			}
			
			// form + ajax + relations
			
			if ($tbl && isset(self::$tableInstances[$tbl])){	
				
				$this->table = &self::$tableInstances[$tbl];	
				$this->table->initRelationsObject();
				
				if ($contexttbl && $this->table->name != $contexttbl ){
						$this->contextTable = &$this->table->relations[$contexttbl] ;
						$this->contextTable->initRelationsObject();	
				}else{
						$this->contextTable = &$this->table ;	
				}
					

				if(get("get_list_ajax")){
					
					$this->contextTable->getFilterOrderParam() ;	
					if ($this->contextTable->name == $this->table->name){
						$this->contextTable->initList($ctrl->PAGE_SIZE);
						$this->contextTable->setSelectedByValue($this->contextTable->id) ;
					}else{
						
						$this->table->initDbData() ;
						$this->table->initDbRelationData();
						/*remove inutile relations  */
						foreach($this->table->relations as $r_name=>$r_obj){
							if ($r_obj->keys['name'] != $contexttbl ){
								unset($this->table->relations[$r_name]); 
							}
						}						
						$this->table->initRelations() ; // so the contextTable->initList() will called
					}
					
					$out = array('list'=>$this->contextTable->getTableBody() ,'paging'=>$this->contextTable->getTablePaging());
					header('Content-type: application/json');
					echo json_encode($out);	
					die ;	
					
				}elseif(get('set_form_ajax') ){
					
					if (post('postback')){					$this->action = PostAction::UNSETD ; 		}
					if (post('form_submit_action_type') == 'add'){			$this->action = PostAction::ADD ; 			}
					if (post('form_submit_action_type') == 'mod'){			$this->action = PostAction::MOD ; 			}
					if (post('form_submit_action_type')	== 'del'){ 			$this->action = PostAction::DEL ;			}
					if (post('form_submit_action_type')	== 'dup'){ 			$this->action = PostAction::DUP ;			}
					
					if ($this->action 			==	PostAction::UNSETD){ 			_die('What ? ');		}
					
					$callback = array();
					$callback['title']				= post($this->contextTable->fld_title) ; // verify when deleting
					$callback['id']  					= $this->contextTable->id ;
					$callback['contexttbl']  	= $contexttbl ;
					$callback['tbl']  				= $this->contextTable->name ;
					
					
					
					if ($this->action 			==	PostAction::ADD ){
							$this->contextTable->Add() ;
							$callback['action'] 	= 'add' ;
							$callback['id']  			= $this->contextTable->id ; //last inserted id
							$this->contextTable->initDbData() ; 
							$callback['tr']  = $this->contextTable->getTableRowById($this->contextTable->id) ;
					}elseif($this->action 			==	PostAction::DUP){
							$this->contextTable->Dup() ;
							$callback['action'] 	= 'dup' ;
							$callback['id']  			= $this->contextTable->id ; //last inserted id
							$this->contextTable->initDbData() ; 
							$callback['tr']  = $this->contextTable->getTableRowById($this->contextTable->id) ;
					}elseif($this->action 	==	PostAction::MOD){
							$this->contextTable->Edit() ;
							$this->contextTable->initDbData() ; 
							$callback['action'] 	= 'mod' ;
							$callback['tr']  = $this->contextTable->getTableRowById($this->contextTable->id) ;
					}elseif($this->action	 ==	PostAction::DEL){
							$this->contextTable->Delete() ;
							$callback['action'] 	= 'del' ;
					}
					
					header('Content-type: application/json');
					echo json_encode($callback);	
					die ;
				}elseif(get('menu')=='1'){
					$this->contextTable->initData() ;
					$this->contextTable->initRelations() ;
					$this->action	= 'add' ;
					$this->contextTable->initList($ctrl->PAGE_SIZE)  ; // even if it calles by ajax page size limit should not requires a lot of resource 
					$this->contextTable->setSelected(0) ;		
				}else{ 					
					$this->contextTable->initData() ;
					$this->contextTable->initDbData() ;
					$this->contextTable->initDbRelationData();
					$this->contextTable->initRelations() ;
					$this->action	= $this->contextTable->id>0 ? 'mod' : 'add' ;
					$this->contextTable->initList($ctrl->PAGE_SIZE)  ; // even if it calles by ajax page size limit should not requires a lot of resource 
					$this->contextTable->setSelectedByValue($this->table->id) ; 
				}
		}
	}
	
	
	/**
	 * match user/pass pairs against the users lists 
	 * @param $username
	 * @param $password
	 * @return true / false
	 */	
	public function login($username,$password){
		foreach(Ctrl::$_USERS as $user=>$pass){
			if ($username == $user && $password == $pass){	return true ;		}
		}
		return false ;	
	}
	
	//TODO  add bans ips system
	/**
	 * Login requests handler
	 * will set the $cookie->auth or will show the login form
	 */
	public function initAuth(){		
		global $cookie ;
		if (get('logout') == '1'){
			$cookie->auth = false ;
			header('Location: index.php?is_logout=1');
			die;	
		}
		if (post("postback") == "login" && $this->login(post('auth_user') , post("auth_pass") )  ){	
		 	$cookie->auth = true ;
		}
		if ($cookie->auth != true){ 	echo $this->getFormLogin(); 	die ; }
	}
	
	/**
	 * Backup requests handler
	 */
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
	
	/**
	 * @return Html string of the user form
	 */
	function getFormLogin(){
		$lform = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Admin Login</title>
		<style>* { font-family:Arial, Helvetica, sans-serif; font-size:12px;}
		label,form{ display:block;}
		form{margin:10px auto; width:300px;}
		</style></head><body  dir="'. self::PREF('body_dir') .'">
		<form method="post" >
		<label>'.l('user name').'</label>
		<input type="text" name="auth_user" value="'.post('auth_user').'" />
		<label>'.l('password').'</label>
		<input type="password" name="auth_pass" value="'.post('auth_pass').'" />
		<p class="submit"><button type="submit" class="btn-blue" name="postback" value="login">'.l('login') .'</button></p>
		</form></body></html>';
		return $lform;
    }
}

?>