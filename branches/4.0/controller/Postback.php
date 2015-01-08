<?php

class Postback{

    /**
     * @var Loader
     */
    private $parent ;

    /**
     * @var Db
     */
    private $db ;

    public $name ;
    public $form ;

    private $action ;
    private $_id ;


    public $VIRTUAL_MODE = true ;

    public $sql = array();
    public $debug = array() ;

    public $cleanup_relation_data = false ;

    public function __construct(Loader &$p){
        $this->parent   = &$p ;
        $this->db       = &$p->db ;
        $this->name     = &$p->name ;
        $this->form     = &$p->Form;
        $this->_id      = $p->id ;
    }

    private $PostAction = array('add','mod','del','dup') ;


    public function deleteRelations() {
        if ($this->VIRTUAL_MODE) return true;

        foreach ($this->parent->relations_instances as $r){
            if ($r->type == 'ManyToMany' || $r->type == 'ManyToManySelect'){

                //$sql = 'DELETE  FROM `'. $r->by_tbl . '` WHERE `' .$r->right_key . '` = ' . $this->_id  ;
                $sql = 'UPDATE `'. $r->by_tbl . '` SET `' .$r->right_key . '` = -' . $this->_id .' WHERE `' .$r->right_key . '` = ' . $this->_id  ;
                $this->sql[] = $sql;
                $this->db->query($sql);
            }
            if ($r->type == 'ManyToOne' ) {
                $sql =  'UPDATE `'. $r->name . '` SET `' .$r->left_key  . '` = -'.$this->_id.' WHERE `' . $r->left_key . '` = ' . $this->_id ;
                $this->sql[] = $sql;
                $this->db->query( $sql);
            }
            if ($r->type == 'ManyToOneByKey'){
				$sql =  'UPDATE `'. $r->name . '` SET `'.$r->left_key  . '` = -1
					WHERE  `'. $r->name .'`.`'.$r->left_key .'` =  ' .
                    (isset($this->form->data_posted[ $r->right_key ]) && $this->form->data_posted[ $r->right_key ] ?  $this->form->data_posted[ $r->right_key ] : '-1' ) ;
                $this->sql[] = $sql;
                $this->db->query($sql ) ;
			}
        }
    }

    public function addRelations($duplicate = false) {
        if ($this->VIRTUAL_MODE) return true;

        foreach ($this->parent->relations_instances as $r){

            if (!$duplicate) {
                $ids_left_key = post($r->left_key) ? post($r->left_key) : array() ;
            }else{
                $this->debug[] = 'duplicate relation value';
                $ids_left_key = array() ;
                if ($r->type ==  'ManyToMany' ||  $r->type ==  'ManyToManySelect'){

                    $sql = 'SELECT `'.$r->left_key .'` AS k_id FROM `'. $r->name . '` WHERE `'.$r->right_key.'` = ' . $this->_id  ;
                    $keys = $this->db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }elseif ($r->type ==  'ManyToOne' ){
                    $sql = 'SELECT id AS k_id FROM `'.$r->name .'` WHERE `'. $r->left_key .'` =  ' . $this->_id ;
                    $keys = $this->db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }elseif ($r->type ==  'ManyToOneByKey' ){
                    $sql = 'SELECT id AS k_id FROM `'.$r->name.'` WHERE ' ;
                    $sql .=' `'. $r->name.'`.`'. $r->left_key .'` =  ' . ( ( array_key_exists ($r->right_key,$this->data ) && $this->data[ $r->right_key ] )
                            ?  $this->data[ $r->right_key ] : '0' );
                    $keys = $this->db->fetch($sql);
                    foreach($keys as $k){	$ids_left_key[] = $k['k_id'] ;		}
                }
            }

            if ($r->type ==  'ManyToOneByKey' && !$duplicate ){
                $this->debug[] = "Processing Relation ". $r->name . ' of type '. $r->type  ;
                if (count($ids_left_key)>0) {
                    $sql = 'UPDATE `'. $r->name. '` SET `' . $r->left_key . '` = ' .
                        ($this->form->data_posted[ $r->right_key ] ?  $this->form->data_posted[ $r->right_key ] : '-1' )
                        . ' WHERE id IN(' . implode(",",$ids_left_key) . ') ' ;
                    $this->sql[] = $sql;
                    $this->db->query($sql);
                }
                if ($this->cleanup_relation_data) {
                    $sql2 = 'DELETE  FROM `'. $r->name. '` WHERE `' . $r->left_key. '` < 0 '  ; //=0
                    $this->sql[] = $sql2;
                    $this->db->query(  $sql2);
                }
            }

            if ($r->type ==  'ManyToMany' || $r->type == 'ManyToManySelect'){
                $this->debug[] = "Processing Relation ". $r->name . ' of type '. $r->type  ;
                if (count($ids_left_key)){

                    $sql2 = '';
                    foreach( $ids_left_key as $id){
                        $sql2 .= ( $sql2 == '' ) ?
                            'INSERT INTO `'. $r->by_tbl .'` (`'.$r->left_key.'`,`'.$r->right_key.'`)
                                SELECT '.$id.','.$this->_id
                            : ' UNION SELECT '.$id .','. $this->_id  ;
                    }
                    $this->sql[] = $sql2;
                    $this->db->query($sql2 );
                }
            }

            if ($r->type ==  'ManyToOne' && !$duplicate){
                $this->debug[] ="Processing Relation ". $r->name . ' of type '. $r->type  ;
                if (count($ids_left_key)>0) {
                    $sql = 'UPDATE `'. $r->name. '` SET `' . $r->left_key . '` = ' . $this->_id . ' WHERE id IN(' . implode(",",$ids_left_key) . ') '  ;
                    $this->sql[] = $sql;
                    $this->db->query($sql);
                }
                if ($this->cleanup_relation_data) {
                    $sql2 = 'DELETE  FROM `'. $r->name. '` WHERE `' . $r->left_key. '` <  0 '  ;//=0
                    $this->sql[] = $sql2;
                    $this->db->query( $sql2);
                }
            }
        }
    }

