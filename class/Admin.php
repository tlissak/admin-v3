<?php

interface RelationType{
    const Simple 					= 0; // product (type.id)  , product_type.id
    const ManyToMany 			= 1; //  product.id , type.id , product_type(product_id,type_id)
    const ManyToOne 			= 2; // product.id , product_type(product_id)
    const ManyToManySelect = 3; //  product.id , type.id , product_type(product_id,type_id) show all type.id to select
    const InnerSimple 			= 4 ;
    const ManyToOneByKey 	= 5 ;
}

class Admin{

#region Paging List
    public $_list  			= array() ; /* ROWS */
    public $num_results 		= -1; /* COUNT OF TOTAL ROWS */

  //  public $b_is_initialized_list = false ;
    //public $b_is_initialized_list_sql = false ;

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




    public $search_val = '' ;
    public $filter_val = array() ;
    public $order_val = array();

    public $sqlParam = '' ;

    public function v4_initList(  $limit  = 0 ,$offset = 0  ){
       // if ($this->b_is_initialized_list ) return ;	$this->b_is_initialized_list = true; //protect
        $this->initListSql();

        $this->sql_count = 'SELECT COUNT(*) AS cn ' ."\r\n"
            . $this->sql_extra_fields  ."\r\n"
            . ' FROM '
            . $this->sql_tables ."\r\n"
            . $this->sql_left_joins ."\r\n"
            . $this->sql_inner_joins ."\r\n"
            . $this->sql_param  ;


        $list_count = $this->db->fetchRow($this->sql_count);

        $this->num_results = (int)($list_count["cn"]) ;

        $this->sql_rows=  'SELECT '
            . $this->sql_fields ."\r\n"
            . $this->sql_extra_fields ."\r\n"
            . ' FROM '
            . $this->sql_tables ."\r\n"
            . $this->sql_left_joins ."\r\n"
            . $this->sql_inner_joins ."\r\n"
            . $this->sql_param  ."\r\n"
            . $this->sql_order  ."\r\n"
            .  ' LIMIT ' . $offset . ','.  $limit ."\r\n" ;

        //  p($this->sql_rows);

        $list = $this->db->fetch(  $this->sql_rows );
        $this->_list = $list;

    }

