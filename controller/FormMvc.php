<?php

class FormMvc
{
    /**
     * @var Loader
     */
    private $parent;

    public $id;

    public $panels = array() ;
/*
    public function GetHeader(){
        //TODO: hidden controls like ID , Action / Buttons controls depandes on Action
    }
*/

    public function GetPanels(){

        //TODO Get Relation lists panels

        /* init data */
        $this->id = get('id') ? intval(get('id')) : 0;

        $this->parent->Form->initData();

        if ($this->id) {
            $this->parent->Form->initDbData();
        } else {
            $this->parent->Form->initPostData();
        }



        $this->parent->formPanel = array_merge( array('default'=>array('title'=>$this->parent->title,'icon'=>'icon ion-compose','cont'=>'','pull'=>"pull-right-lg")) , $this->parent->formPanel );
        //p($this->parent->formPanel);die ;

        //init panel fields
        $panelFields = array();
        foreach($this->parent->formPanel as $k=>$value){ $panelFields[$k] = '' ;}

        //assign fields to the panel fields
        foreach($this->parent->formFields as $fld){
            $pnl = $fld['opts'] && isset($fld['opts']['panel']) && $fld['opts']['panel'] ? $fld['opts']['panel'] : 'default' ;
            if (! isset($panelFields[$pnl])){
                p('Panel not found '. $pnl);
            }else {
                $panelFields[$pnl] .= $this->Control($fld) . NL;
            }
        }


        $cst = array('name'=>'tbl','value'=>$this->parent->name,'type'=>'hidden');
        $panelFields['default'] .= $this->Control($cst) . NL;
        $cst = array('name'=>'set_form_ajax','value'=>1,'type'=>'hidden');
        $panelFields['default'] .= $this->Control($cst) . NL;
        $cst = array('name'=>'id','value'=>get('id'),'type'=>'hidden');
        $panelFields['default'] .= $this->Control($cst) . NL;

        $out = '';
        foreach($this->parent->formPanel as $key=>&$pnl){
            $pnl['cont'] = $panelFields[$key] ;
            $out .= $this->parent->PanelMvc->RenderPanel($key,$pnl,'Form') ;
        }

        return $out ;
    }



    public function __construct(Loader &$p)
    {
        $this->parent = $p;
    }



