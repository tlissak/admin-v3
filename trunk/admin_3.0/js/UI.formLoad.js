
(function(ui){
	
	ui.formLoad(function (){
		if($("#main-form").find('input[name="id"]').val() == 0){
			$('.x-dup,.x-del').hide();
		}else{
			$('.x-dup,.x-del').show();
		}
	})
	
	ui.formLoad(function (){	
	
		$('.tabs',this).tabsLite()
		$('textarea.rte',this).rte({ content_css_url:'../css/style.css',	width:'80%',	height:350
		,fullsize:function(){ 
			$(this).closest('.LAY-center').toggleClass('fullsize') ; 
			$(this).closest('.rte-zone-outer ').toggleClass('fullsize')}
		});
		$('.color_picker',this).mColorPicker({imageFolder:'img/color_picker/'});
		$('.date_picker',this).Zebra_DatePicker({format:"d/m/Y"});	
		$(".droparea",this ).fileupload({
					maxfiles: 1
					,maxfilesize:1000
					,queuefiles:0	
					,refresh:300
					,progressUpdated:function(index,file,prec) {
						f = this.droparea.find("."+file.name.replace(/[^a-zA-Z0-9]+/g,'-'));
						f.find('progress').attr({value: prec,max:100});
					}
					,speedUpdated:function(index,file,speed){
						f = this.droparea.find("."+file.name.replace(/[^a-zA-Z0-9]+/g,'-'));
						f.find('.speed').html( speed.toFixed(2) + ' KB/s');
					}
					,allowedfiletypes:['image/jpeg','image/png','image/gif','application/x-zip']
					,dragOver:function(e){ //c('dragOver',this,e)	
						$(this).addClass('dragover') ;
					}
					,dragLeave:function(e){ //c('dragLeave',this,e)	
						$(this).removeClass('dragover') ;			
					}
					,drop:function(e){ //c('drop',this,e)	
						$(this).removeClass('dragover') ;
						$("body").removeClass('dragover');
					}
					,beforeEach :function(file){ //c("beforeEach",this,file);
						f = $('<div class="file '+file.name.replace(/[^a-zA-Z0-9]+/g,'-')+'"><progress></progress><span class="speed"></span> '+file.name+'</div>');
						if (this.maxfiles == 1){
							this.droparea.find('.files').html(f);
						}else{
							this.droparea.find('.files').append(f);
						}
					}
					,uploadFinished:function(i,ofile,res){		//	c('uploadFinish',this,file);	
						f = this.droparea.find("."+ofile.name.replace(/[^a-zA-Z0-9]+/g,'-'))
						f.find("progress").hide()	;
						
						for(fileinput in res.files){
							for(var i=0;i<res.files[fileinput].length;i++){
								file = res.files[fileinput][i];
								if (file.error == 0){
									f.html('<img src="'+res.virtual_path + file.uploaded+'" alt="alt:'+file.uploaded+'" style="max-height:100px;max-width:100px;" />'
									+ '<a class="btn link-delete-file" data-path="'+encodeURIComponent(file.uploaded)+'"><i class="icon-trash"></i></a>'
									+'<input type="hidden" name="' +fileinput +'" value="'+file.uploaded+'" />');
								}else{
									alert(file.error_msg);
								}
							}
						}
					}
					,error:function(err,file, i){
						alert(UI.lang.FILE_UPLOAD[err]);//c('error',this,err,file,i)  
					}
			});
	});
}(UI))