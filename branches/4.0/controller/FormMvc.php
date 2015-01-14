<?php

class FormMvc
{
    /**
     * @var Loader
     */
    private $parent;

    public $id;

    public $panels = array() ;




    public function GetPanels(){


        /* init data */

        $this->parent->Form->initData();

        if ($this->parent->id) {
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




        //$cst = array('name'=>'action','value'=>$this->parent->id ? 'mod' : 'add' ,'type'=>'hidden');
        //$panelFields['default'] .= $this->Control($cst) . NL;

        $out = '';
        foreach($this->parent->formPanel as $key=>&$pnl){
            $out .= $this->parent->PanelMvc->RenderPanel($key.'-form',$panelFields[$key],'form',$pnl['title']. ' form' ,'') ;
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
        $value = false ;

        if ($fld != 'set_form_ajax' && $fld != 'tbl' && $type != 'html' ) // && $fld != 'id'
            $value = post('set_form_ajax') ? $this->parent->Form->data_posted[$fld] : $this->parent->Form->data[$fld]  ;

        $value = isset($a['value']) ? $a['value'] : $value ;


// if (! array_key_exists($fld,$d)){fb('Trying to create field  "'. $fld . '" but the table ("'.$this->name.'")  does not have this field  ');}

        $out = "";

        if($label){
            $out .= '<div class="form-group"><label class="col-sm-4 control-label" for="fld_' . ($type == 'html' ? '' : $fld) . '">' . $label . ' :</label>' . NL;
            $out .= '<div class="input-group col-sm-8">' .NL;
        }

        if ($type == 'html'){
            $out .= $fld ;
        }

        $extends = ' autocomplete="off" ';

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
            $out .= '<span class="input-group-addon"><i class="fa fa-paint-brush"></i> </span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control color_picker"   data-type="color"  data-limit="7" ' . $extends . '  />';
        }
        if ($type == 'date') {
            $out .= '<span class="input-group-addon add-on">
                    <i class="glyphicon glyphicon-calendar" data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i> </span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control date_picker" data-type="date" data-limit="10" ' . $extends . ' />';
        }
        if ($type == 'text') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-font"></i> </span>';
            $out .= '<input type="' . $type . '" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" ' . $extends . ' class="form-control" data-limit="255" />';
        }
        if ($type == 'zipcode') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="zipcode" class="form-control" data-limit="5" ' . $extends . ' />';
        }
        if ($type == 'number') {
            $out .= '<span class="input-group-addon">0-9 </span>';
            $out .= '<input type="number" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="int" class="form-control" data-limit="11" ' . $extends . ' />';
        }
        if ($type == 'email') {
            $out .= '<span class="input-group-addon"><i class="fa fa-at"></i> </span>';
            $out .= '<input type="email" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" data-type="email" class="form-control" data-limit="100" ' . $extends . ' />';
        }
        if ($type == 'float') {
            $out .= '<span class="input-group-addon">0.00</span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="float"  ' . $extends . ' />';
        }
        if ($type == 'price') {
            $out .= '<span class="input-group-addon">€</span>';
            $out .= '<input type="number" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" step="any"  data-type="price" ' . $extends . ' />';
        }
        if ($type == 'sort') {
            $out .= '<span class="input-group-addon"><i class="fa fa-sort"></i></span>';
            $out .= '<input type="number" name="' . $fld . '"id="fld_' . $fld . '" value="' . $value . '" class="form-control" step="any"  data-type="float" data-limit="50" ' . $extends . ' />';
        }
        if ($type == 'url') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '"  class="form-control" data-type="url" data-limit="255" ' . $extends . ' />';
        }
        if ($type == 'phone') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="phone" data-limit="20" ' . $extends . ' />';
        }
        if ($type == 'password') {
            $out .= '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>';
            $out .= '<input type="text" name="' . $fld . '" id="fld_' . $fld . '" value="' . $value . '" class="form-control" data-type="password" data-limit="50" ' . $extends . ' />';
        }
        if ($type == 'checkbox' || $type == 'check') {
            $out .= '<span class="cbr"><input type="checkbox" name="' . $fld . '" id="fld_' . $fld . '" value="1"  ' . (($value) ? ' checked="checked" ' : 'checked="checked"') ;
            $out .= ' ' . $extends . ' /><i class="fa fa-check"></i></span>' ;
        }
        if ($type == 'textarea') {
            $out .= '<span class="input-group-addon"><i class="fa fa-paragraph"></i></span>';
            $out .= '<textarea name="' . $fld . '" id="fld_' . $fld . '" class="form-control" spellcheck="false" ' . $extends . '>' . $value . '</textarea>';
        }
        if ($type == 'rte') {
            $out .= '<textarea name="' . $fld . '" id="fld_' . $fld . '" class="form-control rte" spellcheck="false" ' . $extends . '>' ;
            $out .=  htmlentities($value, ENT_QUOTES, "UTF-8") . '</textarea>';
        }
        if ($type == 'file') {
            $out .= '<a class="btn" data-toggle="modal" data-target="#ModalFileManager"
             data-href="filemanager/dialog.php?type=2&field_id=fld_fld_' . $fld . '&base=&akey=7B6YhaP5en6B6lcxD5l3Bg">Open file manager</a>' ;
            $out .= '<input class="form-control change" readonly type="text" name="' . $fld . '" id="fld_fld_' . $fld . '" value="' . $value . '" ' . $extends . ' />';
            if (is_image(P_PHOTO . $value)){
                $out .= '<p><img src="'.U_PHOTO.$value.'" class="image_preview"></p>' ;
            }
        }
        if ($type == 'hidden') {
            $out .= '<input type="hidden" name="' . $fld . '" id="hdn_fld_' . $fld . '" value="' . $value . '" ' . $extends . ' />';
        }
        if ($type == 'range') {
            $out .= '<input type="range" name="' . $fld . '" id="fld_' . $fld . '" class="form-control" data-type="int" value="' . ($value ? $value : 0) . '" ';
            $out .= ' step="1" data-limit="11" max="' . $opt['max'] . '" min="' . $opt['min'] . '" ' . $extends . ' /><span>' . $value . '</span>';
        }
        if ($label) {
            $out .= NL . '</div></div>' ;
        }
        return $out . NL;
    }


}
