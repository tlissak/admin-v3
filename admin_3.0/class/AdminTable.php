<?
class AdminTable extends AdminRelation {

	public $db = null;
	
	public $name = '';
	public $fields = array();
	
	public $data = array();	
	public $post_data = array();	
	public $initializedList = false;

	
	function initData(){			foreach($this->fields as $k){	$this->data[$k]	 = '';		}		}	
	function initPostData(){	foreach($this->fields as $k){	if (isset($_POST[$k]) /*Apply only the submited data */) { $this->post_data[$k]	 = post($k);	}}} 
	function initDbData(){	
		if ($this->id < 1){ return ;}	
		$this->initListSql();
		$sql = $this->getRowSql()  ;
		$res = $this->db->fetchRow($sql);
		if(count($res)){$this->data = $res; }else{	fb('unable to get object row data ' . $this->id . ' '. $this->name .' '. $sql ); $this->id = 0; }
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
		$this->db->q(SQL::build('INSERT',$this->name,$this->post_data) ) ;
		$this->id = $this->db->last_id() ;
		$this->deleteRelations() ;
		$this->addRelations() ;	
	}
	public function Dup(){
		$this->initData(); //empty for fields keys only
		$this->initDbRelationData() ;
		$this->db->q(SQL::build('DUPLICATE',$this->name,$this->data,$this->id) ) ;
		$this->id = $this->db->last_id() ;		
		$this->addRelations( true ) ;
	}		
	public function Edit(){	
		$this->initPostData() ;	
		if ($this->id < 0) return ; 
		$this->db->q(SQL::build('UPDATE',$this->name,$this->post_data,$this->id)) ; 	
		$this->deleteRelations() ;
		$this->addRelations() ;
	}	
	public function Delete(){
		$this->db->q('DELETE  FROM `'.$this->name.'` WHERE id = '. $this->id) ;
		$this->deleteRelations() ; 
		$this->id =  0 ; 
	}
}
?>