    public function initListSql(){

        //if ($this->b_is_initialized_list_sql ) return ;	$this->b_is_initialized_list_sql = true; //protect

        $this->sql_fields 				=	'`'.$this->name .'`.* ';
        $this->sql_extra_fields 	= '' ;
        $this->sql_tables 			= ' `'.$this->name.'` ' ;
        $this->sql_order				= "" ;
        $this->sql_param 			= "" ;

        $this->__r_fields = array();

        foreach($this->relations as $rel){
            if($rel->keys['type'] == RelationType::Simple 	|| $rel->keys['type'] == RelationType::InnerSimple ){
                $inner_sql = ','. $rel->name.$rel->keys['left_key'].'_ljoin.'.$rel-> fld_title .' AS '.$rel->keys['left_key'] . '_inner ' ;
                $this->sql_left_joins .= ' LEFT JOIN `'. $rel->name . '` AS '.$rel->name .$rel->keys['left_key'].'_ljoin '  ;
                $this->sql_left_joins .= ' ON '.$rel->name.$rel->keys['left_key'] .'_ljoin.id = `'. $this->name.'`.'.  $rel->keys['left_key']  .' ' ;
                $this->sql_fields .= $inner_sql ;

                $this->__r_fields[$rel->keys['left_key'].'_inner'] = $rel->name.$rel->keys['left_key'].'_ljoin.'.$rel-> fld_title ;
            }
        }

        if ( count($this->filter_val  )>0 ) {
            $this->sql_param .= $this->getListSqlFilter( ) ;
        }

        if ( strlen($this->search_val  )>0 ) {
            $this->sql_param .= $this->getListSqlSearch( ) ;
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
                if($r->keys['left_key'] == $fld ){
                    $rf = true;
                    p($r);
                    break ;
                }


            }
            if ($rf){		$val = ' = '. SQL::v2int($query)  ;
            }else{		$val  = ' LIKE ' .SQL::v2txt($query .'%') ;			}

            if (! in_array($fld,$this->fields) && $fld != 'id') {
                $out .= ($found ? ' AND ' : ' WHERE ') . $this->__r_fields[$fld] . $val;
            }else {
                $out .= ($found ? ' AND ' : ' WHERE ') . '`' . $this->name . '`.' . $fld . $val;
            }
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
    function verifyImageInput(){	foreach($this->image as $img){		if (is_array($img)){	_die('Image as array is depracted Table : '. $this->name);}	}}
    function verifyRelationInput(){
        foreach($this->relation as $r){
            if (!isset($r['name']) || !isset($r['type'] ))	{		_die('Relation input incorrect name or type not seted Table : '. $this->name);	}
            if (!isset($r['tbl'] ))	{		_die('Relation input incorrect "tbl" not seted for Table : '. $this->name);	}
            if ($r['type'] == RelationType::InnerSimple || $r['type'] == RelationType::Simple){
                if ( !isset($r['left_key'])  ){	_die('Relation input incorrect left_key not seted  Table : '. $this->name);	}
            }elseif($r['type'] == RelationType::ManyToMany || $r['type'] == RelationType::ManyToManySelect){
                if (!isset($r['by_tbl'] )|| !isset($r['left_key'] )|| !isset($r['right_key'] ))	{	_die('Relation input incorrect bytable,left or right key not seted Table : '. $this->name ); 	}
            }elseif($r['type'] == RelationType::ManyToOne || $r['type'] == RelationType::ManyToOneByKey){
                if ( !isset($r['left_key'] ))	{	_die('Relation input incorrect left_key not seted Table : '. $this->name ); 	}
            }
        }
    }
#endregion

#region Form
    public $formHtml = "";
    public $formFields = array() ;

    function getForm(){
        $lb = "\r\n";
        foreach($this->formFields as $formFld){
            if ($formFld['type'] == 'file'){
                $this->formHtml .= '<label for="'.$formFld['name'].'">'.$formFld['title'].' :</label> <div class="droparea">'. $lb. $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb . '</div>' .$lb;
                $this->formHtml .= '<div class="clear"></div>' ;
            }elseif($formFld['type'] == 'rte'){
                $this->formHtml .= '<div class="form-group"><label class="col-xs-6 col-sm-3 control-label" for="fld_'.$formFld['name'].'">'.$formFld['title'].' :</label>' . $lb ;
                $this->formHtml .= '<div class="input-group col-xs-6 col-sm-9">' . $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb. '</div>' .$lb  ;
                $this->formHtml .= '</div>' ;
                // v3				$this->formHtml .= '<label for="'.$formFld['name'].'">'.$formFld['title'].' :</label><div class="rte-zone-outer">'. $lb. $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb . '</div>' .$lb ;
            }elseif($formFld['type'] == 'html'){
                $this->formHtml .= $formFld['html'] ;
            }else{
                $this->formHtml .= '<div class="form-group"><label class="col-xs-6 col-sm-3 control-label" for="fld_'.$formFld['name'].'">'.$formFld['title'].' :</label>' . $lb ;
                $this->formHtml .= '<div class="input-group col-xs-6 col-sm-9">' . $this->getSimpleField($formFld['name'],$formFld['type'],$formFld['opt']) .$lb. '</div>' .$lb  ;
                $this->formHtml .= '</div>' ;
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
            return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld] .'" class="form-control color_picker"   data-type="color"  data-limit="7" '. $extends.'  />' ;
        }
        if ($type == 'date'){
            return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control date_picker"  data-type="date" data-limit="10" '. $extends.' />' ;
        }
        if ($type == 'text'){
            return '<span class="input-group-addon"><i class="glyphicon glyphicon-font"></i> </span>
			<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" '. $extends.' class="form-control" data-limit="255" />' ;
        }
        if ($type == 'zipcode'){
            return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="zipcode" class="form-control" data-limit="5" '. $extends.' />' ;
        }
        if ($type == 'number'){
            return '<span class="input-group-addon"><i class="glyphicon"></i> </span>
			<input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="int" class="form-control" data-limit="11" '. $extends.' />' ;
        }
        if ($type == 'email'){
            return '<span class="input-group-addon">@</span>
                    <input type="email" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" data-type="email" class="form-control" data-limit="100" '. $extends.' />' ;
        }
        if ($type == 'float'){
            return '<span class="input-group-addon">0.00</span>
			 <input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="float"  '. $extends.' />' ;
        }
        if ($type == 'price'){
            return '<span class="input-group-addon">€</span>
                    <input type="number" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" step="any"  data-type="price" '. $extends.' />' ;
        }
        if ($type == 'sort'){
            return '<span class="input-group-addon"><i class="glyphicon glyphicon-sort"></i></span>
            <input type="number" name="'.$fld.'"id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" step="any"  data-type="float" data-limit="50" '. $extends.' />' ;
        }
        if ($type == 'url'){
            return '<span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                    <input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'"  class="form-control" data-type="url" data-limit="255" '. $extends.' />' ;
        }
        if ($type == 'phone'){
            return '<input type="text" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="phone" data-limit="20" '. $extends.' />' ;
        }
        if ($type == 'password'){
            return '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="'.$d[$fld].'" class="form-control" data-type="password" data-limit="50" '. $extends.' />' ;
        }
        if($type == 'checkbox'){
            /* return '<label class="switch-input">
                     <input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1" class="switch-checkbox" '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' />
                     <i data-swon-text="ON" data-swoff-text="OFF"></i>
                     </label>' ;*/
            return '<label class="form-checkbox form-icon">
                <input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1"  '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' /></label>' ;
            return '<input type="'.$type.'" name="'.$fld.'" id="fld_'.$fld.'" value="1"  '. (($d[$fld]) ? ' checked="checked" ' : '' ) . ' '. $extends.' />' ;
        }
        if($type == 'textarea'){
            return '<span class="input-group-addon">-</span>
            <textarea name="'.$fld.'" id="fld_'.$fld.'" class="form-control" spellcheck="false" '. $extends.'>'. $d[$fld]  . '</textarea>' ;
        }
        if($type == 'rte'){
            return '<textarea name="'.$fld.'" id="fld_'.$fld.'" class="form-control" spellcheck="false" class="rte" '. $extends.'>'
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
            return '<input type="range" name="'.$fld.'" id="fld_'.$fld.'" class="form-control" data-type="int" value="'.($d[$fld] ? $d[$fld] : 0).'" step="1" data-limit="11" max="'.$opt['max'].'" min="'.$opt['min'].'" '. $extends.' /><span>'.$d[$fld].'</span>' ;
        }
        p('Field type not handle '. $type);
    }
#endregionG



#region Loader
    public $relation 	= array() ;
    public $image 		= array() ;
    public $view 		= false ; // array()
    public $filter 		= false ;
    public $order 		= false ;
    public $fld_title 	= false; // require
    public $show 		= 1; //in menu

    public $options = array('readonly'=>0);
    public $protected = 0 ;


    public function Image($x ){		$this->image[] = $x 	; 	return $this ; }
    public function View($x ){		$this->view = $x 	; 			return $this ; }
    public function Show($x ){		$this->show = $x 	; 		return $this ; }
    public function FieldTitle($x){  $this->fld_title = $x ; 		return $this; }
    public function Relation($x ){	$this->relation[$x['name']] = $x 	; return $this ; }
    public function AddTableAttr($x , $y){ $this->{$x} = $y; return $this; }
    public function Load($opt_array=array()){
        if (! $this->name) {				_die("Table name not seted", E_USER_WARNING); return false;}
        if ( ! $this->view){			trigger_error("view not seted table ". $this->name . ' not loaded !', E_USER_WARNING);	return false; }
        if ( ! $this->fld_title){		trigger_error("fld_title not seted table ". $this->name , E_USER_WARNING);	 }
        if (count($this->image)){		$this->verifyImageInput() ; 	}
        if (count($this->relation)){	$this->verifyRelationInput()  ; }
        $this->options = $opt_array ;

        if (isset($this->options['readonly'])  && $this->options['readonly'] ){
            $this->protected = 1 ;
        }
        Ctrl::$tableInstances[$this->name] = &$this ;
        return false ;
    }


    public $viewtype = 'SIMPLE';

    //public $image  =array();
    public $_sort =array();
#endregion

#region MVC

    public $initializedViewList = false ;


    public $_r_imgp = array() ;
    public $_r_kys = array() ;

    function getTableRows(){ // created for paginig w ajax


        $oh = array();

        //
        if (count($this->image )){ $this->_r_imgp = $this->image  ; }

        foreach($this->formFields as $fld){
            if ($fld['type']=='sort')	{
                $this->_sort[] = 	$fld['name'] ;
            }
        }


        if ($this->viewtype == "SELECT-ONE-EDIT"){ // add empty line
            $empty_row = array('id'=>0,''=>0) ;
            foreach($this->view as $k=>$v){	$empty_row[$k]	 = '&nbsp;-&nbsp;';		}
            $empty_row['id'] = 0 ;
            $empty_row['_selected'] = 0 ;
            $oh[] =  $this->v4_getTableRow( $empty_row ) ;
        }



        foreach($this->_list as $l){
            $oh[] =  $this->v4_getTableRow($l)  ;
        }
        return $oh;
    }
    public function v4_getTableHtml(){

        global $tbl;

        //if ($this);
        //p($this);

        $table_url = '?tbl='.$this->name.'&v4_get_list_ajax=1&type_view='.$this->viewtype . '&contexttbl='. $tbl . '&relation=' ;



        $out =  '<table
                data-id="'.$this->id.'"
               data-silent=""
               data-cache="false"
               data-toggle="table"
               data-show-columns="true"
               data-search="true"

               data-show-refresh="true"
               data-show-toggle="true"
               data-url="'.$table_url.'"

               data-pagination="true"
               data-showFilter="true"

               data-sort-name="id"

               data-sort-order="desc"
               data-click-to-select="true"
               data-select-item-name="row-id"

               data-show_export="true"

               data-height="500"
               data-side-pagination="server"
               data-page-list="[5, 10, 20, 50, 100, 200]"


               >
               <thead><tr>
               ';

        $this->viewtype = get('type_view');

        if (($this->viewtype == "SELECT" || $this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT") && ! $this->protected){
            $out .= '<th data-field="__selection">Selection</th>';
        }
        foreach( $this->view as $key=>$title) {
            $out .= '<th data-field="'.$key.'" data-sortable="true">' . $title .'</th>' ;
            // if sortable data-editable="true"
        }
        if (($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT") && ! $this->protected){
            $out .= '<th data-filed="__editable">Editable</th>';
        }

        $out .=  '</tr></thead></table>' ;
        return $out ;

    }


    public function getTableState(){
        $h = '';
        if ($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT"){
            $h .= '<div class="list-state" data-viewtype="'.$this->viewtype.'" data-cache="'.implode(',',$this->selected).'"  ';
            if ($this->viewtype == "SELECT-EDIT"){
                $h .= '	data-fieldname="'.$this->name.'[]" '  ;
            }
            if ($this->viewtype == "SELECT-ONE-EDIT"){
                $h .= '	data-fieldname="'.$this->keys['left_key'].'" '  ;
            }
            $h .= '>';

            if (count($this->selected ) == 0 && $this->viewtype == "SELECT-ONE-EDIT"){
                $h .= '<input type="hidden" name="'.$this->keys['left_key'].'"  id="fld_'.$this->name .'" value="0"   checked="checked" />'  ;
            }

            foreach($this->selected as $id){
                if ($this->viewtype == "SELECT-EDIT"){
                    $h .= '<input type="hidden" name="'.$this->name.'[]" value="'.$id .'"    />'  ;
                }
                if ($this->viewtype == "SELECT-ONE-EDIT"){
                    $h .= '<input type="hidden" name="'.$this->keys['left_key'].'"  id="fld_'.$this->name .'" value="'.$id .'" />'  ;
                }
            }

            $h .= '</div>' ;
        }
        return $h ;
    }


    function v4_getTableRow($dt = array()){


        $id = $dt['id'] ;

        $out = array() ;

        $out['id'] = $dt['id'] ;
        $out['tbl'] = $this->name ;

      //  $out['_selected'] = $dt['_selected'] ;




        if ($this->viewtype == "SELECT-EDIT" && !$this->protected){
            $out['__selection'] = '<input class="relation-cb relation-cbx" type="checkbox"  id="_fld_'.$this->name.'_'.$id .'" value="'.$id .'"  '. ($dt['_selected'] ? 'checked="checked"' : '') .'   />'  ;
        }
        if (($this->viewtype == "SELECT" || $this->viewtype == "SELECT-ONE-EDIT" )  && !$this->protected){
            $out['__selection'] = '<input class="relation-cb" type="radio"   id="_fld_'.$this->name.'_'.$id .'" value="'.$id .'"   '. ($dt['_selected'] ? 'checked="checked"' : '') .'   />'  ;
        }

        if( count($this->_r_imgp )){
            foreach($this->_r_imgp as $img_k){
                foreach($this->view as $vw_k=>$vw_v){
                    if ($vw_k == $img_k){
                        $dt[$img_k] = $this->thumb($dt[$img_k]) ;
                    }
                }
            }
        }


        foreach ($this->view as $k=>$v){
            if ( ! array_key_exists ($k,$dt)){
                // if (DEV_MODE)
                //     Debug (' Check The row dosent contains key ' . $k);
                // $out['id'] .= '<td>'.$id .'!</td>' ;
            }else{
                if ($dt[$k] != null){
                    $out[$k] =  $dt[$k]  ;
                }else{
                    $out[$k] =  '-'  ;
                }
            }
        }


        if ( ($this->viewtype == "SELECT-EDIT" || $this->viewtype == "SELECT-ONE-EDIT" ) && !$this->protected){
            $out['__editable'] =  ($id>0) ? '<span class="relation-mod btn"  data-id="' . $id .'" ><i class="icon-editalt"></i></span>' : '&nbsp;';
        }

        return $out	;
    }

    function thumb($img){
        if (V2_IMG)
            return 	'<img src="../'. ((strpos($img,'photos/') === 0 )? $img : 'photos/'. $img ) .'?height=40" style="max-height:40px;max-width:100px;"  alt="[--]" />';
        if ( is_image( P_PHOTO . str_replace('/',DS,$img)) ){
            return 	'<img src="'. U_PHOTO . $img .'?height=40" style="max-height:40px;max-width:100px;"  alt="[--]" />';
        }else{
            return '<i class="icon-picture" title="'.$img.'"></i>';
        }
    }
    function sortable($k,$v){
        return 	'<input type="number" data-name="'. $k .'" value="'.$v.'" style="width:50px;" class="sortable_field" />';
    }

    function getListSqlSearch(){
        $val = false;
        $found = false ;
        $val .= 'WHERE (`' . $this->name . '`.id LIKE ' . SQL::v2txt($this->search_val . '%');


        //$this->fields to full search

        foreach($this->view as $fld){
            $val .= ' OR `' . $this->name . '`.' . $fld . ' LIKE ' . SQL::v2txt($this->search_val . '%');
            $found = true;
        }
        foreach($this->__r_fields as $fld){
            $val .=  ' OR '. $fld . ' LIKE ' . SQL::v2txt($this->search_val . '%');
            $found = true;
        }
        $val .= ') ' ;
        return $val;
    }


    function getFilterOrderParam(){
        if(get('search'))
            $this->search_val = get('search') ;

        if (get('sort') && get('order') )
            $this->order_val[get('sort')] =  get('order');

        $order = post('order') ? post('order') : get('order');
        if ($order && is_array($order) && count($order)){
            foreach($order as $k=>$v){
                if($v){	$this->order_val[$k] =  $v;	}
            }
        }
        $filter = post('filter') ? post('filter') : get('filter');

        if ($filter && is_array($filter) && count($filter)){
            foreach($filter as $k=>$v){
                if ($v){$this->filter_val[$k] = $v;	}
            }
        }
    }

#endregion

#region Relation
    public $dataRelation = array();
    public $dataRelationSql = array();

//    public $relation = array(); // Arrays
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
                $sql .=' `'. $v['tbl'] .'`.`'. $v['left_key'] .'` =  ' . ( ( array_key_exists ($v['right_key'],$this->data ) && $this->data[ $v['right_key'] ] ) ?  $this->data[ $v['right_key'] ] : '0' );
                $keys = $this->db->fetch($sql);
                foreach($keys as $k){	$k_ids[] = $k['k_id'] ;		}
                $this->dataRelation[$v['tbl']]	 = $k_ids  ;
            }
        }
    }

