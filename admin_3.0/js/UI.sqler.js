(function(ui){
	
	
	
	ui.sql_exec = function(sql){
		$.ajax('?set_sql=1&sql='+sql,{
			context:$(this).closest(".context")
			,success: function(o){
				if (o.error){
					$('.LAY-message',this).html('<div>'+o.error+'</div>');
				}else {
					$('.LAY-message',this).html('<div>'+o.sql+'</div>');
					if (o.list && o.list.length)
						//c(o.list)
						tbl = '<table class="tbl"><thead><tr>' ;	
						var kys = [] ;
						for(var k in o.list[0]){
							kys.push(k) ;
							tbl += '<th>'+k+'</th>'; 
						}
						tbl += "</tr></thead><tbody>"  ;						
						for(var i=0;i< o.list.length;i++){
							tbl +="<tr>" ;							
							for (var j=0;j < kys.length ; j++){
								tbl += '<td><pre>'+o.list[i][kys[j]]+'</pre></td>'; 
							}
							tbl +="</tr>" ;
						}												
						tbl += "</tbody></table>"  ;
						
						$('.LAY-list',this).html(tbl);
				}
			}
		})
	}
	
	ui.AceScriptLoaded = false;
	
	ui.formLoad(function (){	
		$('.sql_workspace').hide();
		$('body .LAY > .LAY-list,#main-form').show();
	})
	
	ui.docLoad(function(){		
		var $elms =  $('<div class="sql_workspace context">\
		<div class="LAY-list"></div>\
		<div class="LAY-message"></div>\
		<div class="LAY-controls">\
			<a href="#" class="btn-red" id="sql_exec" >Execute</a>\
			<a href="#" class="btn-green right" id="sql_save" >Save</a>\
		</div>\
		<div id="sql_editor" class="LAY-center">SELECT * FROM category</div>\
		</div>' );

		$("body",document).append($elms)
		
		$("#basecontrol").after('<hr /><a href="#sqler" class="btn-orange sqler" style="display:block;margin:10px;" > <i class="icon-mysql-dolphin"></i>  Sqler</a>')
		
		var AceScriptLoading = false ;
		
		$('.sqler').click(function(){
				if (AceScriptLoading) { c("Editor is being load") ;return ;}
				
				$('body .LAY > .LAY-list,#main-form').toggle();	
				$('.sql_workspace').toggle() ;
				
				if (window._sql_editor){ return ;}
				AceScriptLoading = true;
				$.getScript("http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js", function(data, textStatus, jqxhr) {
					if ( ! window._sql_editor){
						window._sql_editor = ace.edit("sql_editor");
						window._sql_editor.getSession().setMode("ace/mode/sql");
						AceScriptLoading = false;
						ui.AceScriptLoaded = true;
					}	
				});
		})	
		
		$("#sql_exec",$elms).click(function(){
				ui.sql_exec.call(this,window._sql_editor.getSession().getValue()) ;
		})			
		$("#sql_save",$elms).hide().click(function(){	})				
	})
	
		
}(UI))