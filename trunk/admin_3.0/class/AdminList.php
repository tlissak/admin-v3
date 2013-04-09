<?

class AdminList  extends PagingList   {
		
	public $list = array() ;
	public $initializedList = false ;
	public $initializedListSql = false ;
	
	public $sql =  '';
	
	public $filter_val = array() ;
	public $order_val = array();
		
	public $sqlParam = '' ;
	
	public function getPage(){ 		return (int)get('page');		}
	
	public function initList(  $PAGE_SIZE_LIMIT  = 0 ){	
		if ($this->initializedList ) return ;	$this->initializedList = true; //protect		
		$this->initListSql();		
		if ($PAGE_SIZE_LIMIT  == 0){ $PAGE_SIZE_LIMIT = 100 ; } 
		$this->_list = $this->getListRows($PAGE_SIZE_LIMIT);
		$this->initListPaging($PAGE_SIZE_LIMIT); 
	}
	
	public function initListSql(){
		
		if ($this->initializedListSql ) return ;	$this->initializedListSql = true; //protect		
		
		$this->sql_fields 				=	$this->name .'.* ';
		$this->sql_extra_fields 	= '' ;
		$this->sql_tables 			= ' `'.$this->name.'` ' ;		
		$this->sql_order				= "" ;			
		$this->sql_param 			= "" ;
		
		foreach($this->relations as $rel){			
			if($rel->keys['type'] == RelationType::Simple 	|| $rel->keys['type'] == RelationType::InnerSimple ){
					$inner_sql = ','. $rel->name.'_ljoin.'.$rel-> fld_title .' AS '.$rel->keys['left_key'] . '_inner ' ;
					$this->sql_left_joins .= ' LEFT JOIN '. $rel->name . ' AS '.$rel->name .'_ljoin ON '.$rel->name .'_ljoin.id = '. $this->name .'.'.  $rel->keys['left_key']  .' ' ;
					$this->sql_fields .= $inner_sql ;	
			}
		}
		
		if ( count($this->filter_val  )>0 ) {	
			$this->sql_param .= $this->getListSqlFilter( ) ;
		}
		if ( count($this->order_val) >0 ) {	
			$this->sql_order = $this->getListSqlOrder( ) ;
		}
		
		if ($this->sqlParam ){
			$this->sql_param .= $this->sqlParam ;
		}
	}
	
	
	function getListSqlFilter(){		
		$out = '' ;
		$found = false;
		foreach($this->filter_val as $fld=>$query ){
				$rf = false;
				foreach($this->relations as $r){ 	 /* so he has relation values*/
					if($r->keys['left_key'] == $fld ){ $rf = true;	break ;		}
				}
				if ($rf){		$val = ' = '. SQL::v2int($query)  ;
				}else{		$val  = ' LIKE ' .SQL::v2txt($query .'%') ;			}				
				$out .= ($found ? ' AND ' : ' WHERE ') . '`'.$this->name.'`.' . $fld . $val ;
				$found = true;
		}	
		return $out;
	}
	function getListSqlOrder(){
		$ha1 = ' ORDER BY ';
		$found = false ;
		foreach($this->order_val as $fld=>$dir){
				$ha1 .= ($found ? ',' : '' ) . $fld . ' ' .$dir;
				$found = true;
		}
		return $ha1;
	}
}

?>