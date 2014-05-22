var ph = function(){
	
}
ph.prototype.init = function(){
	that = this;
	
	that.handleHash()
	
	$(document).keyup(function(e){ 
		var elid = $(document.activeElement).is("input:focus, textarea:focus"); 
		if(e.keyCode === 8 && !elid){ 
		  that.handleHash()
		}; 
	})
	setInterval(function(){
			that.handleHash()
	},1000)
	
	$("#nav-lang a").unbind('click').click(function(){
		$.get($(this).attr('href'),function(s){
			window.location.reload(false);
			window.location.href = window.location.href;	
		})
		return false ;
	})
	
	
}
ph.prototype._click = function(elm){
	 _link = $(elm).attr("href") ;		
	if(_link.indexOf("#") == 0) return false; 
	if(_link.indexOf("http://") == 0) return true; 
	this.linker(_link) ;
	return false;
}
ph.prototype.current = "" ;
ph.prototype.linker = function(_link){
		that = this
		if (that.current  == _link)/*window.location.hash && window.location.hash.replace('#/','') Onload issue*/
			return false;
		that.current = _link ;
		window.location.href='#/'+_link ;		
		that.before()
		var start = new Date();
		$.get(_link,function(_s){
			var data = _s
			var end = new Date() - start;	
			setTimeout(function(){			
					that.after(data)
			},500-end)
		})
}
ph.prototype.handleHash = function(){
		 if(window.location.hash){
			 	if (window.location.hash.indexOf('#/') == 0){
						this.linker(window.location.hash.replace('#/','')) ;
				}  
		}
}