<?php

class Form{
    /**
     * @var Loader
     */
    private $parent ;

    public $id ;
    public $data = array() ;
    public $data_posted = array() ;

    function initData(){
        foreach($this->parent->dbFields as $k){
            $this->data[$k]	 = '';
        }
    }
    function initDbData(){
        global $db ;
        $sql = 'SELECT ' . implode(NL.',',$this->parent->dbFields) . ' FROM ' . $this->parent->name . ' WHERE `'.$this->name.'`.id = '.$this->id  ;
        $res = $db->fetchRow($sql);
        if(count($res)){
            $this->data = $res;
        }else{
            $this->id = 0;
        }
    }
    function initPostData(){
        foreach($this->parent->dbFields as $k){
            if (isset($_POST[$k])) {
                $this->data_posted[$k]	 = post($k);
            }
        }
    }

    public function __construct(Loader &$p){
        $this->parent = $p ;
    }

    public function GetHeader(){
        //TODO:
        //Get : hidden controls like ID , Action
        //Get : Buttons controls depandes on Action
    }


    public function GetBody(){
        /* init data */
        $this->id = get('id') ? intval(get('id')) : 0 ;

        $this->initData() ;

        if ($this->id){
            $this->initDbData();
        }else{
            $this->initPostData() ;
        }

        $controls = array();

        $_out = array();
        foreach ($this->parent->formFields as $fld) {
            $_out[] = $this->Control($fld) . NL ;
        }

        return implode(NL,$_out) ;

    }

    private function Control($a){

        //TODO
        //Loader should have Attr( group with title and pos
        //$opts should contain group to do an separate panel


        $fld = $a['name'] ;
        $label = $a['title'] ;
        $opts = isset($a['opts']) ? $a['opts'] : false ;
        $type = $a['type'] ;
        $d = $this->data ; //? $this->data : array($fld=>post($fld) ) ;

        // if (! array_key_exists($fld,$d)){fb('Trying to create field  "'. $fld . '" but the table ("'.$this->name.'")  does not have this field  ');}

        $out = "" ;

        $out .= '<div class="form-group"><label class="col-sm-4 control-label" for="fld_'.$fld.'">'.$label.' :</label>' . NL ;
        $out .= '<div class="input-group col-sm-8">' ;


        $extends = ' autocomplete="off" ' ;
        if ($opts){
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
            if ($opt['default_value']){
                //
            }
        }

        if($type == 'color'){
            $out .= '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld] .'" class="form-control color_picker"   data-type="color"  data-limit="7" '. $extends.'  />' ;
        }
        if ($type == 'date'){
            $out .= '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control date_picker"  data-type="date" data-limit="10" '. $extends.' />' ;
        }
        if ($type == 'text'){
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-font"></i> </span>
			<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" '. $extends.' class="form-control" data-limit="255" />' ;
        }
        if ($type == 'zipcode'){
            $out .= '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="zipcode" class="form-control" data-limit="5" '. $extends.' />' ;
        }
        if ($type == 'number'){
            $out .= '<span class="input-group-addon"><i class="glyphicon"></i> </span>
			<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="int" class="form-control" data-limit="11" '. $extends.' />' ;
        }
        if ($type == 'email'){
            $out .= '<span class="input-group-addon">@</span>
                    <input type="email" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="email" class="form-control" data-limit="100" '. $extends.' />' ;
        }
        if ($type == 'float'){
            $out .= '<span class="input-group-addon">0.00</span>
			 <input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="float"  '. $extends.' />' ;
        }
        if ($type == 'price'){
            $out .= '<span class="input-group-addon">€</span>
                    <input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" step="any"  data-type="price" '. $extends.' />' ;
        }
        if ($type == 'sort'){
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-sort"></i></span>
            <input type="number" name="'.$fld.'"id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" step="any"  data-type="float" data-limit="50" '. $extends.' />' ;
        }
        if ($type == 'url'){
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                    <input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  class="form-control" data-type="url" data-limit="255" '. $extends.' />' ;
        }
        if ($type == 'phone'){
            $out .= '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="phone" data-limit="20" '. $extends.' />' ;
        }
        if ($type == 'password'){
            $out .= '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="password" data-limit="50" '. $extends.' />' ;
        }
        if($type == 'checkbox'){
            /* return '<label class="switch-input">
                     <input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1" class="switch-checkbox" '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' />
                     <i data-swon-text="ON" data-swoff-text="OFF"></i>
                     </label>' ;*/
            return '<label class="form-checkbox form-icon">
                <input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1"  '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' /></label>' ;
            $out .= '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1"  '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' />' ;
        }
        if($type == 'textarea'){
            $out .= '<span class="input-group-addon">-</span>
            <textarea name="'.$fld.'" id="fld_'.$fld.'" class="form-control" spellcheck="false" '. $extends.'>'. $d[$fld]  . '</textarea>' ;
        }
        if($type == 'rte'){
            $out .= '<textarea name="'.$fld.'" id="fld_'.$fld.'" class="form-control rte" spellcheck="false" '. $extends.'>'
            . htmlentities($d[$fld], ENT_QUOTES , "UTF-8"). '</textarea>' ;
        }
        if($type == 'file'){

            $h = '<div class="droparea"><span class="btn btn-mini btn-file"  >';
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
            $h .= '</div></div>';
            $out .= $h;
        }
        if($type == 'hidden'){
            $out .= '<input type="hidden" name="'.$fld.'" id="hdn_fld_'.$fld.'" value="'. isset($d[$fld]) ? $d[$fld] : $opt['default_value']  . '" '. $extends.' />' ; //
        }
        if ($type == 'range'){
            $out .= '<input type="range" name="'.$fld.'" id="fld_'.$fld.'" class="form-control" data-type="int" value="'.($d[$fld] ? $d[$fld] : 0).'" step="1" data-limit="11" max="'.$opt['max'].'" min="'.$opt['min'].'" '. $extends.' /><span>'.$d[$fld].'</span>' ;
        }

        $out .= '</div></div>' ;
        return $out ;
    }



}

?>