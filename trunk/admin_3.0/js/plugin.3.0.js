if (window.console){	c = console.log ;	}

// ajax submit by me
;(function($){
	$.fn.ajaxSubmit = function(o){
		this.append('<input class="ajaxSubmit_formCaller" type="hidden" value="" />');		
		this.find('input:submit').click(function(){	$(this.form).find('.ajaxSubmit_formCaller').attr('name' ,$(this).attr('name') ).val( $(this).val() )	})
		
		return this.submit(function(response){				
				if (o.validation && o.validation instanceof Function ) {
					if (! o.validation(this)){
							return false;
					}
				}			
				var f 		= $(this)
				var data = f.serialize()
				var uri 	= f.attr('action') ? f.attr('action') : '' ;
				var type = f.attr('method') ? f.attr('method').toUpperCase() : 'GET' ;
				var uri = uri+(uri.indexOf('?')>-1 ? '&' : '?') +  'ajaxSubmit=1' ;
				if (o.onsubmit) o.onsubmit.call(this);
				$.ajax(uri,{type:type,data:data,context:f,success:function(response){ 
					if (o.onsubmit_done) o.onsubmit_done.call(this);
					o.callback.call(this,response) 
				},error: function(rs){ if (o.onsubmit_done) o.onsubmit_done.call(this);}})
				return false; 
		})
	}
	$.fn.ajaxSubmit.version = '2.0' ;
})(jQuery) ;


// poup by me with ids
;(function($){
	var box_mask_selector 	= '.p-popup-mask' ;
	var box_popup_selector = '.p-popup' ;
	var box_cont_selector 	= '.p-popup-cont' ;
	var box_outer_selector 	= '.p-popup-outer' ;		
	var box_html = ''+
	'<div class="'+box_mask_selector.replace(/\./g,'')+'" style="display:none"></div> ' +
	'<div class="'+box_popup_selector.replace(/\./g,'')+'" style="display:none">'+
	 		'<div class="'+box_outer_selector.replace(/\./g,'')+'">'+
				'<form method="post" action="" >'+
							 '<p class="submit popup-submit"><button type="submit" name="postback"  class="btn-green" ><i class="icon-ok"></i> Enregistrer</button></p>'+  
							'<input type="hidden" name="postback" value="1" />'+
							'<div class="'+box_cont_selector.replace(/\./g,'')+'"></div>'+							
				 '</form>'+
		'</div>'+
	'</div>' ;	
	var box_idprefix = 'box__' ;	
	$.boxClose = function(id){
		$('#'+box_idprefix+id).find(box_mask_selector).animate({opacity:0.0},500,function(){			$(this).hide()		})
		$('#'+box_idprefix+id).find(box_popup_selector).animate({opacity:0},500,function(){			$(this).hide()		})
	}
	$.boxOpen = function(id){	
		$('#'+box_idprefix+id).find(box_mask_selector).css('display','block').animate({opacity:0.5},500)
		$('#'+box_idprefix+id).find(box_popup_selector).css('display','block').animate({opacity:1},500)
	}	
	$.boxCreate = function(id , dt){
		$box = $('<div id="'+box_idprefix + id+'" />')
		$box.html(box_html) ;
		$box.find(box_mask_selector).click(function(){  	$.boxClose(id) ;		});
		$box.find(box_cont_selector).html(dt)		
		$(document.body).append($box) ;
	}	
	$.boxDispose = function(id){
		$('<div id="'+box_idprefix + id+'" />').remove()
	}
	$.box = function(id,dt,context,callback){
			if ($('#' + box_idprefix + id).size() == 0) {
				$.boxCreate(id,dt) ;			
				callback.call(context,$('#' +  box_idprefix + id).find(box_outer_selector)) ;
			}else{
				$('#' + box_idprefix + id).find('form').unbind('submit') ;
				$('#' + box_idprefix + id).find(box_cont_selector).html(dt) ;
				callback.call(context,$('#' +  box_idprefix + id).find(box_outer_selector)) ;
			}
			$.boxOpen(id) ;
	} ;
})(jQuery);



//tabs by tlissak
;(function($){
	$.fn.tabsLite = function() {		
		return this.each(function(){
			if ($(this).hasClass("tabsLite")) return ;
			$(this)
			.addClass('tabsLite')
			.find('ul:eq(0)')
			.addClass('tabs-nav')
			.find('a')
			.each(function(){	$($(this).attr('href')).addClass('i-tab')})
			.click(function(){
				$( '.i-tab', $(this).closest('.tabsLite') ).not($(this).attr('href')).removeClass('active');
				$($(this).attr('href'),$(this).closest('.tabsLite')).addClass('active');
				$(this).parent().parent().find('li').removeClass('active')
				$(this).parent().addClass('active');		
				return false;
			})
			$nav = $(this).find(".tabs-nav") ;
			if ($nav.find('li.active').size() > 0){
				$nav.find('li.active a').trigger('click')	
			}else{
				$nav.find('li:eq(0) a').trigger('click')					
			}
		})
	}
})(jQuery) ;

