(function(ui){	
	ui.docLoad(function(){	// Paging 
		$(document).on('change',".form-filter select",function(){		ui.getFilterPagingList.call($(this).closest('.context'), 0);		})
		$(document).on('keyup',".form-filter input",function(){		ui.getFilterPagingList.call($(this).closest('.context'), 0);			})
		$(document).on('click' ,".paging a",function(){						ui.getFilterPagingList.call($(this).closest('.context'), $(this).data('page'));		return false;	})
	}) ;	
	ui.getFilterPagingList = function (page){		
		if ( $('.list-state',this).data('viewtype')  == 'SELECT-EDIT'  ){
			if (!confirm(UI.lang.CONFIRM_DATA_LOST)) return ;		// the data not saved in db (in the relation table) yet
		}
		$.ajax('?get_list_ajax=1&'+ $.param(this.data()) +  '&page='+page,{
							data:$(":input",this).serializeArray()
							,context:this
							,type:'POST'
							,success:function(o){
								$('.tbl tbody',this).html(o.list);
								$('.paging',this).replaceWith(o.paging) ;
								
								ui.stateChanged.call(this,false);
								
							}
		})
	}
	
}(UI)) ;

;(function(ui){
	
	ui.stateChanged = function(callback){		
		var $liststate 		= $('.list-state',this) ;
		var list 				= this.find('table.tbl input.relation-cb').get() ;
		var state_values =  $liststate.find(':input').get() ;		
		if (!callback){ // the list just loaded so apply checked to list by state (applay list checked by state)			
			if ($liststate.data('viewtype') == 'SELECT-ONE-EDIT' ){
				for (var j=0;j< list.length ; j++ ) {						
					$(list[j]).prop('checked', state_values[0].value  == list[j].value);	
				}				
			}else if ($liststate.data('viewtype') == 'SELECT-EDIT' ){	
				for (var j=0;j< list.length ; j++ ) {
						var found = false ;
						for (var i=0;i<state_values.length ; i++){
							if (state_values[i].value == list[j].value )	{
									found = true; 
							}
						}
						$(list[j]).prop('checked', found);
				}
			}
		}else{  // called by poup callback add to state by the returnd object
			if (callback.action != "del"){
				input = $('#_fld_'+callback.tbl+'_'+ callback.id).get(0);
				ui.applyState(this,input);
			}else{// remove the value callback.id from state ;
				var state_values =  $liststate.find(':input').get() ;
				for (var i=0;i<state_values.length ; i++){
						if (state_values[i].value == callback.id )	{ 	$(state_values[i]).remove() ; }
				}				
			}
		}		
	}
	
	ui.applyState = function($context,INPUT){
		
		var $liststate = $context.find('.list-state') ;
		
		if ($liststate.data('viewtype') == 'SELECT-ONE-EDIT' ){
			
				$liststate.find(':input').val(INPUT.value);
				$context.find('table.tbl input:radio').not(INPUT).prop('checked', false);		
				
			}else if ($liststate.data('viewtype') == 'SELECT-EDIT' ){
				
				var current_action = ($(INPUT).is(':checked')) ? 'add_to_state' : 'del_from_state';
				var state_values =  $liststate.find(':input').get() ;
				
				if (current_action == 'add_to_state'){ //check if the value allready exists then do nothing else add new value to state	
					value_found = false ;
					for (var i=0;i<state_values.length ; i++){
						if (state_values[i].value == INPUT.value )	{
								value_found = true ; break ;								
						}
					}
					if (! value_found) {//add it	
						$liststate.append('<input type="hidden" name="'+ $liststate.data('fieldname') +'" value="'+INPUT.value+'" />');
					}					
				}else if(current_action == 'del_from_state'){ //check if the elem is in state  then remove it 
					for (var i=0;i<state_values.length ; i++){
						if (state_values[i].value == INPUT.value )	{ 	$(state_values[i]).remove() ; }
					}
					
				}
				
			}
	}
	
	ui.docLoad(function(){
		
		//whired Issue (onload some inputs are checked randomaly with firefox ?tbl=x&id=x) this will fix it !!			
		$('.relation-cb:checked').not('[checked="checked"]').prop('checked', false);
		$('.relation-cb[checked="checked"]').prop('checked', true);
		
		$(document).on('click','.context input.relation-cb',function(){ 		
			$context 	= $(this).closest('.context');		
			ui.applyState($context, this ) ;			
		})
	})
	
	
		
}(UI)) ;