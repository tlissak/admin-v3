<?php // v4
include('inc/config.php');
include('inc/func.php');

$db = new Db();
$cookie = new Cookie('x_admin_user');

include('Listing.php');
include('Mvc.php');
include('Form.php');
include('Relation.php');
include('Loader.php');

include('Config.php');

#region INIT

#endregion




Loader::Load() ;

//p(Loader::Get('product')->relationFields) ;
//p(Loader::Get('category')->relations);
//p(Loader::Get('category')->Listing->_list);


if (get('ajax') == 'list') {
    Loader::Get(get('tbl'))->Mvc->GetJsonBody();
}


?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin V4</title>

        <script src="js/jquery.js"></script>

        <script src="bootstrap/bootstrap.min.js"></script>
        <link href="bootstrap/bootstrap.min.css" rel="stylesheet">

        <script src="bootstrap/bootstrap-table.min.js"></script>
        <link href="bootstrap/bootstrap-table.min.css" rel="stylesheet">

        <script src="jquery.tableExport/jquery.base64.js"></script>
        <script src="jquery.tableExport/html2canvas.js"></script>
        <script src="jquery.tableExport/tableExport.js"></script>
        <script src="bootstrap/bootstrap-table-export.min.js"></script>


        <script src="bootstrap/wysihtml5-0.3.0.js"></script>
        <link href="bootstrap/bootstrap-wysihtml5.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-wysihtml5.js"></script>


        <link href="bootstrap/bootstrap-editable.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-editable.min.js"></script>

        <script src="bootstrap/bootstrap-table-editable.min.js" data-dependeds="bootstrap-editable"></script>

        <link href="bootstrap/bootstrap-colorpicker.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-colorpicker.js" ></script>

        <link href="bootstrap/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-datepicker.js" ></script>

        <link href="bootstrap/bootstrap-slider.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-slider.js" ></script>

        <link href="bootstrap/bootstrap-progressbar-3.3.0.css" rel="stylesheet"/>
        <script src="bootstrap/bootstrap-progressbar.js" ></script>

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->


        <link href="p.css" rel="stylesheet">
</head>

<body class="fixed-top-active">
<div class="wrapper">
<nav role="navigation" class="top-bar navbar-fixed-top">
<div class="logo-area">
    <a class="btn btn-link btn-nav-sidebar-minified pull-left" id="btn-nav-sidebar-minified" href="#"><i class="icon ion-arrow-swap"></i></a>
    <a class="btn btn-link btn-off-canvas pull-left"><i class="icon ion-navicon"></i></a>
    <div class="logo pull-left">
        <a href="index.html">
            <img alt="QueenAdmin Logo" src="assets/img/queenadmin-logo.png">
        </a>
    </div>
</div>
<form role="form" class="form-inline searchbox hidden-xs">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon ion-ios7-search"></i></span>
            <input type="search" placeholder="search the site ..." class="form-control">
        </div>
    </div>
</form>

</nav>


<div class="col-left" id="col-left">
<nav class="main-nav" id="main-nav">

<h3>MAIN</h3>
<ul class="main-menu">

    <li class="has-submenu active">



        <a class="submenu-toggle" href="#">
            <i class="icon ion-android-note"></i>
            <span class="text">Forms</span></a>

        <ul class="list-unstyled sub-menu collapse in">
            <? foreach (Loader::$instances as $t) {
                if ( ! $t->Hide) { ?>
                    <li><a class="<?= $t->name == get('tbl') ? 'active' : '' ; ?>"
                           data-tbl="<?= $t->name; ?>" href="?tbl=<?= $t->name; ?>&menu=1">
                            <i class="glyphicon glyphicon-link"></i>
                            <span class="text"><?= l($t->name); ?></span>
                            <span class="badge bg-primary">NEW</span>
                        </a></li>
                <? }
            } ?>

        </ul>
    </li>


</ul>

<h3>ESSENTIALS</h3>
<ul class="main-menu">
    <li class="has-submenu">
        <a class="submenu-toggle" href="#"><i class="icon ion-ios7-pie"></i><span class="text">Charts</span></a>
        <ul class="list-unstyled sub-menu collapse">
            <li class="active">
                <a href="charts-basic.html">
                    <span class="text">Basic</span>
                </a>
            </li>
            <li>
                <a href="charts-interactive.html">
                    <span class="text">Interactive Charts</span>
                </a>
            </li>
        </ul>
    </li>
    <li class="has-submenu">
        <a class="submenu-toggle" href="#"><i class="icon ion-android-storage"></i><span class="text">Tables</span></a>
        <ul class="list-unstyled sub-menu collapse">
            <li class="active">
                <a href="tables-static.html">
                    <span class="text">Static Table</span>
                </a>
            </li>
            <li>
                <a href="tables-dynamic.html">
                    <span class="text">Dynamic Table</span>
                </a>
            </li>
        </ul>
    </li>
    <li><a href="maps.html"><i class="icon ion-earth"></i><span class="text">Maps</span></a></li>
    <li><a href="typography.html"><i class="icon ion-edit"></i><span class="text">Typography</span></a></li>
    <li class="has-submenu">
        <a class="submenu-toggle" href="#"><i class="icon ion-android-sort"></i><span class="text">Menu Levels <span class="badge bg-primary">NEW</span></span></a>
        <ul class="list-unstyled sub-menu collapse">
            <li class="has-submenu">
                <a class="submenu-toggle" href="#">
                    <span class="text">Second Lvl 1</span>
                </a>
                <ul class="list-unstyled sub-menu collapse">
                    <li>
                        <a href="#">
                            <span class="text">Third Lvl 1</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="text">Third Lvl 2</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="text">Third Lvl 3</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <span class="text">Second Lvl 2</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="text">Second Lvl 3</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="text">Second Lvl 4</span>
                </a>
            </li>
        </ul>
    </li>
</ul>
</nav>
</div>


<div id="col-right">
        <div class="container-fluid primary-content">
            <h3>Panel title</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Panel title</h3>
                </div>
                <div class="panel-body">
<?

if (get('tbl')){
    echo Loader::Get(get('tbl'))->Mvc->GetHeader();
}

?>
            </div>
        </div>
</div>
</div>
</body>
</html>