(function(ui){
	
	ui.Validator = function(){	
		this.check = function(elm){
			var type 		= elm.data('type');
			var require 	= elm.data('require');
			var limit 		= elm.data('limit');		
			if(require){
				if (! $.trim(elm.val())){
					this.show(elm,'data required') ;
					return false;
				}
			}		
			if(limit){
				if ($.trim(elm.val()).length > limit){
					this.show(elm,'data limit reached') ;
					return false;
				}
			}		
			if (type && $.trim(elm.val())){			
				ipts_val = new input_validation();			
				handler =  (ipts_val['_'+type]) ? ipts_val['_'+type] : false;			
				if (handler){
					if (! handler.pattern.test(elm.val())){
						
						elm.dblclick(function(){
							if ($(this).data('fix')){
								fixer = $(this).data('fix')
								$(this).val(fixer($(this).val()))
							}
						}) ;
						if (handler.fix)
							elm.data('fix',handler.fix) ;
						
						this.show(elm,handler.msg) ;
						
						return false;
					}else{
					//	c('passed',handler.pattern , type , elm.val())	 ;
					}	
				}else{
					alert(type + ' handler not found')	
				}
			}
			return true ;
		}	
		this.show = function(elm,msg,fix){
			elm.after("<span class='field-error'>"+ msg +"</span>")	 ;
			elm.addClass('field-error-ipt')
		}	
	}
}(UI)) ;