<?
class AdminRelation extends AdminMvc {
	
	public $relation = array(); // Arrays
	
	public $relations = array(); // Object
	
	public $keys  ;
	
	public $initializedRelationsObject= false;
	
	public $initializedOuterRelations = false;
	public $initializedInnerRelations = false;
	
	public $initializedRelations = false; // both outer and inner relations
	
	public $selected = array() ;
	
	public function setSelectedByValue($val = 0 ){
			foreach($this->_list as &$r){  $r['_selected'] =  ($r['id'] == $val) ? 1  : 0; 	}	unset ($r);
	}	
	public function setSelected( $selectedValue){
		foreach($this->_list as &$r){ $r['_selected']	= $selectedValue ;		}	unset($r);
	}
	
	public function initRelations(){ // init both inner and outer relations		
		if ($this->initializedRelations ) return ;	$this->initializedRelations = true; 	//protect
		
		$this->initInnerRelations();
		$this->initOuterRelations();	
	}
	
	public $dataRelation = array();
	public $dataRelationSql = array();
	
	public function initDbRelationData(){
		foreach ($this->relation as $k=>$v ) {
			$k_ids = array() ;

			if ($v['type'] ==  RelationType::ManyToMany ||  $v['type'] ==  RelationType::ManyToManySelect){
				$sql = 'SELECT `'.$v['left_key'] .'` AS k_id FROM `'. $v['by_tbl'] . '` WHERE `'.$v['right_key'].'` = ' . $this->id  ;
				$keys = $this->db->fetch($sql);
				foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}				
				$this->dataRelation[$v['tbl']]	 = $k_ids  ; 
			}elseif ($v['type'] ==  RelationType::ManyToOne ){
				$sql = 'SELECT id AS k_id FROM `'.$v['tbl'] .'` WHERE `'. $v['left_key'] .'` =  ' . $this->id ;
				$keys = $this->db->fetch($sql);
				foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}
				$this->dataRelation[$v['tbl']]	 = $k_ids  ; 
			}elseif ($v['type'] ==  RelationType::ManyToOneByKey ){				
				$sql = 'SELECT id AS k_id FROM `'.$v['tbl'] .'` WHERE ' ;
				$sql .=' `'. $v['tbl'] .'`.`'. $v['left_key'] .'` =  ' . ($this->data[ $v['right_key'] ] ?  $this->data[ $v['right_key'] ] : '0' );
				$keys = $this->db->fetch($sql);
				foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}
				$this->dataRelation[$v['tbl']]	 = $k_ids  ; 
			}
			
		}
	}

	public function initRelationsObject($IS_CALLED_BY_ME_RECURSION_PROTECTION = false){
		
		if ($this->initializedRelationsObject ) return ;	$this->initializedRelationsObject = true; 		//protect
		
		foreach($this->relation as $k=>$v){ 				
				if (!isset(Ctrl::$tableInstances[$v['tbl']])){
					_die('The relation table ['.$v['tbl'].'] not found in the tables collection for relation : '.$k);
				}
				
				$obj							= clone Ctrl::$tableInstances[$v['tbl']]; // allways clone 
				
				if ($v['type'] == RelationType::Simple){							$obj->viewtype = 'EDIT' ;			}
				if ($v['type'] == RelationType::InnerSimple){					$obj->viewtype = 'SELECT-ONE-EDIT' ;			}
				if ($v['type'] == RelationType::ManyToMany){					$obj->viewtype = 'SELECT-EDIT' ;			}
				if ($v['type'] == RelationType::ManyToOne){					$obj->viewtype = 'SELECT-EDIT' ;			}
				if ($v['type'] == RelationType::ManyToOneByKey){			$obj->viewtype = 'SELECT-EDIT' ;			}
				if ($v['type'] == RelationType::ManyToManySelect){		$obj->viewtype = 'SELECT-EDIT' ;			}
				
				$obj->keys				= $v;
				$this->relations[$k] = $obj ;
		}
		
		if (! $IS_CALLED_BY_ME_RECURSION_PROTECTION ){ //recursetions protection
			
			foreach($this->relations as &$r){
				$r->initRelationsObject(true); 
			}
			unset($r);
		}
	}
	
	public function initInnerRelations( $IS_CALLED_BY_ME_RECURSION_PROTECTION = false ){		
		$this->initRelationsObject(); //has protection		
		if ($this->initializedInnerRelations ) return ;	$this->initializedInnerRelations = true; 			
		
		foreach($this->relations as &$obj){
				
				if ($obj->keys['type'] == RelationType::Simple){	
					Debug(' RelationType::Simple not supported yet :: shult set state $this->selected = data["key"] ??');
				}				
							
				// Simple Inner
				if ($obj->keys['type'] == RelationType::InnerSimple){					
					
					if (!$IS_CALLED_BY_ME_RECURSION_PROTECTION){ 	// ive disabled the protection because off  same table (as parent tree) list not loaded !
						$obj->initializedRelationsObject = false;
						$obj->initInnerRelations( true );
						$obj->initList() ;
						
						if ($this->id>0)
							$obj->selected = array($this->data[$obj->keys['left_key']]) ;
							
						if ($this->id > 0 && count($this->data)){	
							$obj->setSelectedByValue($obj->selected[0]);
						}else{
							$obj->setSelected(0);	
						}
					}
				}
		}
		unset($obj);
	}	
	

	
	public function initOuterRelations(){
		
		$this->initRelationsObject(); //has protection
		
		//protect
		if ($this->initializedOuterRelations ) return ;	$this->initializedOuterRelations = true;
		
		foreach($this->relations as &$obj){
				
				// Attachd via intermediar table 
				if ($obj->keys['type'] == RelationType::ManyToMany){				
					
					if ($this->id < 1) {continue ; }
					
					$obj->selected = $this->dataRelation[$obj->keys['tbl']] ;
					if (count($obj->selected) > 0){
						$obj->sqlParam  = ' WHERE `'.$obj->name.'`.id IN( '. implode( ',',$obj->selected ) .' ) ' ;
						$obj->initList() ;
						$obj->setSelected(1);
					}
				}
				
				// Selectable list
				if ($obj->keys['type'] ==RelationType::ManyToManySelect){
					
					$obj->initInnerRelations( false );
					
						
					$obj->initList() ;
					$obj->setSelected(0);
					
					if ($this->id < 1) {continue ;}
					
					$obj->selected = $this->dataRelation[$obj->keys['tbl']] ;
					
					if (count( $obj->selected )>0){ // inutile loop protection
						foreach($obj->_list as &$j){
							$bFound = false ;
							if (in_array($j['id'],$obj->selected)){
									$bFound = true ;
							}
							$j['_selected'] = $bFound ;
						}
						unset($j) ;
					}
				}
				
				//Selectable list via dedicate table
				if ($obj->keys['type'] == RelationType::ManyToOne){					
					if ($this->id < 1) {continue ;}
					
					$obj->selected = $this->dataRelation[$obj->keys['tbl']] ;
					
					$obj->sqlParam = ' WHERE `'. $obj->keys['left_key'] .'` =  ' . $this->id ;					
					$obj->initList() 	;
					$obj->setSelected(1);
				}
				
				if ($obj->keys['type'] == RelationType::ManyToOneByKey){
					if ($this->id < 1) {continue ;}
					$obj->selected = $this->dataRelation[$obj->keys['tbl']] ;					
					$obj->sqlParam = ' WHERE `'. $obj->keys['tbl'] .'`.`'. $obj->keys['left_key'] .'` =  ' . ($this->data[ $obj->keys['right_key'] ] ?  $this->data[ $obj->keys['right_key'] ] : '0' );
					$obj->initList() 	;
					$obj->setSelected(1);
				}
		}
		unset($obj);
	}	
	
	
	function deleteRelations(){ /*del M2M relation only !!! */
		if( ! count($this->relation) ){ 		return ; }
		if ($this->id < 0){ 						return ; }
		foreach ($this->relation as $k=>$v ) {
			if ($v['type'] == RelationType::ManyToMany ||
				$v['type'] == RelationType::ManyToManySelect){
				$this->db->q('DELETE  FROM `'. $v['by_tbl']. '` WHERE `' . $v['right_key']. '` = ' . $this->id );				
			}
			if ($v['type'] == RelationType::ManyToOne){
				$this->db->q( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = 0 WHERE `' . $v['left_key']. '` = ' . $this->id );	
			}
			if ($v['type'] == RelationType::ManyToOneByKey){	
				$this->db->q( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key']  . '` = 0 WHERE  `'. $v['tbl'] .'`.`'. $v['left_key'] .'` =  ' . ($this->post_data[ $v['right_key'] ] ?  $this->post_data[ $v['right_key'] ] : '0' ) ); 
			}
		}
	}	

	function addRelations($dupplicate = false){
		if( ! count($this->relation) ){ 		return ; }
		if ($this->id < 0){ 						return ; }
		
		foreach ($this->relation as $k=>$v ) {		
			
			if ($v['type'] ==  RelationType::ManyToMany ||
				$v['type'] == RelationType::ManyToManySelect || 
				$v['type'] ==  RelationType::ManyToOne ||
				$v['type'] ==  RelationType::ManyToOneByKey
				 ){
					if ($dupplicate){
						$k_ids = $this->dataRelation[$v['tbl']] ;
					}else{
						$k_ids = post($v['tbl']) ? post($v['tbl']) : array() ;
					}
			}
			
			if ($v['type'] ==  RelationType::ManyToOneByKey ){
					if (count($k_ids)>0)
						$this->db->q( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = ' . 
							($this->post_data[ $v['right_key'] ] ?  $this->post_data[ $v['right_key'] ] : '0' )  . ' WHERE id IN(' . join($k_ids,",") . ') ' );
					$this->db->q( 'DELETE  FROM `'. $v['tbl']. '` WHERE `' . $v['left_key']. '` =  0 '  );						
			}
			
			if ($v['type'] ==  RelationType::ManyToMany || $v['type'] == RelationType::ManyToManySelect){		
					
					if ($dupplicate){
						$k_ids = $this->dataRelation[$v['tbl']] ;
					}else{
						$k_ids = post($v['tbl']) ? post($v['tbl']) : array() ;
					}
								 
					$insert_sql = '';
					$bulided = false;
					foreach( $k_ids as $id){						
						if ($bulided){
							$insert_sql .=  ' UNION SELECT '.$id .','. $this->id   ;
						}else{
							$insert_sql .=  'INSERT INTO `'. $v['by_tbl'] .'` (`'.$v['left_key'].'`,`'.$v['right_key'].'`) SELECT '.$id.','.$this->id  ; 
						}
						$bulided = true ;
					}
					if ($bulided){
						$this->db->q($insert_sql );
					}
			}
			
			if ($v['type'] ==  RelationType::ManyToOne){
					if (count($k_ids)>0)
						$this->db->q( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = ' . $this->id . ' WHERE id IN(' . join($k_ids,",") . ') '  );
					$this->db->q( 'DELETE  FROM `'. $v['tbl']. '` WHERE `' . $v['left_key']. '` =  0 '  );	
			}
			

		}	
	}		
}

?>