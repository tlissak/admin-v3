(function(ui){	
	ui.filebrowser = '.filebrowser_workspace' ;
	ui.current_iframe = null ;
	ui.filebrowser_upload_settings = {}; 
	
	ui.filebrowser_current_path = '';
	ui.browseFiles_initialize = false ;
	
	ui.docLoad(function(){		
		//CREATE
		var BTN = '<span class="btn-file btn-green"><input type="file" name="filebrowser" data-url="?fld=&upload=1" /><i class="icon-plus"></i>   Parcourir ou placer ici une image</span>';
		$("body",document).append('<div class="filebrowser_overlay"></div>\
		<div class="'+ ui.filebrowser.replace('.','')+' LAY">\
			<div class="LAY-leftcol">\
				<div class="droparea">'+BTN+'</div>\
				<div class="breadcrumb"></div>\
				<div class="directories"></div>\
			</div>\
			<div class="LAY-center files"></div>\
		</div>' );
		
		//Add upload handler		
		UI.filebrowser_upload_settings =  $.extend({},ui.file_upload_shared_settings);
		UI.filebrowser_upload_settings.uploadFinished = function(i,ofile,res){	UI.browseFiles("?path="+ui.filebrowser_current_path) ;}
		
		ui.filebrowser_upload = $(".droparea",ui.filebrowser ).fileupload(UI.filebrowser_upload_settings);
				
		//a links
		$(document).on("click",ui.filebrowser+' .directories a,'+ui.filebrowser+' .breadcrumb a'	,function(){	
			UI.browseFiles($(this).attr('href')) ;		
			return false ;	
		})
				
		$('.filebrowser_overlay').click(ui.closeFileBrowser)	
		
		$("#basecontrol").after('<a href="#browse_files" id="browse_files" class="btn-orange browse" style="display:block;margin:10px;" > <i class="icon-file"></i>  Browse files</a>')
		$('#browse_files').click(function(){ 	UI.openFileBrowser(); ui.browseFilesCallback = null		});
		
		$(document).on("click",UI.filebrowser +" .files a",function(){
			if (UI.browseFilesCallback)
				return UI.browseFilesCallback.call(this);
			return false
		})
	})
	
	ui.formLoad(function(){
		function bindFirst(elm,name, fn) {
				if ($(elm).size() == 0) return; 
				elm.bind(name, fn);
				try{	
					var handlers = $._data(elm.get(0), "events")[name.split('.')[0]] ;	
					var handler = handlers.pop();
					handlers.splice(0, 0, handler);
				}catch(e){
					console.log(e,"UI.fileBrowser.js  FAILD" , elm);	
				}
		};
		
		bindFirst($('.rte-zone .rte-toolbar .image'),'click',function(e){

			//override the click event
			e.stopImmediatePropagation();
			e.stopPropagation();  
			e.preventDefault();			
			
			UI.current_iframe = $(this).closest('.rte-zone').find('>iframe')[0] ;
			UI.browseFilesCallback = function(a){				
					var URI = $(this).attr('href').replace('#',"");
					if (!ui.current_iframe)	return ;
					ui.current_iframe.contentWindow.focus();
					try{  ui.current_iframe.contentWindow.document.execCommand("InsertImage", false, URI);   }catch(e){  }
					ui.current_iframe.contentWindow.focus();
					ui.closeFileBrowser() ;			
					return false;
				
			}
			UI.openFileBrowser() ;	
			return false
		})
		
		$('.droparea .files img').css('cursor','pointer').click(function(){
				UI.fallbackImage = this;
				UI.browseFilesCallback = function(){
					$(ui.fallbackImage).attr('src',$(this).attr('href').replace('#',""))
					$(ui.fallbackImage).prev().val($(this).data('relative'))
					$(ui.fallbackImage).next().data('path',$(this).data('relative'))
						
					UI.closeFileBrowser() ;	
					return false;
				}
				UI.openFileBrowser() ;	
		})
	})
	
	ui.openFileBrowser = function(){			 $('.filebrowser_overlay').fadeIn(100).next().fadeIn(100) ; if (! UI.browseFiles_initialize) { UI.browseFiles('?path=') }	}
	ui.closeFileBrowser = function(){ 						$(".filebrowser_overlay").fadeOut(100).next().fadeOut(100) ;	}	
	
	
	ui.browseFilesCallback = null ;
	
	ui.browseFiles = function(path){
			
			UI.browseFiles_initialize = true ;
			
			UI.filebrowser_current_path = path.replace('?path=',"");
			$(".droparea",UI.filebrowser ).data('opts').url = '?fld='+UI.filebrowser_current_path+'&upload=1'  ;
			
			$.ajax(path + '&browse=1&callme=1',{dataType:"json",success: function(o){				
				for(var i=0 , tbl = '<table class="tbl"><tbody>'; i<o.dirs.length;i++){
						tbl += '<tr><td><a href="?path='+o.dirs[i].relative+'">'+o.dirs[i].name+'</a></td></tr>'; 
				}				
				tbl += "</tbody></table>"  ;				
				for(var i=0,list = '';i< o.files.length;i++){
					if ((/.*?(\.jpg|\.png|\.gif)$/i).test(o.files[i].uri))
						o.files[i].name = '<img src="'+o.files[i].uri+'" />' ;						
					list +='<a href="#'+o.files[i].uri+'" data-relative="'+o.files[i].relative+'">'+o.files[i].name+'</a>' ;
				}

				for(var i=0,bc = '<div>';i<o.bc.length;i++){
					if (i==0) o.bc[i].name = '<i class="icon-home"></i> ' + o.bc[i].name ;
					bc += '<a href="?path=' +o.bc[i].relative+ '" >'+o.bc[i].name +'</a> /' ;
				}
				bc +='</div>'
				
				$('.breadcrumb',ui.filebrowser).html(bc);
				$('.directories',ui.filebrowser).html( tbl) ;
				$(".files",ui.filebrowser).html(list) ;
				
			} ,type:'GET' }) ;
	}
	

	
	
}(UI));