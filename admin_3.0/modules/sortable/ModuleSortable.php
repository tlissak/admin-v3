<?
class ModuleSortable{	
	
	function dispacher(){
			if (get('set_position') == 1){
				global $db;
				$db->query( SQL::build('UPDATE',get('tbl'), array(get('fld') => get("val")) , get('id'))  );
				echo 'ok' ;
				die ;
			}
	}
	
	function includer(){
		return 'UI.sortable.js';
	}
	
}
?>