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
	ui.formLoad(function (){	
		$('.sql_workspace').hide();
		$('body .LAY > .LAY-list,#main-form').show();
	})
	
	ui.docLoad(function(){		
			$('.sqler').click(function(){	
				$('body .LAY > .LAY-list,#main-form').toggle();	
				$('.sql_workspace').toggle() ;					
				if ( ! window._sql_editor){
					window._sql_editor = ace.edit("sql_editor");
					window._sql_editor.getSession().setMode("ace/mode/sql");
				}
			})			
			$("#sql_exec").click(function(){
				ui.sql_exec.call(this,window._sql_editor.getSession().getValue()) ;
			})			
			$("#sql_save").hide().click(function(){
				
			})				
	})
	
		
}(UI))