(function(ui){
	
	ui.docLoad(function(){		
		var $elms =  $('<div class="filebrowser_workspace context">\
		<div class="LAY-list"></div>\
		<div class="LAY-message"></div>\
		<div class="LAY-controls"></div>\
		<div id="file_preview" class="LAY-center"></div>\
		</div>' );

		$("body",document).append($elms)
		
		$("#basecontrol").after('<hr /><a href="#sqler" class="btn-orange filebrowser" style="display:block;margin:10px;" > <i class="icon-mysql-dolphin"></i>  Files</a>')
		

	})
	
	
	
	ui.docLoad(function(){	
		$(document).on("click",'.filebrowser_workspace .LAY-list a,.filebrowser_workspace .LAY-controls a',function(){
			UI.browseFiles($(this).attr('href')) ;
			return false ;
		})
		UI.browseFiles('?path=') ;
	})
	
	ui.browseFiles = function(path){
			$.ajax(path + '&browse=1',{dataType:"json",success: function(o){
				
				tbl = '<div class="list"><table class="tbl"><tbody>' ;	
				
				
				for(var i=0; i<o.dirs.length;i++){
						tbl += '<tr><td><a href="?path='+o.dirs[i].relative+'">'+o.dirs[i].name+'</a></td></tr>'; 
				}
				
				tbl += "</tbody></table></div>"  ;
				list = '';
				for(var i=0;i< o.files.length;i++){
					if ((/.*?(\.jpg|\.png|\.gif)$/i).test(o.files[i].uri))
						o.files[i].name = '<img src="'+o.files[i].uri+'" />' ;
						
					list +='<a href="?path='+o.files[i].relative+'">'+o.files[i].name+'</a>' ;
				}
				
				bc = '<div class="button-group">' ;
				for(var i=0;i<o.bc.length;i++){
					if (i==0) o.bc[i].name = '<i class="icon-home"></i> ' + o.bc[i].name
					bc += '<a href="?path=' +o.bc[i].relative+ '" class="btn">'+o.bc[i].name +'</a> /' ;
				}
				bc +='</div>'
				$('.filebrowser_workspace .LAY-controls').html(bc);
				$('.filebrowser_workspace .LAY-list').html( tbl)
				$("#file_preview").html(list) ;
				
				
				
			} ,type:'GET' }) ;
	};
	
	
	
	
}(UI));