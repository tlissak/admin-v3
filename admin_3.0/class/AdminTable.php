<?
class AdminTable extends AdminRelation {

	public $db = null;
	
	public $name = '';
	public $fields = array();
	
	public $data = array();
	public $dataRelation = array();
	
	public $initializedList = false;

	
	function initData(){			foreach($this->fields as $k){	$this->data[$k]	 = '';		}		}	
	function initPostData(){	foreach($this->fields as $k){	$this->data[$k]	 = post($k);	}	}
	function initDbData(){	
		if ($this->id < 1){ return ;}	
		$this->initListSql();
		$sql = $this->getRowSql()  ;
		$res = $this->db->fetchRow($sql);
		if(count($res)){$this->data = $res; }else{	$this->id = 0; fb('unable to get object row data'); }
	}	
	function initDbRelationData(){
		foreach ($this->relation as $k=>$v ) {
			$k_ids = array() ;
			if ($v['type'] ==  RelationType::ManyToMany ||  $v['type'] ==  RelationType::ManyToManySelect){
				$sql = 'SELECT `'.$v['left_key'] .'` AS k_id FROM `'. $v['by_tbl'] . '` WHERE `'.$v['right_key'].'` = ' . $this->id  ;
				$keys = $this->db->fetch($sql);
				foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}
			}elseif ($v['type'] ==  RelationType::ManyToOne ){
				$sql = 'SELECT id AS k_id FROM `'.$v['tbl'] .'` WHERE `'. $v['left_key'] .'` =  ' . $this->id ;
				$keys = $this->db->fetch($sql);
				foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}
			}
			$this->dataRelation[$v['tbl']]	 = $k_ids  ;
		}
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
		$this->db->q(SQL::build('UPDATE',$this->name,$this->data,$this->id)) ; 	
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