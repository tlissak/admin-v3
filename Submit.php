<?php

class Submit{

    /**
     * @var Loader
     */
    private $parent ;
    public $id ;
    public $name ;
    public $form ;


    public function __construct(Loader &$p){
        $this->parent   = $p ;
        $this->name     = $p->name ;
        $this->form     = $p->Form;
        $this->id       = $p->Form->id ;
    }

    private $PostAction = array('add','mod','del','dup') ;


    public function Save(Loader $loader) {

        if ($this->parent->protected) {
            return ;
        }
        $this->action = post('form_submit_action_type') ;

        if (!in_array($this->action,$this->PostAction)){
            p('Action type not set in form');
        }

        if ($this->action == 'add') {
            $this->Add();
        } elseif ($this->action == 'dup') {
            $this->Dup();
        } elseif ($this->action == 'mod') {
            $this->Edit();
        }elseif ($this->action == 'del') {
            $this->Delete();
        }
        //if ($this->action == "add" || $this->action == 'mod' || $this->action == 'dup' ){
        //    $this->initDbData();
        //}

        return $this->id;

    }

    public function Add(){
        global $db;
        $this->form->initPostData() ;
        if ($this->id = $db->query(SQL::build('INSERT',$this->name,$this->form->post_data) ) ){
            //$this->deleteRelations() ;
            //$this->addRelations() ;
        }else{
            p('Post add db error '. $db->last_error);
        }
    }

    public function Dup(){
        global $db;
        $this->form->initData(); //empty for fields keys only
        //$this->initDbRelationData() ;
        if ($this->id = $db->query(SQL::build('DUPLICATE',$this->name,$this->form->data,$this->id) ) ){
          //  $this->addRelations( true ) ;
        }else{
            p('Post duplicate db error '. $db->last_error);
        }
    }

    public function Edit(){
        global $db;
        $this->initPostData() ;
        if ($db->query(SQL::build('UPDATE',$this->name,$this->form->post_data,$this->id) ) ){
           // $this->deleteRelations() ;
           // $this->addRelations() ;
        } else{
            p('Post edit db error '. $db->last_error);
        }
    }

    public function Delete(){
        global $db;
        if ($db->query('DELETE  FROM `'.$this->name.'` WHERE id = '. $this->id) ){
            //$this->deleteRelations() ;
        }else{
            p('Post delete db error '. $db->last_error);
        } //$this->id =  0;
    }
}

?>