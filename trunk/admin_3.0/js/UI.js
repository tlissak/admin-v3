var UI = {
	version:1.1
	,log:function(x){ if (window.console && window.console.log) console.log(x) ; }
	,docLoad : function(func){	UI._doc_load.push(func) ;	} ,_doc_load :[]	
	,formLoad:function(func){	UI._form_load.push(func) ;	},_form_load :[]	
	,formReady:function(form){
		for( var i = 0 ; i< UI._form_load.length; i++){
			try{
				UI._form_load[i].call(this);
			}catch(e){ 				console.trace()		;		console.log("Error hapend at UI.formRady()" , e);		console.log("The function cause error : ",UI._form_load[i]);		}
		}
	}	
	,docReady : function(){
		$(document).ready(function() {	
				for( var i = 0 ; i< UI._doc_load.length; i++){				
					try{
						UI._doc_load[i].call(this);
					}catch(e){ 						console.trace() ; 	console.log("Error hapend at UI.docRady()" , e ,i); 	console.log("The function cause error : ",UI._doc_load[i]);	 	}
				}
		});
	}
}

