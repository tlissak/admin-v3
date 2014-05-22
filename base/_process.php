<?php
include('inc/conf.php');
include('inc/func.php');

$br = '<br />'."\r\n" ;

if(post('postback')  ){

	$html = '';		
	if(! is_email(post('E-Mail')) || strlen(post('Nom'))  < 4 ){
			echo 'Adresse E-Mail ou Nom  non valide !';
			die ;
	}
	foreach($_POST as $field=>$value){
			$html .= $field .' : ' . $value . $br ;
	}
	$mailto =  _EMAIL_TO_;
	if (is_email(post('mailto')) ){
		$mailto .= ',' .post('mailto') ;
	}
	@mail( $mailto,'Message [JLC] '  ,$html,_EMAIL_HEADER_) ;	
	echo 'Votre message a bien été envoyé !' . "\n";
}else{
	echo "Formulaire inconnue.."; 
	die ;	
}
?>