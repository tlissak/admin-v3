<?

interface PostAction {
	  const UNSETD 	= 0	;
	  const ADD 			= 1	;
	  const MOD 			= 2	;
	  const DEL 			= 3	;
	  const DUP 			= 4	;
}


class AdminController{
	public static $_ALLOWED_IPS = array();
	
	public static $_PREF = array() ;
	public static $_PREF_DEF = array(	'body_dir'=>'ltr' ) ;
	public static $_LNG = array() ;
	public static $_LNG_DEF 		= array(
					'backup'=>'Sauvgarde','logout'=>'Déconnection','login'=>'Connexion',
					'save'=>'Enregistrer','new'=>'Nouveau',	'duplicate'=>'Dupliquer','delete'=>'Supprimer',
					'browse or drop file here'=>'Parcourir ou placer ici une image','user name'=>'Utilisateur','password'=>'Mot de passe',
					'sort direction A first'=>'Par ordre croissant','sort direction Z first'=>'Par ordre décroissant','results'=>'Resultats',' of '=>' sur '
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
		global $module;	
			
			$module->dispachers();
			

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
				if ($contexttbl && $tbl  && $tbl != $contexttbl)
					$p_surfix = $tbl . '_' . $contexttbl .'_'.  $fld . DS ;
				elseif ($tbl && $fld)
					$p_surfix = $tbl .'_'. $fld. DS ;
				else 
					$p_surfix = $fld. DS ;
				if (! is_dir(P_PHOTO . $p_surfix)){ 		mkdir(P_PHOTO . $p_surfix); }				
				echo json_encode( new Upload($p_surfix) ) ;
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
					
					
					if ($this->contextTable->protected){
						$this->action 			=	PostAction::UNSETD ;
					}
					
					if ($this->action 			==	PostAction::UNSETD){ 			_die('PostAction::UNSETD or no permissions'); fb("Died ?");		}
				
					
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

					$this->contextTable->getFilterOrderParam() ;
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
	//TODO token check
	/**
	 * Login requests handler
	 * will set the $cookie->auth or will show the login form
	 */
	static function generate_token(){ 	return hash('sha256',_RIJNDAEL_KEY_ .P_SCRIPT);	}
	static function validate_token($_token){	return ( strcmp($_token, self::generate_token())==0)  ;	}
	
	public function initAuth(){
		
		global $cookie ;		
		
		if ( count(Ctrl::$_ALLOWED_IPS) ) {		
			foreach(Ctrl::$_ALLOWED_IPS as $ip){
				if (strpos($ip,'-') > 0){					
					list($begin, $end) = explode('-', $ip);
					$begin = ip2long($begin);					
					$end = ip2long($end);					
					$_ip = ip2long(IP);					
					if ($_ip >= $begin && $_ip <= $end){
						return true ;	
					}
				}else{
					if ($ip == IP){
						return true ;	
					}
				}
			}
		}
		
		if (get('_token')){
			if (AdminController::validate_token(get('_token'))){
				return true ;
			}
		}		
		if (get('logout') == '1'){
			$cookie->auth = false ;
            $cookie->write();
			header('Location: index.php?is_logout=1');
			die;	
		}
		if (post("postback") == "login"){
			if ($this->login(post('auth_user') , post("auth_pass") )  ){	
				$cookie->auth_user = post('auth_user') .'-' . time() .'-' .Ctrl::$_USERS[post('auth_user')]  ;
				$cookie->auth = true ;
                $cookie->write();
			}else{
				mail('tlissak@gmail.com','Login attempt failed ' 			
				, 'Host : '.U_HOST . ' Client IP : '.IP . ' Coords:['. post('auth_user') .'/' .post("auth_pass") .']'
				,_EMAIL_HEADER_  );
			}
		}

		if ($cookie->auth == true){ // == true can be == 1
            return true ;
        }

        echo $this->getFormLogin();
        die ;
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