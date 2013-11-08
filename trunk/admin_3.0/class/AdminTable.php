<?
class AdminTable extends AdminRelation {
	
	public $debug = false;
	
	public $db = null;
	
	public $name = '';
	public $fields = array();
	public $fields_pairs = array();
	
	public $data = array();	
	public $post_data = array();	

	/**
	 * initialize the $data property to his default state 
	 */
	function initData(){			foreach($this->fields as $k){	$this->data[$k]	 = '';		}		}	
	/**
	 * will initialise the ->post_data array property with the data posted 
	 * Important !!! will initialise only the submited data
	 */
	function initPostData(){ 
		if (count($this->fields) == 0 ) {
			fb('Error retriving fields list from the database DB :');
			fb('checkup for db->ctypes() for table '. $this->name) ;
			die ; 	
		}
		foreach($this->fields as $k){	if (isset($_POST[$k])) { $this->post_data[$k]	 = post($k);	}}
		//Observe to check more unsbmited data !
		if ($this->debug) { fb('fields , fields_pairs, post data   , post_data  :');fb($this->fields); fb($this->fields_pairs); fb($_POST); fb($this->post_data) ; }} 
	/**
	 * initialise the $data property to data from the database
	 * @return
	 */
	function initDbData(){	
		if ($this->id < 1){ return ;}	
		$this->initListSql();
		$sql = $this->getRowSql()  ;
		$res = $this->db->fetchRow($sql);
		if(count($res)){$this->data = $res; }else{	fb('unable to get object row data ' . $this->id . ' '. $this->name .' '. $sql ); $this->id = 0; }
	}	
	/**
	 * minimaliste AdminTable constructior
	 * will init the current 
	 * 1. selected id  
	 * 2. fileds
	 * 3. db reference
	 * @param $name is database table name 
	 */
	public function __construct($name){
		global $db;
		$this->db = $db ; // should use & by ref for performences ?
		$this->id 		=  (int)(post('id')) ? (int)(post('id')) :   (int)(get('id'));	 
		$this->name = $name ;
		$this->fields_pairs = $this->db->ctypes( $this->name ) ;
		$this->fields = array_filter(array_keys($this->fields_pairs  ), create_function('$kyes','return $kyes!="id";')) ;
	}
	/**
	 * Will Add submited row to the database
	 * also will add relations rows 
	 */
	public function Add(){
		$this->initPostData() ;	
		if ($this->id = $this->db->query(SQL::build('INSERT',$this->name,$this->post_data) ) ){
			$this->deleteRelations() ;
			$this->addRelations() ;
		}else{
			Debug('Post add db error '. $this->db->last_error);
		}
	}
	/**
	 * Will duplicate database row by id
	 * also will dupplicate relations rows 
	 */
	public function Dup(){
		$this->initData(); //empty for fields keys only
		$this->initDbRelationData() ;
		if ($this->id = $this->db->query(SQL::build('DUPLICATE',$this->name,$this->data,$this->id) ) ){
			$this->addRelations( true ) ;	
		}else{
			 Debug('Post duplicate db error '. $this->db->last_error);
		}		
	}
	/**
	 * Will save submited data to database row 
	 * will affect changes in relations
	 * @return
	 */
	public function Edit(){	
		$this->initPostData() ;	
		if ($this->id < 0) return ; 
		if ($this->db->query(SQL::build('UPDATE',$this->name,$this->post_data,$this->id) ) ){
			$this->deleteRelations() ;
			$this->addRelations() ;	
		} else{
			 Debug('Post edit db error '. $this->db->last_error);
		}
	}	
	/**
	 * Will remove permently the row from the database also will delete  relations data
	 */
	public function Delete(){
		if ($this->db->query('DELETE  FROM `'.$this->name.'` WHERE id = '. $this->id) ){
			$this->deleteRelations() ; 	
		}else{
			 Debug('Post delete db error '. $this->db->last_error);
		}
		$this->id =  0;
	}
}
?>