<?
class AdminLoader extends AdminForm{
	
	function verifyImageInput(){	foreach($this->image as $img){		if (is_array($img)){	_die('Image as array is depracted Table : '. $this->name);}	}}
	function verifyRelationInput(){
		foreach($this->relation as $r){
			if (!isset($r['name']) || !isset($r['type'] ))	{		_die('Relation input incorrect name or type not seted Table : '. $this->name);	}
			if (!isset($r['tbl'] ))	{		_die('Relation input incorrect "tbl" not seted for Table : '. $this->name);	}
			if ($r['type'] == RelationType::InnerSimple || $r['type'] == RelationType::Simple){
				if ( !isset($r['left_key'])  ){	_die('Relation input incorrect left_key not seted  Table : '. $this->name);	}
			}elseif($r['type'] == RelationType::ManyToMany || $r['type'] == RelationType::ManyToManySelect){
				if (!isset($r['by_tbl'] )|| !isset($r['left_key'] )|| !isset($r['right_key'] ))	{	_die('Relation input incorrect bytable,left or right key not seted Table : '. $this->name ); 	}
			}elseif($r['type'] == RelationType::ManyToOne || $r['type'] == RelationType::ManyToOneByKey){	
				if ( !isset($r['left_key'] ))	{	_die('Relation input incorrect left_key not seted Table : '. $this->name ); 	}
			}
		}
	}	
	public $relation 	= array() ;
	public $image 		= array() ; 
	public $view 		= false ; // array()
	public $filter 		= false ;
	public $order 		= false ;
	public $fld_title 	= false; // require 
	public $show 		= 1; //in menu
	
	public $options = array('readonly'=>0);
	public $protected = 0 ;
	
	
	public function Image($x ){		$this->image[] = $x 	; 	return $this ; }
	public function View($x ){		$this->view = $x 	; 			return $this ; }
	public function Show($x ){		$this->show = $x 	; 		return $this ; }
	public function FieldTitle($x){  $this->fld_title = $x ; 		return $this; }
	public function Relation($x ){	$this->relation[$x['name']] = $x 	; return $this ; }
	public function AddTableAttr($x , $y){ $this->{$x} = $y; return $this; }
	public function Load($opt_array=array()){		
		if (! $this->name) {				_die("Table name not seted", E_USER_WARNING); return false;}
		if ( ! $this->view){			trigger_error("view not seted table ". $this->name . ' not loaded !', E_USER_WARNING);	return false; }		
		if ( ! $this->fld_title){		trigger_error("fld_title not seted table ". $this->name , E_USER_WARNING);	 }			
		if (count($this->image)){		$this->verifyImageInput() ; 	}
		if (count($this->relation)){	$this->verifyRelationInput()  ; }
		$this->options = $opt_array ;
		
		if (isset($this->options['readonly'])  && $this->options['readonly'] ){
			$this->protected = 1 ;
		}
		Ctrl::$tableInstances[$this->name] = &$this ;
		return false ;	
	}
} 

interface RelationType{
	const Simple 					= 0; // product (type.id)  , product_type.id
	const ManyToMany 			= 1; //  product.id , type.id , product_type(product_id,type_id)
	const ManyToOne 			= 2; // product.id , product_type(product_id)
	const ManyToManySelect = 3; //  product.id , type.id , product_type(product_id,type_id) show all type.id to select
	const InnerSimple 			= 4 ;
	const ManyToOneByKey 	= 5 ;
}



?>