// poup by tlissak 
;(function($){
	
	$.box = function(_opt){
		
		var $box,$mask,$popup ,$cont , $form,$close , $buttons ;
				
		var opt = (_opt !== undefined) ?  $.extend({},$.box.defaults,_opt) :$.extend({},$.box.defaults) ;
		
		opt.id = opt.id ? opt.id : parseInt(Math.random() * 1000000000000) ;
		opt._id =  opt.id_prefix + opt.id ;
		
		
		if ($('#' +opt._id ).size() == 0) {
			//console.log("creating box")
			$box = $('<div id="'+opt._id+'" />')
			
			opt._tpl = opt.tpl ;
			for ( clas in opt.class ){
				opt._tpl = opt._tpl.replace('{'+clas+'}',opt.class[clas]) ;
			}
			$box.html(opt._tpl) ;
			
			$mask = $box.find('.'+opt.class.mask) ;
			$popup = $box.find('.'+opt.class.popup);
			$cont	= $box.find('.'+opt.class.cont);
			$form 	= $box.find('form') ;
			$close	= $box.find('.'+opt.class.close  )
			$buttons = $box.find('.'+opt.class.button) ;
			
			$form.on('close',function(e){ $(this).prev().trigger('click') ; })
			
			opt.box = $box ;
			opt.cont = $cont ;
			opt.popup = $popup ;
			opt.mask = $mask ;
			opt.form = $form ;
			opt.close = $close ;
			opt.buttons = $buttons
			
			$close.click(function(){
				$mask.stop().animate({opacity:0},opt.anim_speed,function(){			$(this).hide()		})
				$popup.stop().animate({opacity:0},opt.anim_speed,function(){			$(this).hide()		})
			});
			
			if (opt.bg_close) {
					opt.mask.click(function(){
						opt.close.trigger('click');
					})
			}
			
			$box.data('box_option',opt);
			
			var _i = 100 ;
			for (btn in opt.button){
				_i-- ;
				$buttons.prepend( 				
					$('<button value="'+opt.button[btn]+'" name="'+opt._id+'_button_'+opt.button[btn]+'" tabindex="'+_i+'" type="button" >'+btn+'</button>' )
						.click(function(e){ 
							e.preventDefault() ;
							ev = new $.Event('submit');
							ev.button_value = $(this).val() ;
							$(this).closest('form').trigger(ev);
						}) 
				);
			}
			
			$form.submit(function(e){
				$f = $(this)				
				if (e.button_value){
					return opt.submit($f,e.button_value,$box,e) ;
				}
				return false ;
			}) ;
			
			$(document.body).append($box) ;			
			
			
			/*
			V2
			if (typeof(opt.html) == 'string'){ //console.log("box cont html")
				$cont.html(opt.html) ;
			}else if(typeof(opt.html) == 'object'){			
				if ($cont.html() == ''){  //console.log("box cont append")
					$cont.append(opt.html) ;
				}else{
					//old = $($cont.html() )  ;	console.log("old vs new" ,old, opt.html) 
					$cont.html('').append(opt.html) ; //console.log("box cont cleaned and append",$cont,opt.html,old)
				}
			}
			//callback should be called becouse data has been replaced !
			//$buttons.find('button:eq(0)').get(0).focus(); //dosent work 
			opt.callback($box) ; //opt.context
			*/
			
		}else{
			
			$box = $('#' + opt._id) ;
			
			/* V2 			
			var __opt = $box.data('box_option') ;
			$mask = __opt.mask ; 
			$popup = __opt.popup ; 
			*/
			
				/* V1 */
			$box.data('box_option').html = opt.html ; //get the new html 
			opt = $box.data('box_option')
			
			$cont = opt.cont;
			$box = opt.box;
			$mask = opt.mask;
			$buttons = opt.buttons;
			$popup = opt.popup;
		
		}
		
		
			
		/* V2 */	
		if (typeof(opt.html) == 'string'){ //console.log("box cont html")
			$cont.html(opt.html) ;
		}else if(typeof(opt.html) == 'object'){			
			if ($cont.html() == ''){  //console.log("box cont append")
				$cont.append(opt.html) ;
			}else{
				//old = $($cont.html() )  ;	console.log("old vs new" ,old, opt.html) 
				$cont.html('').append(opt.html) ; //console.log("box cont cleaned and append",$cont,opt.html,old)
			}
		}
		//callback should be called becouse data has been replaced !
		//$buttons.find('button:eq(0)').get(0).focus(); //dosent work 
		opt.callback($box) ; //opt.context
		
		
		
		$mask.show().stop().animate({opacity:0.6},opt.anim_speed)
		$popup.show().css({marginLeft:-($popup.width()/2)}).stop().animate({opacity:1},opt.anim_speed)
		return $box ;
	} ;
	
	$.box.close = function(id){
		$box.find('.'+opt.class.close  ) ;
	}
	
	$.box.defaults = {
		html : '<p></p>'
		,anim_speed : 250
		,id : ''
		,button : {"Ok" : 1 }
		,context : document
		,_tpl : ' ' //generated template with .tpl {class[]}
		,_id : ' ' //generated id with .prefix+.id
		,class : {
			mask : 	'box-mask'
			,popup : 'box-popup'
			,cont: 'box-cont' 
			,outer : 'box-outer' 
			,close : 'box-close'
			,button : 'box-button'
		}
		,bg_close:1
		,submit : function($f,val,e){ 
			//console.log($f,val,e);
			$f.trigger('close') ;
			return false; 
		}
		,callback : function(box){	}
		,id_prefix : 'box__' 
		,tpl :  ''+
	'<div class="{mask}" style="display:none"></div> ' +
	'<div class="{popup}" style="display:none">'+
	 		'<div class="{outer}">'+
				'<div class="{close}">&times;</div>'+
				'<form method="post" action="" >'+					
					'<div class="{cont}"></div>'+
				'<div class="{button}"></div>'+
				 '</form>'+
			'</div>'+
	'</div>' 
	}
	
})(jQuery);