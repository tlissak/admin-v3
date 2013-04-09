<?
class AdminTable extends AdminRelation {

	public $db = null;
	
	public $show = 1 ; // in Menu
	
	public $view = array('id'=>'#');	
	public $name = '';
	public $fields = '';
	
	public $data = array();
	
	public $initializedList = false;

	public $callback_return_type = 0 ;
		
	public $fld_title ;
	
	public $form ;
	
	function initData(){			foreach($this->fields as $k){	$this->data[$k]	 = '';		}		}	
	function initPostData(){	foreach($this->fields as $k){	$this->data[$k]	 = post($k);	}	}
	function initDbData(){	
		if ($this->id < 1){ return ;}	
		$this->initListSql();
		$sql = $this->getRowSql()  ;
		$res = $this->db->fetchRow($sql);
		if(count($res)){$this->data = $res; }else{	$this->id = 0; }
	}
	
	public function __construct($name){
		global $db;
		$this->db = $db ;
		$this->id 		=  (int)(post('id')) ? (int)(post('id')) :   (int)(get('id'));	 
		$this->name = $name ;
		$this->fields = array_filter(array_keys( $this->db->ctypes( $this->name ) ), create_function('$kyes','return $kyes!="id";')) ;
	}
		
	public function Add(){
		$this->initPostData() ;	
		$this->db->q(SQL::build('INSERT',$this->name,$this->data) ) ;
		$this->id = $this->db->last_id() ;
		$this->deleteRelations() ;
		$this->addRelations() ;	
	}
		
	public function Edit(){		
		$this->initPostData() ;	
		if ($this->id < 0) return ; 
		$this->db->q(SQL::build('UPDATE',$this->name,$this->data,$this->id)) ; 	
		$this->deleteRelations() ;
		$this->addRelations() ;
	}
	
	public function Delete(){
		$this->initPostData() ;	
		$this->db->q('DELETE  FROM `'.$this->name.'` WHERE id = '. $this->id) ;
		$this->deleteRelations() ; 
		$this->id =  0 ; 
	}
}
?>