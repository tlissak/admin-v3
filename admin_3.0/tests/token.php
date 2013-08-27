<?php
include("../../inc/config.php" );
  $user_id=42;
  
  $validity_time=600;


//the exact url you want allowed access with token querystring include



  $token_clair=_RIJNDAEL_KEY_ ."http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]   .$_SERVER['HTTP_USER_AGENT'];


  $informations=time()."-".$user_id;

  echo $token = hash('sha256', $token_clair.$informations);
  echo '<br />' ;
  
  // On poste les cookies
  $session_token =  array(  time()+$validity_time ,  $token) ;
  $session_informations =  array(time()+$validity_time ,$informations);
  
  if ( isset($_POST['token'] ) ){
	  
		$token_clair= _RIJNDAEL_KEY_.$_SERVER['HTTP_REFERER'].$_SERVER['HTTP_USER_AGENT'];	
		echo $token = hash('sha256', $token_clair.$_POST["informations"]);
		echo '<br />' ;
		
		if(strcmp($_POST['token'], $token)==0) {
		  echo "signature ok<br>\n";
		  list($date, $user) = split('[-]', $_POST["informations"]);
		  if($date+ $validity_time>time() AND $date <time()) { // On vérifie que la session n'est pas expirée
			echo "session en cour de validité<br>";
			echo "user id:".$user."<br>\n";
		  } else {
			echo "wrong timing<br>";	
		  }
		}else{
		  echo "token check failed<br>";	
		}
  }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
<form method="POST" action="">

		<input type="text" name="token" style="width:80%" value="<?= $session_token[1] ?>" />
        <input type="text" name="informations" style="width:80%" value="<?= $session_informations[1] ?>" />
        <pre><? print_r($session_token); ?>
        
        <? print_r($session_informations); ?>
        
        <?= isset($_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : 'no HTTP_REFERER' ;?>
        
        <?= $_SERVER["HTTP_USER_AGENT"] ;?>
        
        <?= P_SELF ?>
        
        <?= P_SCRIPT?>
        </pre>
      <input type="submit">
    </form>
  </body>
</html>