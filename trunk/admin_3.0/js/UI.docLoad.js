(function(ui){
	
	ui.parseAjaxForm 	= function (rs){	return rs.substring(rs.indexOf("<!--AJAX_FORM-->") ,rs.indexOf("<!--/AJAX_FORM-->"))+"<!--/AJAX_FORM-->" ;	}
	ui.parseAjaxList 		= function (rs){	return rs.substring(rs.indexOf("<!--AJAX_LIST-->") ,rs.indexOf("<!--/AJAX_LIST-->"))+"<!--/AJAX_LIST-->" ;	}
	
	
	ui.docLoad(function(){
			
			$(document).dragEvent({	 docEnter: function(){ $("body").addClass('dragover'); }
					,docLeave:function(){	 $("body").removeClass('dragover');	},docDrop:function(){ $("body").removeClass('dragover');	}})
					
			$(document).on('click','.x-del,.x-dup',function(){
					if ($(this).is('.x-del')){ 	
						var BF = {confirm : UI.lang.CONFIRM_ITEM_SUPPRESSION, action:"del"}
					}else{
						var BF = {confirm : UI.lang.CONFIRM_ITEM_DUPPLICATION, action:"dup"}
					}
					if ( confirm(BF.confirm)	 ){
						$.ajax($('#main-form').attr('action'),{ 
						data:{form_submit_action_type:BF.action,id:$(this).data('id')}
						,context:$('#main-form')
						, type:'POST'
						, success: ui.formSubmit.callback})						
					}
					return false;
			})
			
			$(document).on('click',".callback_message",function(){	$(this).remove();	}) ;	
		
			$(document).on('click','.relation-add,.relation-mod',function(){			
					
					var context 	= $.extend({},$(this).closest(".context").data()); //http://stackoverflow.com/questions/16428385
					context.id = $(this).data('id') ; //override the tbl.id val ;
										
					$.ajax('?get_relation_form=1&'+$.param(context)  ,{
						context:this  
						,success: function(rs){
							
							$.box($(this).closest('.context').data('contexttbl') +'_' + $(this).data('id') , ui.parseAjaxForm(rs) , this ,	function(inner){	
								oContext = $.extend({},$(this).closest(".context").data() );
								oContext.id = $(this).data('id') ; //override the tbl.id val ;									
								var form = inner.find('form') ;
								form.data('context',$(this).closest('.context'))
									.ajaxSubmit(ui.formSubmit)
									.attr('action', '?set_form_ajax=1&'+ $.param(oContext));						
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
						alert(UI.lang.ERROR_DELTING_FILE )	;
					}
				}})	
			})
			
			$(document).on('change',"input[type='range']",function(){
				$(this).next().html($(this).val());
			})
	})
	
	
	//X
	ui.mainFormCallback = function (rs){
		$("#main-form").html(ui.parseAjaxForm(rs))
		.data('context',$("#list .context"))
		.attr('action' , '?tbl='+ $(this).data('tbl')+ '&set_form_ajax=1' )
		
		$(".x-new").data('tbl', $(this).data('tbl'))			
		ui.formReady.call(	$('form#main-form'))		
	}

	ui.docLoad(function(){
		
			//X0
			$('form#main-form').data('context',$("#list .context")).ajaxSubmit(ui.formSubmit) ;
			ui.formReady.call($('form#main-form')) ; 
	
	
			$(document).on("click","#list tbody tr,.x-new",function(){
					$("#list tbody tr").removeClass('selected');
					$(this).addClass('selected');		
					$.ajax('?get_form_ajax=1&'+$.param($(this).data()),{
						context:this,
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