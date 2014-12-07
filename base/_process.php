<?php
include('inc/config.php');
include('inc/func.php');

$br = '<br />'."\r\n" ;
$error = array();

if(post('postback')  ){

	$html = '';		
	if(! is_email(post('E-Mail')) || strlen(post('Nom'))  < 4 ){
        $error[] = 'Adresse E-Mail ou Nom  non valide !';
	}

    if (count($error)>0){
        foreach($error as $er){
            echo $er . "\r\n" ;
        }
        die ;
    }

	foreach($_POST as $field=>$value){
			$html .= $field .' : ' . $value . $br ;
	}
	$mailto =  _EMAIL_TO_;
	if (is_email(post('mailto')) ){
		$mailto .= ',' .post('mailto') ;
	}
	@mail( $mailto,'Message '  ,$html,_EMAIL_HEADER_) ;	
	echo 'Votre message a bien été envoyé !' . "\n";
}else{
	echo "Formulaire inconnue.."; 
	die ;	
}
?>