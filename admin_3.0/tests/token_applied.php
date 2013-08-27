<?php
include("../../inc/config.php" );

class Token{
	
	var $expire = 10 ; 
	
	function context(){
		return time()."-".'foxdanni|LOGIN_TIME_STAMP|2' ;  
	}
	
	function generate(){
		$token_url = _RIJNDAEL_KEY_ .P_SCRIPT ;
		return hash('sha256',$token_url .$this->context());		 
	}
	
	function validate($_token,$_info){
		$token = hash('sha256', _RIJNDAEL_KEY_ .P_SCRIPT .$_info);		
		if(strcmp($_token, $token)==0) {
			echo "signature ok<br>\n";
		 	list($date, $user) = split('[-]', $_info );
		  if($date + $this->expire > time() AND $date < time()) { 
			echo "session en cour de validité<br>";
			echo "user id:".$user."<br>\n";
		  } else {
			echo "wrong timing<br>";	
		  }
		}else{
		  echo "token check failed<br>";	
		}
	}
	
}
$t = new Token();
$token = $t->generate() ;

if ( isset($_POST['token'] ) ){
	$t->validate($_POST['token'] , $_POST["informations"]);
}
  
 
  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
<form method="POST" >

		<input type="text" name="token" style="width:80%" value="<?= $token  ?>" />
        <input type="text" name="informations" style="width:80%" value="<?= $t->context() ?>" />
        <pre>
<?= $token ; ?>
        
<?= $t->context() ; ?>

        </pre>
      <input type="submit">
    </form>
  </body>
</html>