    public function Set(){

        $this->action = get('action');

        if ($this->parent->protected) {   return '{"error":"Table is protected","details":"'.$this->parent->name.'"->protected"}'; }

        if (!in_array($this->action,$this->PostAction)){
            return '{"error":"Action type not set in form","details":"get(action) == ('.$this->action.')"}';
        }

        $out = array("id"=>$this->_id,"tbl"=>$this->parent->name,'left-key'=>$this->parent->name,"action"=>$this->action);
        $out['message'] = $this->action;

        if ($this->action == 'add') {
            $out['status'] = $this->Add() ? 201 : 501 ;
        } elseif ($this->action == 'dup') {
            $out['status'] = $this->Dup() ? 202 : 502 ;
        } elseif ($this->action == 'mod') {
            $out['status'] = $this->Edit() ? 203 : 503 ;
        }elseif ($this->action == 'del') {
            $out['old_id'] = $this->_id;
            $out['status'] = $this->Delete() ? 204 : 504 ;
        }

        if ($this->action == "add" || $this->action == 'mod' || $this->action == 'dup' ){
            if ($this->VIRTUAL_MODE) {
                $out['row'] = $this->form->data_posted ;
                $out['row']['id'] = 0 ;
            }else {
                $out['id'] = $this->_id;
                $this->parent->id = $this->_id;
                $this->form->initDbData();
                //
                $out['row'] = $this->form->data ; // Should contains ID key
            }
        }
        $out['sql'] = $this->sql ;

        return json_encode($out);

    }

    public function Add(){

        $this->form->initPostData() ;

        if ($this->VIRTUAL_MODE) return true;

        $sql = $this->db->build('INSERT',$this->name,$this->form->data_posted) ;
        if ($this->_id = $this->db->query($sql ) ){
            $this->addRelations() ;
            return true;
        }else{
            p('Post add db error '.$sql  . $this->db->last_error);
            return false;
        }
    }

    public function Dup(){

        $this->form->initData();

        if ($this->VIRTUAL_MODE) return true;

        $data = array_filter( $this->form->data , function($kyes){ return $kyes !="id" ;},ARRAY_FILTER_USE_KEY ) ;

        $sql = $this->db->build('DUPLICATE',$this->name,$data,$this->_id) ;
        if ($this->_id = $this->db->query($sql ) ){
            $this->addRelations( true ) ;
            return true;
        }else{
            p('Post duplicate db error '.$sql .  $this->db->last_error);
            return false;
        }
    }

    public function Edit(){

        $this->form->initPostData() ;

        if ($this->VIRTUAL_MODE) return true;

        $sql = $this->db->build('UPDATE',$this->name,$this->form->data_posted,$this->_id );

        if ($this->db->query($sql)  ){
            $this->deleteRelations() ;
            $this->addRelations() ;
            return true;
        } else{
            p('Post edit db error '. $this->db->last_error);
            return false;
        }
    }

    public function Delete(){

        if ($this->VIRTUAL_MODE) return true;

        $sql = 'DELETE  FROM `'.$this->name.'` WHERE id = '. $this->_id ;
        if ($this->db->query($sql) ){
            $this->deleteRelations() ;
            return true;
        }else{
            p('Post delete db error '. $this->db->last_error);
            return false ;
        }
    }
}

?>