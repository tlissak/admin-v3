<?
class ModuleFilebrowser{	
	
	function dispacher(){
			if (get('browse') == 1){
				$out = new FileBrowser( get('path') );
				header('Content-type: application/json');
				echo json_encode($out) ;
				die ;
			}
	}
	
	function includer(){
		return 'UI.fileBrowser.js';
	}
	
}
?>