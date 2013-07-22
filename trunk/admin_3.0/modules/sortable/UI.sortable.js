(function(ui){
	
	ui.handleSortable = function(e){
				if (e.type =='keypress' && e.keyCode == 13 || e.type == 'focusout') {
						e.preventDefault();
						data =  {
							val 	: $(this).val()
							,fld 	: $(this).data('name')
							,tbl 	: $(this).closest(".context").data('contexttbl')
							,id		: $(this).closest('tr').data('id')
						}
						
						$.ajax('?set_position=1',{ data:data, context:this, type:'GET',success: function(json){
							var elm = $(this).css('background-color',"#D8FFD7")
							setTimeout(function(){
								elm.css('background-color',"");
							},3000)
						}})
				}
	}
	
	ui.docLoad(function(){
		$(document)
			.on("click",".sortable_field",function(e){e.stopPropagation();})
			.on('keypress',".sortable_field",ui.handleSortable)
			.on("blur",".sortable_field",ui.handleSortable)
	})
	
})(UI) ;