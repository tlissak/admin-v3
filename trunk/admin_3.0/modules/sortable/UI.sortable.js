(function(ui){
	
	ui.docLoad(function(){
	
		$(document).on("click",".sortable_field",function(e){	e.stopPropagation();})
			.on('keypress',".sortable_field",function(e){
				if (e.type =='keypress' && e.keyCode == 13) {
						data =  {
							val 	: this.value
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
			})
	})
	
})(UI) ;