/*
* jQuery RTE plugin 0.5.1 - create a rich text form for Mozilla, Opera, Safari and Internet Explorer
*
* Copyright (c) 2009 Batiste Bieler
* Distributed under the GPL Licenses.
* Distributed under the MIT License.
*/

// define the rte light plugin
(function($) {

if(typeof $.fn.rte === "undefined") {
	
	function bindFirst(elm,name, fn) { 
				elm.bind(name, fn);
				try{	
					var handlers = $._data(elm.get(0), "events")[name.split('.')[0]] ;	
					var handler = handlers.pop();
					handlers.splice(0, 0, handler);
				}catch(e){
					console.log(e,"$.fn.bindFirst FAILD" , elm);	
				}
	};
	function parse_css(st){
		var keys = st.replace(/\{[\s|\S]*?\}/g,',').replace(/\n|\r/g,' ').replace(/@[\s|\S]*?[\;|,]/g,'').replace(/\/\*.*?\*\/?/g,'').replace(/ /g,'').split(',')
		var out = '' ;
		for (var i=0;i<keys.length;i++){
			var key = keys[i]
			if (key.indexOf('.') == -1 ){ 			continue ;	}
			if (key.indexOf(':') > -1 ){				continue ;	}
			if (key.indexOf('>') > -1 ){				continue ;	}
			if (key.indexOf('#') > -1){ continue ;
					key = '.' + key.replace(/#(.*\.)?/,'').replace(/#(.*)$/,'')
			}
			out += ',' + key.substring(key.indexOf('.')).replace(/\./g, '')	
		}
		return out ;
	}
	
    var defaults = {
        content_css_url: "rte.css",
        dot_net_button_class: null,
        max_height: 350,
		select_font:false,
		fullsize:function(){}
    };

    $.fn.rte = function(options) {

    $.fn.rte.html = function(iframe) {
        return iframe.contentWindow.document.getElementsByTagName("body")[0].innerHTML;
    };

    // build main options before element iteration
    var opts = $.extend(defaults, options);

    // iterate and construct the RTEs
    return this.each( function() {
        var textarea = $(this);
        var iframe;
        var element_id = textarea.attr("id");

        // enable design mode
        function enableDesignMode() {

            var content = textarea.val();

            // Mozilla needs this to display caret
            if($.trim(content)=='') {
                content = '<br />';
            }

            // already created? show/hide
            if(iframe) {
                textarea.hide();
                $(iframe).contents().find("body").html(content);
                $(iframe).show();
                $("#toolbar-" + element_id).remove();
                textarea.before(toolbar());
                return true;
            }

            // for compatibility reasons, need to be created this way
            iframe = document.createElement("iframe");
            iframe.frameBorder=0;
            iframe.frameMargin=0;
            iframe.framePadding=0;
            iframe.height=textarea.height();
			iframe.width='100%';
            if(textarea.attr('class'))
                iframe.className = textarea.attr('class');
            if(textarea.attr('id'))
                iframe.id = element_id;
            if(textarea.attr('name'))
                iframe.title = textarea.attr('name');

            textarea.wrap('<div class="rte-zone" />').after(iframe);

            var css = "";
            if(opts.content_css_url) {
                css = "<link type='text/css' rel='stylesheet' href='" + opts.content_css_url + "' />";
            }

            var doc = "<html><head>"+css+"</head><body class='frameBody' style='padding:10px;' spellcheck='false'>"+content+"</body></html>";
			var retries  = 0 ;
            tryEnableDesignMode(doc, function() {
                $("#toolbar-" + element_id).remove();
                textarea.before(toolbar());
                textarea.hide();
            });

        }
		var retries = 0;
        function tryEnableDesignMode(doc, callback) {
            if(!iframe) { return false; }
			retries ++ ; if (retries > 100) {return false;}
            try {
                iframe.contentWindow.document.open();
                iframe.contentWindow.document.write(doc);
                iframe.contentWindow.document.close();
            } catch(error) {
               //console.log(error);
            }
            if (document.contentEditable) {
                iframe.contentWindow.document.designMode = "On";
                callback();
                return true;
            }else if (document.designMode != null) {
                try {
					iframe.contentWindow.document.designMode = "on";
                    callback.call();
                    return true;
                } catch (error) {
                   //console.log(error);
                }
            }
            setTimeout(function(){tryEnableDesignMode(doc, callback)}, 500);
            return false;
        }

        function disableDesignMode(_submit) {
			
            var content = $(iframe).contents().find("body").html();
			
			$(iframe).contents().find("head *:not('link')").remove();
			
            if($(iframe).is(":visible")) {
                textarea.val(content);
            }

            if(_submit !== true) {
                textarea.show();
                $(iframe).hide();
            }
        }

        // create toolbar and bind events to it's elements
        function toolbar() {
            var _tb = "<div class='rte-toolbar' id='toolbar-"+ element_id +"'><p>\
					<span class='button-group'>\
					<a href='#' class='btn bold'><i class='icon-bold'></i></a>\
                    <a href='#' class='btn italic'><i class='icon-italic'></i></a>\
					</span>\
					<span class='button-group'>\
                    <a href='#' class='btn unorderedlist'><i class='icon-dotlist'></i></a>\
					<a href='#' class='btn orderedlist'><i class='icon-numberlist'></i></a>\
					</span>\
					<span class='button-group'>\
                    <a href='#' class='btn link'><i class='icon-link'></i></a>\
					<a href='#' class='btn unlink'><i class='icon-silverstripe'></i></a>\
                    <a href='#' class='btn image'><i class='icon-images-gallery'></i></a>\
					<a href='#' class='btn code'><i class='icon-chevrons'></i></a>\
                  	</span>\
					<span class='button-group'>\
					<a href='#' class='btn justifyLeft'><i class='icon-align-left'></i></a>\
					<a href='#' class='btn justifyCenter'><i class='icon-align-center'></i></a>\
					<a href='#' class='btn justifyRight'><i class='icon-align-right'></i></a>\
					<a href='#' class='btn justifyFull'><i class='icon-align-justify'></i></a>\
					</span>\
					<span class='button-group'>\
					<a href='#' class='btn indent'><i class='icon-indentright'></i></a>\
					<a href='#' class='btn outdent'><i class='icon-indentleft'></i></a>\
					</span>\
					<span class='button-group'>\
					<a href='#' class='btn clearformatting'><i class='icon-clearformatting'></i></a>\
					<a href='#' class='btn wordclear'><i class='icon-microsoftoffice'></i></a>\
					</span>\
					<span class='button-group'>\
					<a href='#' class='btn fullscreen'><i class='icon-fullscreen'></i></a>\
					<a href='#' class='btn disable'><i class='icon-cplusplus'></i></a>\
					</span>\
					<span class='select-list'><i></i>\
                    <select class='formatblock'>\
                        <option value=''>- Block -</option>\
                        <option value='p'>Paragraph</option>\
                        <option value='h1'>H1</option>\
						<option value='h2'>H2</option>\
						<option value='h3'>H3</option>\
						<option value='h4'>H4</option>\
                    </select>\
					</span>\
					<span class='select-list'><i></i>\
					<select class='cssstyle'></select>\
					</span>\
					</p></div>";
		
					
			var tb = $(_tb) ;
			
			if (opts.select_font) {
				$('p',tb).append("<span class='select-list'><i></i>\
					 <select class='fontsize'>\
                        <option value=''>- Police -</option>\
                        <option value='1'>1 (8pt)</option>\
						<option value='2'>2 (10pt)</option>\
						<option value='3'>3 (12pt)</options>\
						<option value='4'>4 (14pt)</option>\
						<option value='5'>5 (16pt)</options>\
						<option value='6'>6 (18pt)</option>\
						<option value='7'>7 (20pt)</options>\
                    </select>\
					</span>") ;
			}
			if(opts.content_css_url) {	
				$.ajax({
						context:tb,
						type:'GET',
						url:opts.content_css_url,
						async: false,
						success: function(data) {
							var list = parse_css(data).split(',');
							var _select = "";
							for(var name in list)
								_select += '<option value="' + list[name] + '">' + list[name] + '</option>';				
							$('.cssstyle',this).html( '<option value="">- css -</option>' + _select );
						}});
			}
			// $('.image', tb).click(function(e){ 
			$(document).on('click','.image',function(e){   //change style that can be override
				var p=prompt("image URL:");    
				if(p)
					formatText('InsertImage', p);
				return false; 
			});
			
			$('.cssstyle', tb).change(function(){
                var index = this.selectedIndex;
                if( index!=0 ) {
                    var selected = this.options[index].value;
					var html = getSelectionText();
					html = '<span class="' + selected + '">' + html + '</span>';
					setSelectionReplaceWith(html);
                }
            });
			
	
			$('.code', tb).click(function(e){
				manipulateSelection(function(str){return  '<pre>' + ( str.replace(/\</g,'&lt;').replace(/\>/g,'&gt;') )+ '</pre>' ;} );
				return false;				
            });
			
			function manipulateSelection(manip){
				var txt
				if(iframe.contentWindow.getSelection)﻿  
		﻿  ﻿  		txt =  iframe.contentWindow.getSelection().toString();
			﻿	else txt = iframe.contentWindow.document.selection.createRange().text;				
				var html = manip(txt);
				var rng﻿  = null;
				iframe.focus();			﻿  
				if(iframe.contentWindow.getSelection) {
				﻿  ﻿  rng = iframe.contentWindow.getSelection().getRangeAt(0);				
				﻿﻿	var s = rng.startContainer;				
				﻿	if(s.nodeType === Node.TEXT_NODE){
						$(s).wrap('<p />');
				﻿  ﻿  ﻿  ﻿  rng.setStartBefore(s.parentNode);
					}				
				} else {
				﻿  ﻿  rng = iframe.contentWindow.document.selection.createRange();
				}					
				if(iframe.contentWindow.getSelection) {					
				﻿  ﻿  rng.deleteContents();
					formatText('delete');
				﻿  ﻿  rng.insertNode(rng.createContextualFragment(html));
				} else {
				﻿  ﻿  formatText('delete');
				﻿  ﻿  rng.pasteHTML(html);
				}				
			}			
			
			$(".fullscreen",tb).click(function(){
				$(iframe).closest(".rte-zone").toggleClass('fullsize')
				$(iframe).closest("form").toggleClass('fullsize')
				opts.fullsize.call(iframe);
				return false;	
			})
			$(".wordclear",tb).click(function(){
				$('body', iframeDoc).html(cleanupWord($(iframe).contents().find("body").html()));
				return false;	
			})			
            $('.formatblock', tb).change(function(){ if( this.selectedIndex!=0 ) {  formatText("formatblock", '<'+this.options[this.selectedIndex].value+'>');  }  });			
			$('.fontsize', tb).change(function(){ if( this.selectedIndex!=0 ) { formatText("fontsize", this.options[this.selectedIndex].value); } });			
            $('.bold', tb).click(function(){ formatText('bold');return false; });
            $('.italic', tb).click(function(){ formatText('italic');return false; });
            $('.orderedlist', tb).click(function(){ formatText('insertorderedlist');return false; });			
			$('.unorderedlist', tb).click(function(){ formatText('insertunorderedlist');return false; });
			$('.outdent', tb).click(function(){ formatText('outdent');return false; });
			$('.indent', tb).click(function(){ formatText('indent');return false; });
			$('.unlink', tb).click(function(){ formatText('unlink');return false; });
            $('.link', tb).click(function(){   var p=prompt("URL:");    if(p)   formatText('CreateLink', p);   return false; });
			$('.justifyLeft',tb).click(function(){ formatText('justifyLeft'); return false; })
			$('.justifyCenter',tb).click(function(){ formatText('justifyCenter'); return false; })
			$('.justifyRight',tb).click(function(){ formatText('justifyRight'); return false; })
			$('.justifyFull',tb).click(function(){ formatText('justifyFull'); return false; })			
			$('.clearformatting',tb).click(function(){ formatText('removeFormat');formatText('unlink'); return false; })            
            $('.disable', tb).click(function() {
                disableDesignMode();
                var edm = $('<a class="rte-edm btn" href="#"><i class="icon-terminal"></i></a>');
                tb.empty().append(edm);
                edm.click(function(e){
                    e.preventDefault();
                    enableDesignMode();
                    $(this).remove();
                });
                return false;
            });
		
			bindFirst($(iframe).closest('form') , 'submit', function(){  
				try{
					disableDesignMode(true);        
				}catch(e){	}
			})			
            
            var iframeDoc = $(iframe.contentWindow.document);
			
            var formatblock = $('.formatblock', tb)[0];
            
			iframeDoc.keypress(function(e){
				if (e.which == 98 && e.ctrlKey){e.preventDefault(); formatText('bold' ) ;	 return false ;	}
				if (e.which == 105 && e.ctrlKey){e.preventDefault(); formatText('italic' ) ; return false ;	}
			}).bind('paste',function(){ })
			
			iframeDoc.mouseup(function(){
                setSelectedType(getSelectionElement(), formatblock);
                return true;
            });
			
            return tb;
        };
		
		function cleanupWord(_s){
			return _s.replace(/<o:p>\s*<\/o:p>/g, '')
			.replace(/<o:p>[\s\S]*?<\/o:p>/g, '&nbsp;')
			.replace( /\s*mso-[^:]+:[^;"]+;?/gi, '' )
			.replace( /\s*MARGIN: 0cm 0cm 0pt\s*;/gi, '' )
			.replace( /\s*MARGIN: 0cm 0cm 0pt\s*"/gi, "\"" )
			.replace( /\s*TEXT-INDENT: 0cm\s*;/gi, '' ) 
			.replace( /\s*TEXT-INDENT: 0cm\s*"/gi, "\"" )
			.replace( /\s*TEXT-ALIGN: [^\s;]+;?"/gi, "\"" ) 
			.replace( /\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/gi, "\"" ) 
			.replace( /\s*FONT-VARIANT: [^\s;]+;?"/gi, "\"" ) 
			.replace( /\s*tab-stops:[^;"]*;?/gi, '' ) 
			.replace( /\s*tab-stops:[^"]*/gi, '' ) 
			.replace( /\s*face="[^"]*"/gi, '' )
			.replace( /\s*face=[^ >]*/gi, '' )
			.replace( /\s*FONT-FAMILY:[^;"]*;?/gi, '' ) 
			.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") 
			.replace( /<(\w[^>]*) style="([^\"]*)"([^>]*)/gi, "<$1$3" ) 
			.replace( /<STYLE[^>]*>[\s\S]*?<\/STYLE[^>]*>/gi, '' )
			.replace( /<(?:META|LINK)[^>]*>\s*/gi, '' )
			.replace( /\s*style="\s*"/gi, '' ) 
			.replace( /<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/gi, '&nbsp;' ) 
			.replace( /<SPAN\s*[^>]*><\/SPAN>/gi, '' ) 
			.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") 
			.replace( /<SPAN\s*>([\s\S]*?)<\/SPAN>/gi, '$1' ) 
			.replace( /<FONT\s*>([\s\S]*?)<\/FONT>/gi, '$1' ) 
			.replace(/<\\?\?xml[^>]*>/gi, '' ) 
			.replace( /<w:[^>]*>[\s\S]*?<\/w:[^>]*>/gi, '' ) 
			.replace(/<\/?\w+:[^>]*>/gi, '' ) 
			.replace(/<\!--[\s\S]*?-->/g, '' ) 
			.replace( /<(U|I|STRIKE)>&nbsp;<\/\1>/g, '&nbsp;' )
			.replace( /<H\d>\s*<\/H\d>/gi, '' )
			.replace( /<(\w+)[^>]*\sstyle="[^"]*DISPLAY\s?:\s?none[\s\S]*?<\/\1>/ig, '' )
			.replace( /<(\w[^>]*) language=([^ |>]*)([^>]*)/gi, "<$1$3") 
			.replace( /<(\w[^>]*) onmouseover="([^\"]*)"([^>]*)/gi, "<$1$3") 
			.replace( /<(\w[^>]*) onmouseout="([^\"]*)"([^>]*)/gi, "<$1$3") 
			.replace( /<H(\d)([^>]*)>/gi, '<h$1>' ) 
			.replace( /<(H\d)><FONT[^>]*>([\s\S]*?)<\/FONT><\/\1>/gi, '<$1>$2<\/$1>' )
			.replace( /<(H\d)><EM>([\s\S]*?)<\/EM><\/\1>/gi, '<$1>$2<\/$1>' );
		};
		
        function formatText(command, option) {
            iframe.contentWindow.focus();
            try{
                iframe.contentWindow.document.execCommand(command, false, option);
            }catch(e){  }
            iframe.contentWindow.focus();
        };

        function setSelectedType(node, select) {
            while(node.parentNode) {
                var nName = node.nodeName.toLowerCase();
                for(var i=0;i<select.options.length;i++) {
                    if(nName==select.options[i].value){
                        select.selectedIndex=i;
                        return true;
                    }
                }
                node = node.parentNode;
            }
            select.selectedIndex=0;
            return true;
        };
		
		function getSelectionRange() {
			﻿  var rng﻿  = null;
			﻿  iframe.focus();			﻿  
			﻿  if(iframe.contentWindow.getSelection) {
			﻿  ﻿  rng = iframe.contentWindow.getSelection().getRangeAt(0);
			﻿  ﻿  ﻿var s = rng.startContainer;
			﻿  ﻿  ﻿if(s.nodeType === Node.TEXT_NODE)
			﻿  ﻿  ﻿  ﻿  rng.setStartBefore(s.parentNode);
			﻿  } else {
			﻿  ﻿  rng = iframe.contentWindow.document.selection.createRange();
			﻿  }			
			﻿  return rng;
		};
		
		function setSelectionReplaceWith(html) {
		﻿  var rng﻿  = getSelectionRange();		
		﻿  if(!rng) return;		﻿  
		﻿  formatText('removeFormat'); 		
		﻿  if(iframe.contentWindow.getSelection) {
		﻿  ﻿  rng.deleteContents();
		﻿  ﻿  rng.insertNode(rng.createContextualFragment(html));
		﻿  ﻿  formatText('delete');
		﻿  } else {
		﻿  ﻿  formatText('delete');
		﻿  ﻿  rng.pasteHTML(html);
		﻿  }
		};
		
		function getSelectionText(){
		﻿  if(iframe.contentWindow.getSelection)﻿  
		﻿  ﻿  return iframe.contentWindow.getSelection().toString();		
		﻿  return iframe.contentWindow.document.selection.createRange().text;
		};

        function getSelectionElement() {
            if (iframe.contentWindow.document.selection) {
                selection = iframe.contentWindow.document.selection;
                range = selection.createRange();
                try {
                    node = range.parentElement();
                } catch (e) {   return false;    }
            } else {
                try {
                    selection = iframe.contentWindow.getSelection();
                    range = selection.getRangeAt(0);
                }catch(e){   return false;         }
                node = range.commonAncestorContainer;
            }
            return node;
        };
        enableDesignMode();
    }); //return this.each   
    }; // rte
} // if
})(jQuery);