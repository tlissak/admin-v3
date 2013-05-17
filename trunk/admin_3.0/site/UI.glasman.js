// JavaScript Document
(function(ui){
	ui.formLoad(function (){
		$('.fac-gen',this).click(function(){
			var oid = $(this).closest('form').find('.form-id').val() ;
			window.open( $(this).attr('href') +'?id=' +  oid 
			,'facture-'+ oid ,'width=1200,height=500,fullscreen=0,toolbar=1,resizable=1,scrollbars=1,top=200',false) ;
			 ;
			return false; 	
		})
	})
	//alert('in');
		
})(UI) ;