(function(ui){	
	ui.filebrowser = '.filebrowser_workspace' ;
	ui.current_iframe = null ;
	
	ui.docLoad(function(){		
		//CREATE
		var BTN = '<span class="btn-file btn-green"><input type="file" name="filebrowser" data-url="?fld=filebrowser&upload=1" /><i class="icon-plus"></i>   Parcourir ou placer ici une image</span>';
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
		filebrowser_upload_settings =  $.extend({},ui.file_upload_shared_settings);
		filebrowser_upload_settings.uploadFinished = function(i,ofile,res){	UI.browseFiles("?path=filebrowser") ;}		
		
		$(".droparea",ui.filebrowser ).fileupload(filebrowser_upload_settings);

		//a links
		$(document).on("click",ui.filebrowser+' .directories a,'+ui.filebrowser+' .breadcrumb a'	,function(){	
			UI.browseFiles($(this).attr('href')) ;		
			return false ;	
		})
		
		//load list ction
		UI.browseFiles('?path=') ;		
		$('.filebrowser_overlay').click(ui.closeFileBrowser)			
	})
	
	ui.formLoad(function(){
		$('.rte-zone .rte-toolbar .image').unbind('click').bind('click',function(e){
			ui.openFileBrowser() ;		
			ui.current_iframe = $(this).closest('.rte-zone').find('>iframe')[0] ;			
			return false
		})
	})
	
	ui.openFileBrowser = function(){		$('.filebrowser_overlay').fadeIn(100).next().fadeIn(100) ;	}	
	ui.closeFileBrowser = function(){ 		$(".filebrowser_overlay").fadeOut(100).next().fadeOut(100) ;	}	
	
	ui.insertBrowserImage = function(URI){
			if (!ui.current_iframe)	return ;
			ui.current_iframe.contentWindow.focus();
            try{  ui.current_iframe.contentWindow.document.execCommand("InsertImage", false, URI);   }catch(e){  }
            ui.current_iframe.contentWindow.focus();
			ui.closeFileBrowser() ;
	}	
	
	ui.browseFiles = function(path){

			$.ajax(path + '&browse=1&callme=1',{dataType:"json",success: function(o){				
				for(var i=0 , tbl = '<table class="tbl"><tbody>'; i<o.dirs.length;i++){
						tbl += '<tr><td><a href="?path='+o.dirs[i].relative+'">'+o.dirs[i].name+'</a></td></tr>'; 
				}				
				tbl += "</tbody></table>"  ;				
				for(var i=0,list = '';i< o.files.length;i++){
					if ((/.*?(\.jpg|\.png|\.gif)$/i).test(o.files[i].uri))
						o.files[i].name = '<img src="'+o.files[i].uri+'" />' ;						
					list +='<a href="#'+o.files[i].uri+'">'+o.files[i].name+'</a>' ;
				}

				for(var i=0,bc = '<div>';i<o.bc.length;i++){
					if (i==0) o.bc[i].name = '<i class="icon-home"></i> ' + o.bc[i].name ;
					bc += '<a href="?path=' +o.bc[i].relative+ '" >'+o.bc[i].name +'</a> /' ;
				}
				bc +='</div>'
				
				$('.breadcrumb',ui.filebrowser).html(bc);
				$('.directories',ui.filebrowser).html( tbl)
				$(".files",ui.filebrowser).html(list).find('a').click(function(){
					ui.insertBrowserImage($(this).attr('href').replace('#',""));
					return false;
				})
			} ,type:'GET' }) ;
	};
}(UI));