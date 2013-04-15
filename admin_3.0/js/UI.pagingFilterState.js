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
		
		if ( $('.list-state',this).data('viewtype')  == 'SELECT-EDIT'  ){
			if (!confirm(UI.lang.CONFIRM_DATA_LOST)) return ;		
		}
		$.ajax('?get_list_ajax=1&'+ $.param($(this).data()) +  '&page='+page,{
							data:$(":input",this).serializeArray()
							,context:this
							,type:'POST'
							,success:function(o){
								$('.tbl tbody',this).html(o.list);
								$('.paging',this).replaceWith(o.paging) ;								
								ui.applyState(this);
							}
		})
	}
	
}(UI)) ;


;(function(ui){
	
	// listState 
	ui.applyState = function(context){
			
			$liststate = $('.list-state',context) ;
																		
			if ($liststate.data('viewtype') == 'SELECT-ONE-EDIT' || $liststate.data('viewtype')  == 'SELECT-EDIT'){									
					state_ipts=$liststate.find(':input').get();																									
					ipts = $('.tbl input',context)
					ipts.find(':checked').removeAttr('checked')									
					ipts_g = ipts.get();
									
					for (var i= 0; i< state_ipts.length ;i++ ){
								for( var j=0;j<ipts_g.length;j++)	{
											if ($(ipts_g[j]).val() == $(state_ipts[i]).val() )	{
												alert('This not worked whey ?' );
												$(ipts_g[j]).attr('checked',true) ;
											}
								}
					}
			}
	}
	
	ui.docLoad(function(){
		
		//whired Issue (onload some inputs are checked randomaly with firefox ?tbl=x&id=x) this will fix it !!		
		$('.relation-cb:checked').not('[checked="checked"]').removeAttr("checked");
		
		$(document).on('click','.relation-cb',function(){
			$context = $(this).closest('.context') ;
			$liststate = $context.find('.list-state') ;
			if ($liststate.data('viewtype') == 'SELECT-ONE-EDIT'){
				$liststate.find(':input').val($(this).val());
			}else if($liststate.data('viewtype')  == 'SELECT-EDIT'){
				$that = $(this) ;
				action = ($(this).is(':checked')) ? 'add' : 'del';
				if (action == 'add'){
					$liststate.find(':input').each(function() {
                        if ( $(this).val() == $that.val() ){ // val found nothing to add
							return ;
						}
                    });
					 $that.clone().attr('name',$that.attr('name').replace('__','')).attr('type','hidden').appendTo($liststate)
				}else if (action == 'del'){
					$liststate.find(':input').each(function() {
                        if ( $(this).val() == $that.val() ){ // val found nothing to add
							$(this).remove() ;
						}
                    });
				}				
			} //SIMPLE			
		})
	})
		
}(UI)) ;