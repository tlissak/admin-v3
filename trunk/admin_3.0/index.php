<?php // v3
include('../inc/config.php') ;
require_once('inc/fb.php');
include('../inc/func.php') ;

$debug  	= new Debug() ;
$db 			= new Db() ;
$cookie 	= new Cookie('x_admin_user') ;

/*// Dreamweaver keep this comment !
include('class/AdminList.php'); 
include('class/AdminMvc.php');
include('class/AdminRelation.php');
include('class/AdminTable.php');
include('class/AdminForm.php');
include('class/AdminLoader.php');
include('class/AdminController.php');
include('class/AdminModule.php');
*/
include(P_SITE) ;

$_LNG			= array_merge(Ctrl::$_LNG_DEF,Ctrl::$_LNG) ;
$tbl 				= get('tbl') ;
$contexttbl 	= get('contexttbl');

$module 		= new AdminModule();
$ctrl				= new Ctrl() ;
$ctrl->initAuth();
$ctrl->dispacher() ;

if ($tbl && $ctrl && $ctrl->contextTable){  $contexttbl = $ctrl->contextTable->name ; }

?><!doctype html>
<html lang="fr">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin</title>
<? include('_inc.php') ; ?>
<link rel="stylesheet" type="text/css" href="css/layout.css">
<?
$module->includers();

if (Ctrl::PREF('ui_js')){	
	foreach(Ctrl::PREF('ui_js') as $ui_js) { c('<script src="' .$ui_js .'"></script>') ; }
}
if (Ctrl::PREF('ui_css')){	
	foreach(Ctrl::PREF('ui_css') as $ui_css) { c('<link rel="stylesheet" type="text/css" href="' .$ui_css .'" >') ; }
}

?>
<script>
UI.docReady();
</script>
</head>
<body dir="<? c(Ctrl::PREF('body_dir') )?>">
<div class="LAY">
<div class="LAY-left">
		<div  id="menu"> 
				   <? foreach(Ctrl::$tableInstances as $t) { if ( $t->show == 1 ) {   ?>                   
					<a class="btn <? c($t->name == $tbl ? 'active':'') ;?>" data-tbl="<? c($t->name);?>" href="?tbl=<? c($t->name);?>&menu=1" ><? c(l($t->name));?></a>
				   <? } }?>
                   
                </div>
                <hr />
                <div id="basecontrol" > 
                    <a href="?logout=1" class="btn-black"> <i class="icon-off"></i>  <? c(l('logout')) ; ?></a>
					</div>
</div>
<div class="LAY-list">	 
       <div id="list">
                <!--AJAX_LIST-->
                <? if ($contexttbl){?>	
            	    <?=  $ctrl->contextTable->getTableHtml(); ?>
				<? } ?>
                <!--/AJAX_LIST-->
       </div>
</div>	

<form method="post" id="main-form" action="?tbl=<? c($contexttbl ? $contexttbl : $tbl);?>&set_form_ajax=1" class="LAY-main">
    				<!--AJAX_FORM-->
                    
                    
                    
                    <? if (!get('get_relation_form') && $contexttbl){?>
                     		<div class="LAY-north">
                        		
			 				<div class="LAY-message"></div>
                            
                            <? if ( !  $ctrl->contextTable->protected ){ ?>
							<div class="LAY-controls">
                            <div class="button-group">
							  <button type="submit" id="btn-save" name="postback"  class="btn-green"  > <i class="icon-ok"></i> <? c(l('save')) ; ?></button>                              
                              <a class="btn-orange x-new" data-id="0" data-tbl="<? c( $contexttbl ) ;?>"   > <i class="icon-plus"></i> <? c(l('new')) ; ?></a>

                               </div>

                               <? if( $ctrl->contextTable->id  > 0) { ?>
                               <div class="button-group right">
                            	<a  class="btn-blue x-dup" data-id="<? c( $ctrl->contextTable->id ) ; ?>" data-tbl="<? c( $contexttbl ) ;?>" > <i class="icon-addshape"></i> <? c(l('duplicate')) ; ?></a>                           
                                <a  class="btn-red x-del" data-id="<? c( $ctrl->contextTable->id ) ; ?>" data-tbl="<? c( $contexttbl ) ;?>"  > <i class="icon-trash"></i> <? c(l('delete')) ; ?></a>                                
                                </div>
                                <? } ?>
                               
						</div>	
                        <? }else{ ?>
                        <div class="LAY-controls">
                            <div class="button-group">
							  <button type="submit" id="btn-save" name="postback"  class="btn-green"  > <i class="icon-ok"></i> <? c(l('save')) ; ?></button>   
                               </div>                               
						</div>	
                        
                        <? } ?>
                    
                    </div>
                    	<? }?>
                    
                    <? if ($contexttbl) { ?>
                    
                    <input type="hidden" name="id" class="form-id" value="<? c( $ctrl->contextTable->id ) ; ?>" />
                    <input type="hidden" name="form_submit_action_type" class="form-action" value="<? c( $ctrl->action ) ; ?>" />
                    
			        <div class="tabs">
           						<div class="LAY-tabs">
                                 	<ul class="tabrow">
                                 	<li><a href=".tab-main-<? c($ctrl->contextTable->name) ; ?>"><? c(l($ctrl->contextTable->name)) ; ?></a></li>
                               <? foreach($ctrl->contextTable->relations as $v){ ?>
                                    	<li><a href=".tab-<? c($v->keys['name']) ; ?>"><? c(l($v->keys['name'])) ; ?></a></li>                                        
                                    <? } ?>

                                 </ul>
                                 </div>
            
                        <div class="LAY-center " id="layout-form-controls"> 
                 
                        <div class="AJAX_CONT">
                                 
                                 <div class="tab-main-<? c($ctrl->contextTable->name) ; ?> context">
                                 
                                 <div class="tab">
                                 <h1 class="title"> <? c(l($ctrl->contextTable->name)); ?></h1>
                                 
                                <?=  $ctrl->contextTable->getForm() ; ?> 
                                
                                <p>&nbsp;</p>
                          		</div>
                                  
     							</div>
  							  <? if (count($ctrl->contextTable->relations)){ ?>  								
                                <? foreach($ctrl->contextTable->relations as $v){ ?>
                                        <div class="tab-<? c($v->keys['name']) ;?>">                                        
                                        
                                        	<div class="tab <?=  ($v->keys['type'] == RelationType::ManyToMany || $v->keys['type'] == RelationType::ManyToOne || $v->keys['type'] == RelationType::ManyToOneByKey) ? 'relation-tab' :""; ?>">
											
                                            <h1 class="title"> <? c(l($v->name)); ?></h1>
                                            
											<?
											echo $v->getTableHtml(); 
											?>
										
                                        </div>
                             		</div>
                          <? }} ?>
                              
                                
                               
<div class="debug"  style="display:none;"><?  Debug::p();	?></div>
<script type="text/javascript">
	if ($('.debug').html()  &&  '1'== '<?=  ( isset( $ctrl->Debug ) && $ctrl->Debug)   ? 1 : 0  ; ?>' ){
		dp = window.open('','debug '+ Math.random(),'width=1200,height=500,fullscreen=0,toolbar=1,resizable=1,scrollbars=1,top=200,left=1500',false) ;
		dp.document.body.innerHTML = $('.debug').html();
		$(dp.document.body).dblclick(function() { this.ownerDocument.defaultView.close();     });
	}
</script>

  </div>
				
                     </div>
                  
                      
                     </div>
                       <? } ?>
                        	<!--/AJAX_FORM-->
                     <div style="height:50px;"></div>
 </form>  					 
</div>           
</body>
</html>