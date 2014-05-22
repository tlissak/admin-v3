//tabs by tlissak
;(function($){
	$.fn.tabsLite = function() {		
		return this.each(function(){
			var rnd = parseInt(Math.random() *1000000000)
			
			var _parent = $(this)
			if (_parent.hasClass("tabsLite")) return ;
			
			_parent.addClass("tabsLite").data("tabs_id",rnd)			
			.find('ul:eq(0)').addClass("tabsNav").data("tabs_id",rnd)
			.find('a').data("tabs_id",rnd)
			.each(function(){	$($(this).attr('href')).data("tabs_id",rnd).addClass('i-tab')})			
			.click(function(){				
				var _a = $(this)				
				var tabs_id = _a.data("tabs_id")				
				$('.tabsLite').filter(function(){return $(this).data("tabs_id") == tabs_id })
				.find('.i-tab').filter(function(){return $(this).data("tabs_id") == tabs_id })
				.not($(_a.attr('href')))
				.removeClass('active');				
				$(_a.attr('href')).addClass('active');
				_a.parent().parent().find('li').removeClass('active')
				_a.parent().addClass('active');		
				//window.location.hash = '/' +$(this).attr('href').replace(/#/g,"");
				return false;				
			})			
			$nav = _parent.find(".tabsNav");
			//c($nav)
			if ($nav.find('li.active').size() > 0){
				$nav.find('li.active a').trigger('click')	
				/*
			}else if(window.location.hash.indexOf('/') == 1){
				curr = window.location.hash.replace('#/','')
				$nav.find('a[href="#'+curr.split('__')[0]+'"]').trigger('click') ;
				*/
			}else {
				$nav.find('li:eq(0) a').trigger('click')					
			}
		})
	}
})(jQuery) ;
