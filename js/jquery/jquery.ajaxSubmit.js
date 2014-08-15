;$(function() {
	$.fn.ajaxSubmit = function(ret_func)	{
		return this.submit(function(e){
			e.preventDefault();
			var f = $(this) ;	$.ajax({
				url:f.attr('action')?f.attr('action'):'#'
				,data:f.serialize()	
				,type:f.attr('method')
				,success:function(s){	ret_func ? ret_func(s,f) : alert(s) ;	}}) ;
			return false ;	
		})
	}
}) ;