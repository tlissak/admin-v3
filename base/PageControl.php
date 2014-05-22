<?php 
class PageControl{
	
	public $data = array();
	
	public function __construct($alias = false){
		global $db ;
		if (! $alias )
			$alias = get("url_alias") ;
		if (! $alias )
			$alias = basename(P_SELF,'.php');
		
		$this->data =  $db->fetchRow('SELECT * FROM page  WHERE url_alias = '.SQL::v2txt($alias).' AND valid = 1 ');		
		 if (DEV_MODE && count($this->data) == 0 ){ 
		 	$db->query( SQL::build('INSERT','page',array('url_alias'=>$alias,'title'=>$alias,'meta_title'=>$alias,'valid'=>1) )) ;
		 }
	}
	
	function __get($k){
			if (count($this->data)){return $this->data[$k] ;	}
			return "";
	}
}
