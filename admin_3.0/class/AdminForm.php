<?
class AdminForm extends AdminTable{

	public $formHtml = ""; 
	public $formFields = array() ;	
	
	function getForm(){
		$lb = "\r\n";
		foreach($this->formFields as $formFld){
			if ($formFld['type'] == 'file'){
				$this->formHtml .= '<label for="'.$formFld['name'].'">'.$formFld['title'].' :</label> <div class="droparea">'. $lb. $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb . '</div>' .$lb;
				$this->formHtml .= '<div class="clear"></div>' ;
			}elseif($formFld['type'] == 'rte'){ 			
				$this->formHtml .= '<label for="'.$formFld['name'].'">'.$formFld['title'].' :</label><div class="rte-zone-outer">'. $lb. $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb . '</div>' .$lb ;	
			}elseif($formFld['type'] == 'html'){
				$this->formHtml .= $formFld['html'] ;
			}else{
				$this->formHtml .= '<p class="text"><label for="fld_'.$formFld['name'].'">'.$formFld['title'].' :</label>' . $lb . $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb. '</p>' .$lb ;
			}
		}		
		return $this->formHtml;
	}
	
	public function _Zipcode($fld,$title,$opt=array()){ 	$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'zipcode') ;return $this; }	
	public function _Number($fld,$title,$opt=array()){ 	$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'number') ;return $this; }	
	public function _Color($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'color') ;return $this; }	
	public function _Phone($fld,$title,$opt=array()){ 	$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'phone') ;return $this; }	
	public function _Url($fld,$title,$opt=array()){ 			$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'url') ;return $this; }	
	public function _Sort($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'sort') ;return $this; }
	public function _Range($fld,$title,$opt=array()){ 	$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'range') ;return $this; }	
	public function _Email($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'email') ;return $this; }	
	public function _Price($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'price') ;return $this; }	
	public function _Float($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'float') ;return $this; }	
	public function _Text($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'text') ;return $this; }	
	public function _Date($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'date') ;return $this; }	
	public function _Check($fld,$title,$opt=array()){ 	$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'checkbox') ;return $this; }	
	public function _Textarea($fld,$title,$opt=array()){$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'textarea') ;return $this; }	
	public function _Rte($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'rte') ;return $this; }
	public function _Hidden($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'hidden') ;return $this; }			
	public function _File($fld,$title,$opt=array()){ 		$this->formFields[] = array('opt'=>$opt,'name'=>$fld,'title'=>$title,'type'=>'file') ;return $this; }		
	public function _Html($html,$opt=array()){ 			$this->formFields[] = array('opt'=>$opt,'type'=>'html','html'=>$html) ;return $this; }	
	
	private function getSimpleField($fld,$type,$opt){
		$d = $this->data ? $this->data : array($fld=>post($fld) );
		if (! array_key_exists($fld,$d)){fb('Trying to create field  "'. $fld . '" but the table ("'.$this->name.'")  does not have this field  ');}	
		
		$extends = ' autocomplete="off" ' ;
		if (count($opt)){
			if (isset($opt['required'])){
				$extends .= ' data-require="true" ' ;
			}
			if (isset($opt['pattern'])){
				$extends .= ' data-pattern="'.$opt['pattern'].'" ' ;
			}
			if (isset($opt['readonly'])){
				$extends .= ' readonly="readonly" ' ;
			}
			if (isset($opt['extends'])){
				$extends .= $opt['extends'] ;
			}
		}
		
		if($type == 'color'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld] .'" class="color_picker"   data-type="color"  data-limit="7" '. $extends.'  />' ;
		}
		if ($type == 'date'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="date_picker"  data-type="date" data-limit="10" '. $extends.' />' ;
		}
		if ($type == 'text'){
			return '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" '. $extends.' data-limit="255" />' ;
		}
		if ($type == 'zipcode'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="zipcode" data-limit="5" '. $extends.' />' ;
		}
		if ($type == 'number'){
			return '<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="int" data-limit="11" '. $extends.' />' ;
		}
		if ($type == 'email'){
			return '<input type="email" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="email" data-limit="100" '. $extends.' />' ;
		}
		if ($type == 'float'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  data-type="float"  '. $extends.' />' ;
		}
		if ($type == 'price'){
			return '<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" step="any"  data-type="price" '. $extends.' />' ;
		}
		if ($type == 'sort'){
			return '<input type="number" name="'.$fld.'"id="fld_'.$fld.'" value="'.$d[$fld].'"  step="any"  data-type="float" data-limit="50" '. $extends.' />' ;
		}
		if ($type == 'url'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  data-type="url" data-limit="255" '. $extends.' />' ;
		}
		if ($type == 'phone'){
			return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  data-type="phone" data-limit="20" '. $extends.' />' ;
		}
		if ($type == 'password'){
			return '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  data-type="password" data-limit="50" '. $extends.' />' ;
		}
		if($type == 'checkbox'){
			return '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1" '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' />' ;
		}
		if($type == 'textarea'){
			return '<textarea name="'.$fld.'" id="fld_'.$fld.'" spellcheck="false" '. $extends.'>'. $d[$fld]  . '</textarea>' ;
		}
		if($type == 'rte'){
			return '<textarea name="'.$fld.'" id="fld_'.$fld.'" spellcheck="false" style="width:100%;height:350px;" class="rte" '. $extends.'>' 
				. htmlentities($d[$fld], ENT_QUOTES , "UTF-8"). '</textarea>' ; 
		}
		if($type == 'file'){
			
			$h = '<span class="btn btn-mini btn-file"  >';
			$h .= '<i class="icon-plus"></i>  ' . l('browse or drop file here') ;
			$h .= '<input  name="'.$fld.'" type="file" data-url="?tbl='.$this->name.'&contexttbl='. get('contexttbl').'&fld='.$fld.'&upload=1" '. $extends.'  />'; // add data-path ;
			$h .= '</span>';
			$h .= '<div class="files">';
			$h .= '<input type="hidden" name="'.$fld.'" id="fld_fld_'.$fld.'" value="'.$d[$fld] . '" '. $extends.' />' ;			
			if (V2_IMG && is_image( '../'.$d[$fld])){
				$h .= '<img src="../'  . $d[$fld] .'?width=200" style="max-height:100px; max-width:100px;"  />' ;
			}elseif(is_file(P_PHOTO .$d[$fld])){//current state ;
				if (is_image( P_PHOTO .$d[$fld]) ){  	$h .= '<img src="' .U_PHOTO . $d[$fld] .'?width=200" style="max-height:100px; max-width:100px;"  />' ;	}
				elseif (is_pdf(P_PHOTO .$d[$fld])){  			$h .= '<a href="'.U_PHOTO .$d[$fld].'" target="_blank"><img src="img/pdf.jpg"  /></a>' ; 	}
				else{$h .=  $d[$fld];	}
			}else{
				$h .='<img src="img/color_picker/grid.gif" style="max-height:100px; max-width:100px;" >' ;
			}
			$h .= '<a href="javascript:" class="btn link-delete-file" data-path="'.urlencode($d[$fld]).'" ><i class="icon-trash"></i></a>' ;	
			$h .= '</div>';
			return $h;
		}
		
		if($type == 'hidden'){
			return '<input type="hidden" name="'.$fld.'" id="hdn_fld_'.$fld.'" value="'. isset($d[$fld]) ? $d[$fld] : $opt['default_value']  . '" '. $extends.' />' ; //
		}
		if ($type == 'range'){
			return '<input type="range" name="'.$fld.'" id="fld_'.$fld.'" data-type="int" value="'.$d[$fld].'" step="1" data-limit="11" max="'.$opt['max'].'" min="'.$opt['min'].'" '. $extends.' /><span>'.$d[$fld].'</span>' ;
		}
		fb('Field type not handle ');
		fb($type);
	}
	
		
}
?>