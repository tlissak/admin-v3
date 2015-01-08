<?
include('inc/config.php');
include('inc/func.php');

include "controller/Config.php" ;
include "controller/Auth.php" ;

$config = new Config(true);

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin v<?= Config::$version ?> Login</title>
    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="bs/bootstrap.min.js"></script>
    <link href="bs/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url("//fonts.googleapis.com/css?family=Ubuntu+Condensed");
        html , html > body{ height: 100%; font-family: 'Ubuntu Condensed'}
        body{background:#a7cfdf;background:-moz-linear-gradient(top,#a7cfdf 0,#23538a 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#a7cfdf),color-stop(100%,#23538a));background:-webkit-linear-gradient(top,#a7cfdf 0,#23538a 100%);background:-o-linear-gradient(top,#a7cfdf 0,#23538a 100%);background:-ms-linear-gradient(top,#a7cfdf 0,#23538a 100%);background:linear-gradient(to bottom,#a7cfdf 0,#23538a 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#a7cfdf', endColorstr='#23538a', GradientType=0)}
        #loginbox{            margin: 50px auto 50px;            max-width: 400px;        }
        #loginbox > .panel{ margin-bottom: 0;            background-color: #f8f8f8;            border-color: #d2d2d2;            border-radius: 5px;            border-width: 5px;           box-shadow: 0 1px 0 #cfcfcf;            padding: 10px;            }
        footer,header{ color:#FFF; text-align: center; margin: 10px 0;   }
        header{font-size: 30px; margin-bottom: 20px; padding-bottom: 20px; }
        header i{ vertical-align:-2px; font-size: 90%; margin-right: 5px; }
        footer a{ color:#FFF;}
    </style>
    </head>
<body>
<div class="container">
    <div id="loginbox">
        <header> <i class="glyphicon glyphicon-dashboard"></i> ADMINPANEL v<?= Config::$version ?>  </header>
        <div class="panel">
            <div class="panel-body" style="padding-top:30px">
                <? if ($config->show_login_message){ ?>
                    <div class="alert alert-danger col-sm-12" id="login-alert">
                        Login faild .. you have another <?= $config->role_max_attamps ?> retries
                    </div>
                <? } ?>
                <form role="form" method="post" enctype="multipart/form-data" >
                    <input type="hidden" name="postback" value="login">
                    <div class="form-group">
                        <label for="email" class="control-label">Adresse e-mail</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope "></i></span>
                            <input type="text" placeholder="test@example.com" tabindex="1" autofocus="autofocus" value="<?= post('auth_user')?>" class="form-control" id="email" name="auth_user">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="passwd" class="control-label">
                            Mot de passe
                        </label>
                        <div class="input-group" >
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" placeholder="Mot de passe" tabindex="2" value="" class="form-control" id="passwd" name="auth_pass">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-info" tabindex="4" type="submit" name="submitLogin">
                            <i class="glyphicon glyphicon-check"></i>
                            Connexion
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <a href="https://code.google.com/p/admin-v3/">Google SVN</a> © Lissak.fr™ 2015 - All rights reserved
        </footer>
    </div>

</div>
</body>
</html>