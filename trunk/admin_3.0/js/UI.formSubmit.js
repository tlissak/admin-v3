(function(ui){	
	ui.formSubmit = {	
			validation:function(f){							
				validator = new UI.Validator();				
				$('.field-error',f).remove()				
				ips = $(f).find(':input');
				ok = true ;
				for(var i=0;i<ips.length;i++){
					var elm = $(ips[i])	 ;
					if (!validator.check(elm))  ok = false ;					
				}
				return ok;
			}
			,callback:function(callback){
				form 		= $(this);
				context 	= form.data('context') ;
				
				if(form.is('#main-form')){
					$('#btn-dupp,#btn-del').toggle(callback.action != "del");			
				}else{ //is poup form
					$.boxClose(callback.contexttbl+ '_' + ( callback.action == 'add' ? '0' : callback.id )) ;							
				}
				
				if (callback.action == 'mod'){
					$('.row-'+ callback.tbl +"-" +callback.id,context)
					.replaceWith($(callback.tr))
				}else if( callback.action == 'add'){	
					$(".list-"+callback.tbl+" tr.selected",context).removeClass('selected')
					$(".list-"+callback.tbl,context).append($(callback.tr))
				}else if(callback.action == 'del'){
					// all not in context only
					$('.row-'+ callback.tbl +"-" +callback.id).remove();
				}
				
				if (callback.action == 'mod' || callback.action == 'add'){
					$('input[name="id"]',this).val( callback.id )
					$('input[name="form_submit_action_type"]',this).val( 'mod' )
				}else if(callback.action == "del"){
					this.get(0).reset();
					$('input[name="id"]',this).val(0)
					$('input[name="form_submit_action_type"]',this).val( 'add' )				
				}
				
				if (callback.action == "mod")	$msg = "modifié" ;
				if (callback.action == "del")		$msg = "supprimé" ;
				if (callback.action == "add")	$msg = "ajouté" ;
				if (callback.action == "dup")	$msg = "dupliqué" ;	
	
				$('.LAY-message >div ',this).remove() ;
				$('<div class="callback_message" />')
				.append('<div class="'+callback.action+'" >'+callback.title + '" a été '+ $msg +' avec succès</div>' ) 
				.appendTo($('.LAY-message',this)).fadeIn('slow') ;
	
				$('.relation-tab tbody input:not(:checked)').parents("tr").remove();
				
			},onsubmit:function(){
				$('<div class="progress" />').appendTo(this).fadeIn(100);
			},onsubmit_done:function(){
				$(".progress",this).stop().fadeOut(500,function(){ 	$(this)	.remove();	});
			}
	}
	
}(UI)) ;