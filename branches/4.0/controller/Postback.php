<?php

class Postback{

    /**
     * @var Loader
     */
    private $parent ;

    public $name ;
    public $form ;

    private $action ;
    private $_id ;

    public $Message ='';

    public function __construct(Loader &$p){
        $this->parent   = &$p ;
        $this->name     = &$p->name ;
        $this->form     = &$p->Form;
        $this->_id       = $p->id ;
    }

    private $PostAction = array('add','mod','del','dup') ;


    public function deleteRelations() {
        global $db ;
        foreach ($this->parent->relations_instances as $r){
            if ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect'){

                $sql = 'DELETE  FROM `'. $r->by_tbl . '` WHERE `' .$r->right_key . '` = ' . $this->_id  ;
                p($sql);
                //$db->query($sql);
            }
            if ($r->type == 'ManyToOne' ) {
              $sql =  'UPDATE `'. $r->name . '` SET `' .$r->left_key  . '` = 0 WHERE `' . $r->left_key . '` = ' . $this->_id ;
                p($sql);
                //$db->query( $sql);
            }


            if ($r->type == 'ManyToOneByKey'){
				$sql =  'UPDATE `'. $r->name . '` SET `'.$r->left_key  . '` = 0
					WHERE  `'. $r->name .'`.`'.$r->left_key .'` =  ' . ($this->form->data_posted[ $r->right_key ] ?  $this->form->data_posted[ $r->right_key ] : '0' ) ;
                p($sql);
                //$db->query($sql ) ;
			}

        }


    }
    public function addRelations($duplicate = false) {

        global $db;
        foreach ($this->parent->relations_instances as $r){

            if (!$duplicate) {
                $ids_left_key = post($r->left_key) ? post($r->left_key) : array() ;
            }else{
                $ids_left_key = array() ;
                if ($r->type ==  'ManyToMany' ||  $r->type ==  'ManyToManySelect'){
                    $sql = 'SELECT `'.$r->left_key .'` AS k_id FROM `'. $r->name . '` WHERE `'.$r->right_key.'` = ' . $this->_id  ;
                    $keys = $db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }elseif ($r->type ==  'ManyToOne' ){
                    $sql = 'SELECT id AS k_id FROM `'.$r->name .'` WHERE `'. $r->left_key .'` =  ' . $this->_id ;
                    $keys = $db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }elseif ($r->type ==  'ManyToOneByKey' ){
                    $sql = 'SELECT id AS k_id FROM `'.$r->name.'` WHERE ' ;
                    $sql .=' `'. $r->name.'`.`'. $r->left_key .'` =  ' . ( ( array_key_exists ($r->right_key,$this->data ) && $this->data[ $r->right_key ] )
                            ?  $this->data[ $r->right_key ] : '0' );
                    $keys = $db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }
            }

            if ($r->type ==  'ManyToOneByKey' ){
                if (count($ids_left_key)>0) {
                    $sql = 'UPDATE `'. $r->name. '` SET `' . $r->left_key . '` = ' .
                        ($this->form->data_posted[ $r->right_key ] ?  $this->form->data_posted[ $r->right_key ] : '0' )
                        . ' WHERE id IN(' . implode(",",$ids_left_key) . ') ' ;
                    p($sql);
                    //$db->query($sql);
                }
                $sql2 = 'DELETE  FROM `'. $r->name. '` WHERE `' . $r->left_key. '` =  0 '  ;

                p($sql2);
                //$db->query(  $sql2);
            }

            if ($r->type ==  'ManyToMany' || $r->type == 'ManyToManySelect'){
                if (count($ids_left_key)){

                    $sql2 = '';
                    foreach( $ids_left_key as $id){
                        $sql2 .= ( $sql2 == '' ) ?
                            'INSERT INTO `'. $r->by_tbl .'` (`'.$r->left_key.'`,`'.$r->right_key.'`)
                                SELECT '.$id.','.$this->_id
                            : ' UNION SELECT '.$id .','. $this->_id  ;
                    }
                    p($sql2);
                   // $db->query($sql2 );
                }
            }

            if ($r->type ==  'ManyToOne'){
                if (count($ids_left_key)>0) {
                    $sql = 'UPDATE `'. $r->name. '` SET `' . $r->left_key . '` = ' . $this->_id . ' WHERE id IN(' . implode(",",$ids_left_key) . ') '  ;
                    p($sql);
                    //$db->query($sql);
                }
                $sql2 = 'DELETE  FROM `'. $r->name. '` WHERE `' . $r->left_key. '` =  0 '  ;
                p($sql2);
                //$db->query( $sql2);
            }
        }

    }

    public function Set(){

        $this->action = get('action');

        if ($this->parent->protected) {            return ;        }

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


        if ($this->action == "add" || $this->action == 'mod' || $this->action == 'dup' ){
            $this->parent->id = $this->_id;
            $this->form->initDbData();
        }



        $this->Message = "Saved :" ;

        return $this->_id;

    }

    public function Add(){
        global $db;
        $this->form->initPostData() ;
        $sql = SQL::build('INSERT',$this->name,$this->form->data_posted) ;
        if ($this->_id = $db->query($sql ) ){

            $this->deleteRelations() ;
            $this->addRelations() ;
        }else{
            p('Post add db error '.$sql  . $db->last_error);
        }
    }

    public function Dup(){
        global $db;
        $this->form->initData();

        $data = array_filter( $this->form->data , function($kyes){ return $kyes !="id" ;},ARRAY_FILTER_USE_KEY ) ;

        $sql = SQL::build('DUPLICATE',$this->name,$data,$this->_id) ;
        if ($this->_id = $db->query($sql ) ){
            $this->addRelations( true ) ;
        }else{
            p('Post duplicate db error '.$sql .  $db->last_error);
        }
    }

    public function Edit(){
        global $db;
        $this->form->initPostData() ;
        $sql = SQL::build('UPDATE',$this->name,$this->form->data_posted,$this->_id );

        if ($db->query($sql)  ){
            $this->deleteRelations() ;
            $this->addRelations() ;
        } else{
            p('Post edit db error '. $db->last_error);
        }
    }

    public function Delete(){
        global $db;
        $sql = 'DELETE  FROM `'.$this->name.'` WHERE id = '. $this->_id ;
        if ($db->query($sql) ){
            $this->deleteRelations() ;
        }else{
            p('Post delete db error '. $db->last_error);
        } //$this->_id =  0;
    }
}

?>