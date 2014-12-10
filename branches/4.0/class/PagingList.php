<?

class PagingList {
	
	public $_list  			= array() ;	
	
	public $debug_sql 			= false;
	public $debug_list   			= false;
	
	public $num_results 		= -1;
	
	public $page			 		= 0 ;
	public $offset 					= 0 ;	
	public $pages , $pages_range_start  ,	$pages_range  ;
	public $pages_show_first  , $pages_show_last 	, $pages_show_next , $pages_show_prev = false;
	
	public $results ;
	public $results_start = 0 ;
	public $results_end = 0 ;
	public $sql_fields 	= '';
	public $sql_extra_fields = '' ;
	public $sql_tables 	= '' ;
	public $sql_inner_joins  = '';
	public $sql_left_joins  = '';
	public $sql_order	= '' ;
	public $sql_param = '';
	public $sql_limit = '';
	public $sql_count = '';
	public $sql_rows = '';
	
	
	public function getPage(){
			return (int)get("page") ;
	}
	
	public function initOffset($page_size){
		if ( ($this->page 	= $this->getPage()) > 0){
			 $this->offset = $page_size * $this->page ;
		}else{
			$this->page = 0;
			$this->offset = 0;
		}
	}
	
	public function getRowSql(){
		 $this->sql_rows=  'SELECT '
								. $this->sql_fields 
								. $this->sql_extra_fields 
								. ' FROM '
								. $this->sql_tables 
								. $this->sql_left_joins
								. $this->sql_inner_joins
								. ' WHERE `'.$this->name.'`.id = '.$this->id  ;	
		return $this->sql_rows ;
	}
	
	public function getListRowsSql($page_size){		
		$this->initOffset($page_size);		
		 $this->sql_rows=  'SELECT '
								. $this->sql_fields ."\r\n"
								. $this->sql_extra_fields ."\r\n"
								. ' FROM '
								. $this->sql_tables ."\r\n"
								. $this->sql_left_joins ."\r\n"
								. $this->sql_inner_joins ."\r\n"
								. $this->sql_param  ."\r\n"
								. $this->sql_order  ."\r\n"
								. ($this->sql_limit ? $this->sql_limit : ' LIMIT ' . $this->offset. ','. $page_size) ."\r\n" ;
		return $this->sql_rows ;
	}
	
	public function getListCountSql(){
		$this->sql_count = 'SELECT COUNT(*) AS cn ' ."\r\n"
								. $this->sql_extra_fields  ."\r\n"
								. ' FROM '
								. $this->sql_tables ."\r\n"
                                . $this->sql_left_joins ."\r\n"
								. $this->sql_inner_joins ."\r\n"
								. $this->sql_param  ; 
		return $this->sql_count ;
	}


	public function getListRows($page_size){
		$list = $this->db->fetch( $this->getListRowsSql($page_size) );		
		if ($this->db->last_error != "" ){
			Debug("Sql list contains errors : " .$this->db->last_error ) ;
			return array();
		}
		return $list;
	}
	
	public function initListPaging($page_size){	
		
		$this->initOffset($page_size);
		$count_sql =  $this->getListCountSql($page_size)  ;
		$list_count = $this->db->fetchRow($count_sql);
		if (count($list_count) == 0 ) {
			Debug("Sql count contains errors " . $this->db->last_error  /*$this->getListCountSql($page_size)*/) ;
			return ;
		}
				
		$this->num_results = (int)($list_count["cn"]) ;

		$this->pages =  ceil($this->num_results / $page_size ) ;
				
		$start = ($this->offset+1);
		$end = ($this->offset == 0) ?  $page_size : $page_size * ($this->page +1  )  ;
				
				
		if ($end > $this->num_results) {
			$end = $this->num_results;
		}
		$this->results =  $start  . ' - '. $end ;
		$this->results_start = $start ;
		$this->results_end = $end ;
				
		$this->pages_range_start 		= 0 ;
		$this->pages_range 				= $this->pages ;
				
		if ($this->pages > 7 ){
			if ($this->page < 7){
					$this->pages_range 			= $this->page + 4 > 7 ? $this->page + 4 : 7 ;
					$this->pages_range_start	= $this->page - 3 > -1 ?  $this->page - 3 : 0  ;
					$this->pages_show_last 					= true;
			}elseif($this->pages - $this->page > 4 ){
					$this->pages_range 			= $this->page + 4 ;
					$this->pages_range_start	= $this->page - 3 ;
					$this->pages_show_first 					= true;
					$this->pages_show_last 					= true;
			}else{
					$this->pages_show_first 					= true;
					$this->pages_range 			= $this->pages ;
					$this->pages_range_start	= $this->page -3 ;
			}
		}
		$this->pages_show_next =  ! ($this->page+1 >= $this->pages) ;
		$this->pages_show_prev =  ! ($this->page-1 < 0) ;		
	}	
}
?>