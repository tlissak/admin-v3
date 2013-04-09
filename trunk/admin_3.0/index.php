<? // v3
include('../inc/config.php') ;
require_once('inc/fb.php');
include('../inc/func.php') ;

$debug  	= new Debug() ;
$db 			= new Db() ;
$cookie 	= new Cookie('x_admin_user') ;

include('class/AdminList.php');
include('class/AdminMvc.php');
include('class/AdminRelation.php');
include('class/AdminTable.php');
include('class/AdminForm.php');
include('class/AdminLoader.php');
include('class/AdminController.php');

include(P_SITE) ;
$_LNG = Ctrl::$_LNG ;

$tbl 				= get('tbl') ;
$contexttbl 	= get('contexttbl');

$ctrl = new Ctrl() ;
$ctrl->initAuth();
$ctrl->dispacher() ;

if ($tbl && $ctrl && $ctrl->contextTable){  $contexttbl = $ctrl->contextTable->name ; }


?><!doctype html>
<html lang="fr">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin</title>
<? include('_inc.php') ; ?>
<link rel="stylesheet" type="text/css" href="css/layout.css">
<script src="js/plugin.3.0.js"></script>
<script src="js/UI.js"></script>
<script src="js/UI.docLoad.js"></script>
<script src="js/UI.formLoad.js"></script>
<script src="js/UI.formSubmit.js"></script>
<script src="js/UI.formValidate.js"></script>
<script src="js/UI.sqler.js"></script>
<script src="js/UI.pagingFilter.js"></script>
<script>
UI.docReady();
</script>
</head>
<body>
<div class="LAY">

<div class="LAY-left">
		<div  id="menu"> 
				   <? foreach(Ctrl::$tableInstances as $t) { if ( $t->show == 1 ) {   ?>                   
					<a class="btn <? c($t->name == $tbl ? 'active':'') ;?>" data-tbl="<? c($t->name);?>" href="?tbl=<? c($t->name);?>&menu=1" ><? c(l($t->name));?></a>
				   <? } }?>
                   
                </div>
                <hr />
                <div id="basecontrol" >
                    
                    <a href="?backup=1" target="_blank" class="btn-turquise"> <i class="icon-archive" ></i> Sauvgarder</a>
					<a href="#sqler" class="btn-orange sqler" > <i class="icon-mysql-dolphin"></i>  Sqler</a>
                    <a href="?logout=1" class="btn-black"> <i class="icon-off"></i>  Deconnexion</a>
					</div>
</div>
<div class="sql_workspace context">
<div class="LAY-list"></div>
<div class="LAY-message"></div>
<div class="LAY-controls">
	<a href="#" class="btn-red" id="sql_exec" >Execute</a>
    <a href="#" class="btn-green right" id="sql_save" >Save</a>    
</div>
<div id="sql_editor" class="LAY-center">SELECT 
* 
FROM 
category</div>
</div>
<div class="LAY-list">	 
       <div id="list">
                <!--AJAX_LIST-->
                <? if ($contexttbl){?>	
            	    <? echo $ctrl->contextTable->getTableHtml(); ?>
				<? } ?>
                <!--/AJAX_LIST-->
       </div>
</div>	

<form method="post" id="main-form" action="?tbl=<? c($contexttbl ? $contexttbl : $tbl);?>&set_form_ajax=1" class="LAY-main">
    				
                    
                   
			
					<div class="LAY-center " id="layout-form-controls"> 
                             
                        <? if ($contexttbl) { ?>
   						<!--AJAX_FORM-->
                        
                        <? if (!get('get_relation_form')){?>
                     		<div class="LAY-north">
                        
                        <div id="layout-form-top">			
			 				<div class="LAY-message"></div>
							<div class="LAY-controls">
                            <div class="button-group">
							  <button type="submit" id="btn-save" name="postback"  class="btn-green"  > <i class="icon-ok"></i> Enregistrer</button>                              
                              <a id="btn-new" href="#" data-id="0" data-tbl="<? c( $contexttbl ) ;?>"  class="btn-orange" > <i class="icon-plus"></i> Nouveau</a>

                               </div>
                               
                               <div class="button-group right">
                            	<button type="submit" id="btn-dupp"  class="btn-blue" > <i class="icon-addshape"></i> Dupliquer</button>                           
                                <button type="submit" id="btn-del"	class="btn-red"  > <i class="icon-trash"></i> Supprimer</button>                                
                                </div>
                                
                               
                                </div>
						</div>	
                    
                    </div>
                    	<? }?>
                        <div class="AJAX_CONT">
                        	
                            <input type="hidden" name="id" class="form-id" value="<? echo $ctrl->contextTable->id; ?>" />
                            <input type="hidden" name="form_submit_action_type" class="form-action" value="<? echo $ctrl->action; ?>" />
                           
                                 <div class="tabs">
                                 <div class="LAY-tabs">
                                 	<ul class="tabrow">
                                 	<li><a href=".tab-main-<? c($ctrl->contextTable->name) ; ?>"><? c(l($ctrl->contextTable->name)) ; ?></a></li>
                               <? foreach($ctrl->contextTable->relations as $v){  
										if( $v->keys['type'] != RelationType::Simple ){?>                                    
                                    	<li><a href=".tab-<? c($v->keys['name']) ; ?>"><? c(l($v->keys['name'])) ; ?></a></li>                                        
                                    <? }} ?>

                                 </ul>
                                 </div>
                                 <div class="tab-main-<? c($ctrl->contextTable->name) ; ?> context">
                                 
                                 <div class="tab">
                                 <h1 class="title"> <? c(l($ctrl->contextTable->name)); ?></h1>
                                 
                                <? echo $ctrl->contextTable->getForm() ; ?> 
                                
                                <p>&nbsp;</p>
                          		</div>
                                  
     							</div>
  							  <? if (count($ctrl->contextTable->relations)){ ?>  								
                                <? foreach($ctrl->contextTable->relations as $v){ 
								if($v->keys['type'] !=  RelationType::Simple){ ?>
                                        <div class="tab-<? c($v->keys['name']) ;?>">                                        
                                        
                                        	<div class="tab <? echo ($v->keys['type'] == RelationType::ManyToMany || $v->keys['type'] == RelationType::ManyToOne) ? 'relation-tab' :""; ?>">
											
                                            <h1 class="title"> <? c(l($v->name)); ?></h1>
                                            
											<?
											echo $v->getTableHtml(); 
											?>
										
                                        </div>
                             		</div>
                          <? }}} ?>
                                </div>
                                
                               
<div class="debug"  style="display:none;"><?  Debug::p()	?></div>
<script>
	if ($('.debug').html()){
		dp = window.open('','debug '+ Math.random(),'width=1200,height=500,fullscreen=0,toolbar=1,resizable=1,scrollbars=1,top=200,left=1500',false) ;
		dp.document.body.innerHTML = $('.debug').html();
		$(dp.document.body).dblclick(function() { dp.close();     });
	}
</script>
				
                     </div>
                     	<!--/AJAX_FORM-->
                        <? } ?>
                     </div>
                     
                     
                     <div style="height:50px;"></div>
 </form>  					 
</div>           
</body>
</html>