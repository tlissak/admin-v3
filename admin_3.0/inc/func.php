<?
//v3
function cf($_fld,$title=false,$type="text"){
	global $ctrl;
	global $tbl;
	$p = $ctrl->contextTable ;
		
	if ($p != null){ 	
		$d = $p->data ;
	}else{
		$d = array($_fld=>post($_fld) );	
	}

	if ($title==false){			
		$fld 		= $_fld['name'] ;
		$title	= $_fld['title'] ;
		$type	= $_fld['type'] ;
	}else{
		$fld = $_fld	;
	}
	
	if (! array_key_exists($fld,$d)){
		global $tbl;
		Debug('cf() the index not found.  Try to add into new AdminController::$tableInstances("'.$tbl.'") = array("'. $fld . '")   ');
	}
	
	$h = '<p class="'.$type.'">' ;
	$h .= '<label for="fld_'.$fld.'">'.$title.' :</label>' ;
	
	if ($type == 'select-relation'){
		Todo("Check this new version");
		$h = '</p><div class="'.$type.'">' ;
		$h .= '<label for="fld_'.$fld.'">'.$title.' :</label>' ;	
		$h .= 'is in' ;
		//$h .= $table->getTableHtml() ;
		$h .= "</div><p>" ;		
		$h .= 'Selected Value :' . $d[$fld]  ;	
		
	}
	if ($type == 'text'){
		$h .= '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" />' ;
	}
	if ($type == 'password'){
		$h .= '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" />' ;
	}
	if ($type == 'date'){
		$h .= '<input type="date" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="jDate" />' ;
	}
	if($type == 'checkbox'){
		$h .= '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1" ' ;
		$h .= ($d[$fld]) ? ' checked="checked" ' : '' ;
		$h .= ' />' ;
	}
	if($type == 'textarea'){
		$h .= '<textarea name="'.$fld.'" id="fld_'.$fld.'" >' ;
		$h .= $d[$fld]  ;
		$h .= '</textarea>' ;
	}
	if($type == 'textarea-large'){
		$h .= '<textarea name="'.$fld.'" id="fld_'.$fld.'" style="width:100%; height:500px;" >' ;
		$h .= coded($d[$fld]  );
		$h .= '</textarea>' ;
	}
	if($type == 'color'){
		$h .= '<input type="text" class="jColor" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld] .'"  />' ;
	}
	if($type == 'file'){	
		$h = '<div class="droparea">';
		$h .= '<span class="btn btn-mini btn-file"  >';
		$h .= '<i class="icon-plus icon"></i>  Sélectionnez ou glissez le fichier ici	';
		$h .= '<input  name="'.$fld.'" type="file" data-url="?tbl='.$tbl.'&contexttbl='.$p->name.'&fld='.$fld.'&upload=1" />'; // add data-path ;
		$h .= '</span>';
		$h .= '<div class="files">';		
		//current state ;
		if(is_file(P_PHOTO .$d[$fld])){
			$h .= '<input type="hidden" name="'.$fld.'" id="fld_fld_'.$fld.'" value="'.$d[$fld] . '" />' ;
			if (is_image( P_PHOTO .$d[$fld]) ){  	$h .= '<img src="' .U_PHOTO . $d[$fld] .'?width=200" style="max-height:100px; max-width:100px;"  />' ;	}
			if (is_pdf(P_PHOTO .$d[$fld])){  			$h .= '<a href="'.U_PHOTO .$d[$fld].'" target="_blank"><img src="img/pdf.jpg"  /></a>' ; 	}
			$h .= '<a href="javascript:" class="btn link-delete-file" data-path="'.urlencode($d[$fld]).'" ><i class="ico-trash"></i></a>' ;	
		}
		$h .= '</div></div><p>';
	}
	
	if($type == 'hidden'){
		$h .= '<input type="hidden" name="s_'.$fld.'" id="hdn_fld_'.$fld.'" value="'.($d[$fld])  . '" />' ;		
	}
	
	if ($type == 'select'){
		$json = array("id"=>0 , "tbl"=>$_fld['relation_tbl']);
		$h .= '<span class="select-list"><select name="'.$fld.'" id="fld_'.$fld.'" style="max-width:400px;" data=\''.  json_encode($json) .'\'>' ;
		$h .='<option value="0"></option>' ;		
		foreach($_fld['list'] as $l){
            $h .= '<option value="'. $l['id']  . '"' ;
			if ($d[$_fld['right_key']] == $l['id']){
				$json["id"] =  $l['id'];
				$h .= ' selected="selected" ' ;
			}
			$h .= ">".$l['id'] .'. ' . $l['name']."</option>" ;
		}	
		$h .= '</select><i></i></span>' ;
		if (isset($_fld['relation_tbl'])){			
			$h .= ' <span class="relation-add relation-option" data=\''.  json_encode($json) .'\'   ><span class="ico ico-add"></span></span>';
			$h .= '<span data=\''. json_encode($json) .'\' style="'. (($json['id']>0) ? '' :'display:none;') .'" class="relation-mod relation-option">' ;
			$h .= '<span class="ico ico-edit"></span></span>' ;	
		}
	}
	if ($type == 'select-disable'){
		$json = array("id"=>0 , "tbl"=>$_fld['relation_tbl']);
		$h .= '<span class="select-list"><select name="'.$fld.'" id="fld_'.$fld.'" style="width:400px;" onfocus="return false;" data=\''.  json_encode($json) .'\'>' ;
		$h .='<option value="0">Ajouter             &gt;&gt;&gt;</option>' ;		
		foreach($_fld['list'] as $l){ if ($l['id'] == $d[$_fld['right_key']] ) {
            $h .= '<option value="'. $l['id']  . '" selected="selected" >'.$l['id'] .'. ' . $l['name']."</option>" ;
			$json["id"] = $l['id'] ;
		}}
		$h .= '</select><i></i></span>' ;
		if (isset($_fld['relation_tbl'])){			
			$h .= ' <span class="relation-add relation-option" data=\''.  json_encode($json) .'\'   ><span class="ico ico-add"></span></span>';			
			$h .= '<span data=\''. json_encode($json) .'\' style="'. (($json['id']>0) ? '' :'display:none;') .'" class="relation-mod relation-option">' ;
			$h .= '<span class="ico ico-edit"></span></span>' ;	
		}		
	}
	if ($type == 'select-boolean'){
		$h .= '<span class="select-list"><select name="'.$fld.'" id="fld_'.$fld.'" style="width:210px;" >' ;
		$h .= '<option value="0">'. $_fld['list'][0] . '</option>' ;
		$h .= '<option value="1" '.(($d[$fld] )  ? ' selected="selected" ' : "" ).' >'.$_fld['list'][1]."</option>" ;
		$h .= '</select><i></i></span>' ;
	}
	if ($type == 'sort'){
		$h .= '<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" />' ;
	}
	if ($type == 'range'){
		$h .= '<input type="range" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" step="1"   max="'.$_fld['max'].'" min="'.$_fld['min'].'" />' ;
		$h .= '<span> ( Entre '.$_fld['min'] .' et ' .$_fld['max'] .' )</span>' ;
	}
	if ($type == 'number'){
		$h .= '<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" ' ;
		$h .= ( isset($_fld['min']) ? ' min="'.$_fld['min'].'"' : ''  ) ;
		$h .= ( isset($_fld['max']) ? ' max="'.$_fld['max'].'"' : ''  ) ;		
		$h .= ' />' ;
	}
	$h .= '</p>' ;
	return $h ;
}


?>