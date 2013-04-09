(function(ui){
	
	ui.parseAjaxForm 	= function (rs){	return rs.substring(rs.indexOf("<!--AJAX_FORM-->") ,rs.indexOf("<!--/AJAX_FORM-->"))+"<!--/AJAX_FORM-->" ;	}
	ui.parseAjaxList 		= function (rs){	return rs.substring(rs.indexOf("<!--AJAX_LIST-->") ,rs.indexOf("<!--/AJAX_LIST-->"))+"<!--/AJAX_LIST-->" ;	}
	
	
	ui.docLoad(function(){
			
			$(document).dragEvent({	 docEnter: function(){ $("body").addClass('dragover'); }
					,docLeave:function(){	 $("body").removeClass('dragover');	},docDrop:function(){ $("body").removeClass('dragover');	}})
					
			$(document).on('click','#btn-del',function(){	if ( confirm('Etes-vous sur de vouloir supprimer l\'élément ?')	 ){
						$('input[name="form_submit_action_type"]',$('form#main-form')).val( 'del' ) ;
			}})	
			$(document).on('click','#btn-dupp',function(){	$('input[name="form_submit_action_type"]',$('form#main-form')).val( 'add' ) 	})
			
			$(document).on('click',".callback_message",function(){	$(this).remove();	}) ;	
		
			$(document).on('click','.relation-add,.relation-mod',function(){			
					context 	= $(this).closest(".context").data()
					context.id = $(this).data('id') ; //override the tbl.id val ;
					
					$.ajax('?get_relation_form=1&'+$.param(context)  ,{
						context:this  
						,success: function(rs){
							$.box($(this).closest('.context').data('contexttbl') +'_' + $(this).data('id') , ui.parseAjaxForm(rs) , this ,	function(inner){	
								context = $(this).closest(".context").data()
								context.id = $(this).data('id') ; //override the tbl.id val ;						
								var form = inner.find('form') ;
								form
								.data('context',$(this).closest('.context'))
								.ajaxSubmit(ui.formSubmit)
								.attr('action', '?set_form_ajax=1&'+ $.param(context));						
								ui.formReady.call(form)
							}) ;
						}
					})
			})	
			
			$(document).on('click','.link-delete-file',function(){
				$.ajax("?delete_file=1&file="+$(this).data("path"),{context:this,success: function(res){
					if (res == 'OK'){		
						$(this).closest(".files").empty();
					}else{
						alert('ErrorDeletingFile')	;
					}
				}})	
			})
			
			$(document).on('change',"input[type='range']",function(){
				$(this).next().html($(this).val());
			})
	})
	
	
	//X
	ui.mainFormCallback = function (rs){
		$("#layout-form-controls").html(ui.parseAjaxForm(rs));	
		$('form#main-form').data('context',$("#list .context")).attr('action' , '?tbl='+ $(this).data('tbl')+ '&set_form_ajax=1' ).data('context',$("#list .context")) ;
		$("#btn-new").data('tbl', $(this).data('tbl'))			
		ui.formReady.call(	$('form#main-form'))		
	}

	ui.docLoad(function(){
		
			//X0
			$('form#main-form').data('context',$("#list .context")).ajaxSubmit(ui.formSubmit) ;
			ui.formReady.call($('form#main-form')) ; 
	
	
			$(document).on("click","#list tbody tr,#btn-new",function(){
					$("#list tbody tr").removeClass('selected');
					$(this).addClass('selected');		
					$.ajax('?get_form_ajax=1&'+$.param($(this).data()),{
						context:this,
						//X1
						success:ui.mainFormCallback
					}) ;
					return false;
			})
				
			$("#menu a").click(function(){		
					$("#menu a").removeClass("active")
					$(this).addClass('active');
					$.ajax( $(this).attr('href') ,{
							context:this
							,success: function(rs){
								//X2
								$('#list').html(ui.parseAjaxList(rs)) 
								ui.mainFormCallback.call(this,rs) ;
								
							}
						})
					return false;
			})
		
	})
		
})(UI) ;