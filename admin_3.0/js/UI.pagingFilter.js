(function(ui){	
	
	// Paging 
	ui.docLoad(function(){
		$(document).on('change',".form-filter select",function(){
				ui.getFilterPagingList.call($(this).closest('.context'), 0);
			})
			$(document).on('keyup',".form-filter input",function(){
				ui.getFilterPagingList.call($(this).closest('.context'), 0);
			})
			
			$(document).on('click' ,".paging a",function(){
				ui.getFilterPagingList.call($(this).closest('.context'), $(this).data('page'));
				return false;	
			})
	}) ;	
	ui.getFilterPagingList = function (page){
			$.ajax('?get_list_ajax=1&'+ $.param($(this).data()) +  '&page='+page,{
							data:$(":input",this).serializeArray()
							,context:this
							,type:'POST'
							,success:function(o){
								$('.tbl tbody',this).html(o.list);
								$('.paging',this).replaceWith(o.paging) ;
							}
			})
	}
	
}(UI)) ;