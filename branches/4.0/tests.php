<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin V4</title>

    <script src="js/jquery-2.1.1.min.js"></script>

    <link href="http://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" data-type="1.5.2">

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">


    <script src="bs/bootstrap.min.js"></script>
    <link href="bs/bootstrap.min.css" rel="stylesheet">

    <link href="css/admin.css" rel="stylesheet">

    <script>
        $(document).ready(function () {
            $(document).on("click",'.input-group',function(){
                console.log('group clicked') ;
            })
            $(document).on("click",'input', function () {
                console.log('input clicked')
            })
        })
    </script>
    </head>
<body>

<div class="col-xs-1">
    <div class="form-group">
        <!--<label>-->
        <div class="input-group">

            <div class="input-group-addon"> <input type="checkbox" value="1" class="form-control" id="exampleInputAmount" placeholder="Amount"></div>
            <div class="input-group-addon input-group-addon-clean"> Ttitle</div>

        </div>
        <div class="input-group">

            <div class="input-group-addon"> <input type="checkbox" value="2" class="form-control" id="exampleInputAmount" placeholder="Amount"></div>
            <div class="input-group-addon input-group-addon-clean"> Ttitle</div>

        </div>
        <div class="input-group">

            <div class="input-group-addon"> <input type="checkbox" value="3" class="form-control" id="exampleInputAmount" placeholder="Amount"></div>
            <div class="input-group-addon input-group-addon-clean"> Ttitle</div>

        </div>
       <!-- </label>-->
    </div>
</div>
</form>


</body>

</html>