    public function initRelationsObject($IS_CALLED_BY_ME_RECURSION_PROTECTION = false){
        //p($this);
        if ($this->initializedRelationsObject ) return ;	$this->initializedRelationsObject = true; 		//protect

        foreach($this->relation as $k=>$v){
            if (!isset(Ctrl::$tableInstances[$v['tbl']])){
                p('The relation table ['.$v['tbl'].'] not found in the tables collection for relation : '.$k);
                die ;
            }



            $obj							= clone Ctrl::$tableInstances[$v['tbl']]; // allways clone

            if ($v['type'] == RelationType::Simple){							$obj->viewtype = 'SELECT-EDIT' ;			}
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

        if ($this->initializedInnerRelations ) return ;	$this->initializedInnerRelations = true;

        foreach($this->relations as &$obj){

            //Simple
            if ($obj->keys['type'] == RelationType::Simple){
                if (!$IS_CALLED_BY_ME_RECURSION_PROTECTION){ 	// ive disabled the protection because of  same table (as parent tree) list not loaded !
                    $obj->initializedRelationsObject = false;
                    $obj->initInnerRelations( 1 );

                    if ($this->id>0)
                        $obj->selected = array($this->data[$obj->keys['left_key']]) ;
                    if(count($obj->selected)){
                        $obj->sqlParam  = ' WHERE `'.$obj->name.'`.id IN( '. implode( ',',$obj->selected ) .' ) ' ;
                        $obj->v4_initList();
                        $obj->setSelected(1);
                    }else{
                        $obj->v4_initList() ;
                        $obj->setSelected(0);
                    }
                }
            }

            // Simple Inner
            if ($obj->keys['type'] == RelationType::InnerSimple){

                if (!$IS_CALLED_BY_ME_RECURSION_PROTECTION){ 	// ive disabled the protection because of  same table (as parent tree) list not loaded !

                    $obj->initializedRelationsObject = false;
                    $obj->initInnerRelations( 1 );
                    $obj->v4_initList() ;

                    if ($this->id>0  && count($this->data))
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

                    $obj->v4_initList() ;

                    $obj->setSelected(1);
                }
            }

            // Selectable list
            if ($obj->keys['type'] ==RelationType::ManyToManySelect){

                $obj->initInnerRelations( false );


                $obj->v4_initList() ;
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
                $obj->v4_initList() 	;
                $obj->setSelected(1);
            }

            if ($obj->keys['type'] == RelationType::ManyToOneByKey){
                if ($this->id < 1) {continue ;}
                $obj->selected = $this->dataRelation[$obj->keys['tbl']] ;
                $obj->sqlParam = ' WHERE `'. $obj->keys['tbl'] .'`.`'. $obj->keys['left_key'] .'` =  ' . ($this->data[ $obj->keys['right_key'] ] ?  $this->data[ $obj->keys['right_key'] ] : '0' );
                $obj->v4_initList() 	;
                $obj->setSelected(1);
            }
        }
        unset($obj);
    }


    function deleteRelations(){ /*del M2M relation only !!! */

        if( ! count($this->relation) ){ 		return ; }
        if ($this->id < 0){ 						return ; }
        foreach ($this->relation as $k=>$v ) {
            if (isset($v['readonly']) && $v['readonly']){}else{
                if ($v['type'] == RelationType::ManyToMany ||
                    $v['type'] == RelationType::ManyToManySelect){
                    $this->db->query('DELETE  FROM `'. $v['by_tbl']. '` WHERE `' . $v['right_key']. '` = ' . $this->id );
                }
                if ($v['type'] == RelationType::ManyToOne){
                    $this->db->query( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = 0 WHERE `' . $v['left_key']. '` = ' . $this->id );
                }
                if ($v['type'] == RelationType::ManyToOneByKey){
                    $this->db->query( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key']  . '` = 0 WHERE  `'. $v['tbl'] .'`.`'. $v['left_key'] .'` =  ' . ($this->post_data[ $v['right_key'] ] ?  $this->post_data[ $v['right_key'] ] : '0' ) );
                }
            }
        }
    }

    function addRelations($dupplicate = false){
        if( ! count($this->relation) ){ 		return ; }
        if ($this->id < 0){ 						return ; }

        foreach ($this->relation as $k=>$v ) {
            if (isset($v['readonly']) && $v['readonly']){  }else{
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
                        $this->db->query( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = ' .
                            ($this->post_data[ $v['right_key'] ] ?  $this->post_data[ $v['right_key'] ] : '0' )  . ' WHERE id IN(' . join($k_ids,",") . ') ' );
                    $this->db->query( 'DELETE  FROM `'. $v['tbl']. '` WHERE `' . $v['left_key']. '` =  0 '  );
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
                        $this->db->query($insert_sql );
                    }
                }

                if ($v['type'] ==  RelationType::ManyToOne){
                    if (count($k_ids)>0)
                        $this->db->query( 'UPDATE `'. $v['tbl']. '` SET `' . $v['left_key'] . '` = ' . $this->id . ' WHERE id IN(' . join($k_ids,",") . ') '  );
                    $this->db->query( 'DELETE  FROM `'. $v['tbl']. '` WHERE `' . $v['left_key']. '` =  0 '  );
                }
            }

        }
    }

#endregion

#region Table
    public $debug = false;

    public $db = null;

    public $name = '';
    public $fields = array();
    public $fields_pairs = array();

    public $data = array();
    public $post_data = array();

    /**
     * initialize the $data property to his default state
     */
    function initData(){			foreach($this->fields as $k){	$this->data[$k]	 = '';		}		}
    /**
     * will initialise the ->post_data array property with the data posted
     * Important !!! will initialise only the submited data
     */
    function initPostData(){
        if (count($this->fields) == 0 ) {
            p('Error retriving fields list from the database DB :');
            p('checkup for db->ctypes() for table '. $this->name) ;
            die ;
        }
        foreach($this->fields as $k){	if (isset($_POST[$k])) { $this->post_data[$k]	 = post($k);	}}
        //Observe to check more unsbmited data !
        if ($this->debug) { fb('fields , fields_pairs, post data   , post_data  :');fb($this->fields); fb($this->fields_pairs); fb($_POST); fb($this->post_data) ; }}
    /**
     * initialise the $data property to data from the database
     * @return
     */
    function initDbData(){
        if ($this->id < 1){ return ;}
        $this->initListSql();
        $sql = $this->sql_rows=  'SELECT '
            . $this->sql_fields
            . $this->sql_extra_fields
            . ' FROM '
            . $this->sql_tables
            . $this->sql_left_joins
            . $this->sql_inner_joins
            . ' WHERE `'.$this->name.'`.id = '.$this->id  ;
        $res = $this->db->fetchRow($sql);
        if(count($res)){$this->data = $res; }else{	fb('unable to get object row data ' . $this->id . ' '. $this->name .' '. $sql ); $this->id = 0; }
    }
    /**
     * minimaliste AdminTable constructior
     * will init the current
     * 1. selected id
     * 2. fileds
     * 3. db reference
     * @param $name is database table name
     */
    public function __construct($name){
        global $db;
        $this->db = $db ; // should use & by ref for performences ?
        $this->id 		=  (int)(post('id')) ? (int)(post('id')) :   (int)(get('id'));
        $this->name = $name ;
        $this->fields_pairs = $this->db->ctypes( $this->name ) ;
        $this->fields = array_filter(array_keys($this->fields_pairs  ), create_function('$kyes','return $kyes!="id";')) ;
    }
    /**
     * Will Add submited row to the database
     * also will add relations rows
     */
    public function Add(){
        $this->initPostData() ;
        if ($this->id = $this->db->query(SQL::build('INSERT',$this->name,$this->post_data) ) ){
            $this->deleteRelations() ;
            $this->addRelations() ;
        }else{
            Debug('Post add db error '. $this->db->last_error);
        }
    }
    /**
     * Will duplicate database row by id
     * also will dupplicate relations rows
     */
    public function Dup(){
        $this->initData(); //empty for fields keys only
        $this->initDbRelationData() ;
        if ($this->id = $this->db->query(SQL::build('DUPLICATE',$this->name,$this->data,$this->id) ) ){
            $this->addRelations( true ) ;
        }else{
            Debug('Post duplicate db error '. $this->db->last_error);
        }
    }
    /**
     * Will save submited data to database row
     * will affect changes in relations
     * @return
     */
    public function Edit(){
        $this->initPostData() ;
        if ($this->id < 0) return ;
        if ($this->db->query(SQL::build('UPDATE',$this->name,$this->post_data,$this->id) ) ){
            $this->deleteRelations() ;
            $this->addRelations() ;
        } else{
            Debug('Post edit db error '. $this->db->last_error);
        }
    }
    /**
     * Will remove permently the row from the database also will delete  relations data
     */
    public function Delete(){
        if ($this->db->query('DELETE  FROM `'.$this->name.'` WHERE id = '. $this->id) ){
            $this->deleteRelations() ;
        }else{
            Debug('Post delete db error '. $this->db->last_error);
        }
        $this->id =  0;
    }
#endregion
}

?>