    private function Control($a)
    {


        $fld = $a['name'];
        $type = $a['type'];

        $label = isset($a['title']) ?$a['title'] : false;
        $opt = isset($a['opts'] ) ? $a['opts'] : false ;

        if ($fld != 'set_form_ajax' && $fld != 'tbl' ) // && $fld != 'id'
            $value = post('set_form_ajax') ? $this->parent->Form->data_posted[$fld] : $this->parent->Form->data[$fld]  ;

        $value = isset($a['value']) ? $a['value'] : $value ;


// if (! array_key_exists($fld,$d)){fb('Trying to create field  "'. $fld . '" but the table ("'.$this->name.'")  does not have this field  ');}

        $out = "";

        if ($label) {
            $out .= '<div class="form-group"><label class="col-sm-4 control-label" for="fld_' . $fld . '">' . $label . ' :</label>' . NL;
            $out .= '<div class="input-group col-sm-8">';
        }
        $extends = ' autocomplete="off" ';

        if ($type == 'file'){
            $extends .= ' data-url="?tbl=' . $this->parent->name . '&fld=' . $fld . '&upload=1" ' ;
        }

        if ($opt) {
            if (isset($opt['required'])) {
                $extends .= ' data-require="true" required ';
            }
            if (isset($opt['pattern'])) {
                $extends .= ' data-pattern="' . $opt['pattern'] . '" ';
            }
            if (isset($opt['readonly'])) {
                $extends .= ' readonly="readonly" ';
            }
            if (isset($opt['extends'])) {
                $extends .= $opt['extends'];
            }
            if (isset($opt['default_value'])) {
                //
            }
        }


        if ($type == 'color') {
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control color_picker"   data-type="color"  data-limit="7" ' . $extends . '  />';
        }
        if ($type == 'date') {
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control date_picker"  data-type="date" data-limit="10" ' . $extends . ' />';
        }
        if ($type == 'text') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-font"></i> </span>
        <input type="' . $type . '" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" ' . $extends . ' class="form-control" data-limit="255" />';
        }
        if ($type == 'zipcode') {
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="zipcode" class="form-control" data-limit="5" ' . $extends . ' />';
        }
        if ($type == 'number') {
            $out .= '<span class="input-group-addon">0-9 </span>
        <input type="number" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="int" class="form-control" data-limit="11" ' . $extends . ' />';
        }
        if ($type == 'email') {
            $out .= '<span class="input-group-addon"><i class="fa fa-at"></i> </span>
        <input type="email" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="email" class="form-control" data-limit="100" ' . $extends . ' />';
        }
        if ($type == 'float') {
            $out .= '<span class="input-group-addon">0.00</span>
        <input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="float"  ' . $extends . ' />';
        }
        if ($type == 'price') {
            $out .= '<span class="input-group-addon">€</span>
        <input type="number" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" step="any"  data-type="price" ' . $extends . ' />';
        }
        if ($type == 'sort') {
            $out .= '<span class="input-group-addon"><i class="fa fa-sort"></i></span>
        <input type="number" name="' . $fld . '"id="fld_' . $fld . '" value="' . $value . '" class="form-control" step="any"  data-type="float" data-limit="50" ' . $extends . ' />';
        }
        if ($type == 'url') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
        <input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '"  class="form-control" data-type="url" data-limit="255" ' . $extends . ' />';
        }
        if ($type == 'phone') {
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="phone" data-limit="20" ' . $extends . ' />';
        }
        if ($type == 'password') {
            $out .= '<input type="' . $type . '" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="password" data-limit="50" ' . $extends . ' />';
        }
        if ($type == 'checkbox' || $type == 'check') {
            $out .= '<span class="cbr"><input type="checkbox" name="' . $fld . '" id="fld_' . $fld . '" value="1"  ' . (($value) ? ' checked="checked" ' : 'checked="checked"') . ' ' . $extends . ' /><i class="fa fa-check"></i></span>' ;
        }
        if ($type == 'textarea') {
            $out .= '<span class="input-group-addon">-</span>
        <textarea name="' . $fld . '" id="fld_' . $fld . '" class="form-control" spellcheck="false" ' . $extends . '>' . $value . '</textarea>';
        }
        if ($type == 'rte') {
            $out .= '<textarea name="' . $fld . '" id="fld_' . $fld . '" class="form-control rte" spellcheck="false" ' . $extends . '>'
                . htmlentities($value, ENT_QUOTES, "UTF-8") . '</textarea>';
        }
        if ($type == 'file') {

            $h = '<div class="droparea"><span class="btn btn-mini btn-file"  >';
            $h .= '<i class="icon-plus"></i>  ' . l('browse or drop file here');
            $h .= '<input  name="' . $fld . '" type="file" ' . $extends . '  />'; // add data-path ;
            $h .= '</span>';
            $h .= '<div class="files">';
            $h .= '<input type="hidden" name="' . $fld . '" id="fld_fld_' . $fld . '" value="' . $value . '" ' . $extends . ' />';
            if (V2_IMG && is_image('../' . $value)) {
                $h .= '<img src="../' . $value . '?width=200" style="max-height:100px; max-width:100px;"  />';
            } elseif (is_file(P_PHOTO . $value)) {//current state ;
                if (is_image(P_PHOTO . $value)) {
                    $h .= '<img src="' . U_PHOTO . $value . '?width=200" style="max-height:100px; max-width:100px;"  />';
                } elseif (is_pdf(P_PHOTO . $value)) {
                    $h .= '<a href="' . U_PHOTO . $value . '" target="_blank"><img src="img/pdf.jpg"  /></a>';
                } else {
                    $h .= $value;
                }
            } else {
                $h .= '*** FILE NOT FOUND ('.$value.') ***';
            }
            $h .= '<a href="javascript:" class="btn link-delete-file" data-path="' . urlencode($value) . '" ><i class="icon-trash"></i></a>';
            $h .= '</div></div>';
            $out .= $h;
        }
        if ($type == 'hidden') {
            $out .= 'WWWWW<input type="hidden" name="' . $fld . '" id="hdn_fld_' . $fld . '" value="' . $value . '" ' . $extends . ' />'; //
        }
        if ($type == 'range') {
            $out .= '<input type="range" name="' . $fld . '" id="fld_' . $fld . '" class="form-control" data-type="int" value="' . ($value ? $value : 0) . '" step="1" data-limit="11" max="' . $opt['max'] . '" min="' . $opt['min'] . '" ' . $extends . ' /><span>' . $value . '</span>';
        }
        if ($label) {
            $out .= '</div></div>';
        }
        return $out;
    }


}
