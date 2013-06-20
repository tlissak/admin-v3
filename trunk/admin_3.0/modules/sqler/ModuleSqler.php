<?
class ModuleSqler{	
	
	function dispacher(){
			if (get('set_sql') == 1){
				$sql = trim(get('sql') );
				$out = array('sql'=> $sql ) ; 
				if (stripos($sql,'SELECT')  !== 0 && stripos($sql,'UPDATE')  !== 0){
					$out['error'] = 'QueryNotAllowed' ;
				}else{
					global $db ;
					$out['list'] = $db->fetch($sql)	 ;
					$out['error'] = $db->last_error ;
				}
				header('Content-type: application/json');
				echo json_encode($out) ;
				die; 	
			}
	}
	
	function includer(){
		return 'UI.sqler.js';
	}
	
}
?>