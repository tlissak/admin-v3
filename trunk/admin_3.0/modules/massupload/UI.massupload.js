(function(ui){
	
	//Override :
	UI.file_upload_shared_settings.maxfiles = 20 ;
	
	
	
	/* 
	
	WIll be activted by : 
	
	->_File('path','Fichier',array('extends'=>' multiple '))

	*/
	
	UI.file_upload_shared_settings.uploadFinished = function(i,ofile,res){		//	c('uploadFinish',this,file);	
			
			f = this.droparea.find("."+ofile.name.replace(/[^a-zA-Z0-9]+/g,'-'))
			f.find("progress").hide()	;
			
			
			for(fileinput in res.files){
				for(var i=0;i<res.files[fileinput].length;i++){
					file = res.files[fileinput][i];
					if (file.error == 0){
						
						f.html('<img src="'+res.virtual_path + file.uploaded+'" alt="alt:'+file.uploaded+'" style="max-height:100px;max-width:100px;" />'
						+ '<a class="btn link-delete-file" data-path="'+encodeURIComponent(file.uploaded)+'"><i class="icon-trash"></i></a>'
						+'<input type="hidden" name="' +fileinput +'" value="'+file.uploaded+'" />');
						
						
						if (  this.droparea.data('opts').fallback.attr('multiple')) {
							$form =  $(this.droparea).closest('form')
							$form.find('.form-id').val(0) ;
							$form.find('.form-action').val('add')
							$form.trigger('submit');
							
						}
						
					}else{
						alert(file.error_msg);
					}
				}
			}
	}
	
		
}(UI))