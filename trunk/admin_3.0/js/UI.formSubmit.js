(function(ui){	
	ui.formSubmit = {
			serializeForm:function(f){
				var data = $(f).serializeArray() ;
				$(f).find('.tab:eq(0) input:checkbox:not(:checked)').each(function() {
                    data.push({'name':$(this).attr('name'),'value':0})
                });
				return data ;
			}
			,validation:function(f){							
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
					$('.x-dup,.x-del').toggle(callback.action != "del");
				}else{ //is poup form
					$.boxClose(callback.contexttbl+ '_' + ( callback.action == 'add' ? '0' : callback.id )) ;
				}
				
				if (callback.action == 'mod'){
					$('.row-'+ callback.tbl +"-" +callback.id,context).replaceWith($(callback.tr))
					
				}else if( callback.action == 'add' || callback.action == 'dup'){
					$(".list-"+callback.tbl+" tr.selected",context).removeClass('selected')
					$(".list-"+callback.tbl,context).append($(callback.tr))
					
				}else if(callback.action == 'del'){
					// all not in context only
					$('.row-'+ callback.tbl +"-" +callback.id).remove();
				}
				
				if (callback.action == 'mod' || callback.action == 'add' || callback.action == 'dup'){
					$('input[name="id"]',this).val( callback.id )
					$('input[name="form_submit_action_type"]',this).val( 'mod' )
				}else if(callback.action == "del"){
					this.get(0).reset();
					$('input[name="id"]',this).val(0)
					$('input[name="form_submit_action_type"]',this).val( 'add' )
				}
				
				if (callback.action == "mod")	$msg =UI.lang.POST_BACK_MODIFIED  ;
				if (callback.action == "del")		$msg =UI.lang.POST_BACK_DELETED  ;
				if (callback.action == "add")	$msg =UI.lang.POST_BACK_ADDED  ;
				if (callback.action == "dup")	$msg =UI.lang.POST_BACK_DUPLICATED  ;	
				
				
				$('.LAY-message >div ',this).remove() ;
				$('<div class="callback_message" />')
				.append('<div class="'+callback.action+'" >'+callback.title + ' '+ $msg +'</div>' ) 
				.appendTo($('.LAY-message',this)).fadeIn('slow') ;
	
				$('.relation-tab tbody input:not(:checked)').parents("tr").remove();
				
				 if(! form.is('#main-form')){ // relation form data changed
				 	ui.stateChanged.call( context , callback ) ;
				 }
				 
			},onsubmit:function(){
				$('<div class="progress" />').appendTo(this).fadeIn(100);
			},onsubmit_done:function(){
				$(".progress",this).stop().fadeOut(500,function(){ 	$(this)	.remove();	});
			}
	}
}(UI)) ;