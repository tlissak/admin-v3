<?php // v4
include('inc/config.php');
include('inc/func.php');

define('NL',"\r\n") ;

$db = new Db();
$cookie = new Cookie('x_admin_user');
include('Mvc.php');
include('Listing.php');
include('ListingMvc.php');
include('Form.php');
include('FormMvc.php');
include('Relation.php');
include('Loader.php');

include('Config.php');

/*INIT*/
Loader::Load() ;

/*TODO: Add bread crumbs / Add login
*/

if(get('set_form_ajax') ) {
    Loader::Current()->Submit();
}
if (get('ajax') == 'list') {
    Loader::Current()->GetListing();
}


?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin V4</title>

        <script src="jquery-2.1.1.min.js"></script>

        <link href="http://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" data-type="1.5.2">

        <script src="bs/bootstrap.min.js"></script>
        <link href="bs/bootstrap.min.css" rel="stylesheet">

        <script src="bs/bootstrap-table.js"></script>
        <link href="bs/bootstrap-table.min.css" rel="stylesheet">

        <script src="jquery.tableExport/jquery.base64.js"></script>
        <script src="jquery.tableExport/html2canvas.js"></script>
        <script src="jquery.tableExport/tableExport.js"></script>
        <script src="bs/bootstrap-table-export.min.js"></script>


        <!--
        <script src="bs/wysihtml5-0.3.0.js"></script>
        <link href="bs/bootstrap-wysihtml5.css" rel="stylesheet"/>
        <script src="bs/bootstrap-wysihtml5.js"></script>
-->

        <link rel="stylesheet" type="text/css" href="bs/bootstrap3-wysihtml5.css" />
        <script src="bs/wysihtml5x-toolbar.0.5.min.js"></script>
        <script src="bs/bootstrap3-wysihtml5.js"></script>

        <link href="bs/bootstrap-editable.css" rel="stylesheet"/>
        <script src="bs/bootstrap-editable.min.js"></script>

        <script src="bs/bootstrap-table-editable.min.js" data-dependeds="bootstrap-editable"></script>

        <link href="bs/bootstrap-colorpicker.css" rel="stylesheet"/>
        <script src="bs/bootstrap-colorpicker.js" ></script>

        <link href="bs/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="bs/bootstrap-datepicker.js" ></script>

        <link href="bs/bootstrap-slider.css" rel="stylesheet"/>
        <script src="bs/bootstrap-slider.js" ></script>

        <link href="bs/bootstrap-progressbar-3.3.0.css" rel="stylesheet"/>
        <script src="bs/bootstrap-progressbar.js" ></script>

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->


        <link href="p.css" rel="stylesheet">

        <script>
            $(document).ready(function(){
                $('.rte').wysihtml5({
                    stylesheets: ["include-me-in-rte.css"]
                    ,"html":true
                    ,'locale':'en' //fr dont exist
                });
            })

        </script>
</head>

<body class="fixed-top-active">



<div class="wrapper">
    <nav role="navigation" class="top-bar navbar-fixed-top">
        <div class="logo-area">
            <a class="btn btn-link btn-nav-sidebar-minified pull-left"  onclick="$('.wrapper').toggleClass('main-nav-minified')"><i class="icon ion-arrow-swap"></i></a>
            <a class="btn btn-link btn-off-canvas pull-left" onclick="$('.wrapper').removeClass('main-nav-minified').toggleClass('off-canvas-active')"><i class="icon ion-navicon"></i></a>
            <div class="logo pull-left">
                <a href="#">
                    ADMINPANEL
                </a>
            </div>
        </div>
    </nav>


    <div class="col-left" id="col-left">
        <nav class="main-nav" id="main-nav">

            <h3>MAIN</h3>
            <ul class="main-menu">
                <li class="has-submenu active">
                    <a class="submenu-toggle" href="javascript:void(0)"  data-toggle="collapse" data-target="#menu-list" aria-expanded="true" aria-controls="menu-list">
                        <i class="glyphicon glyphicon-cog"></i> <span class="text">Menu</span></a>
                    <ul class="list-unstyled sub-menu collapse in" id="menu-list">
                        <? foreach (Loader::$instances as $t) {
                            if ( ! $t->Hide) { ?>
                                <li><a class="<?= $t->name == get('tbl') ? 'active' : '' ; ?>"
                                       data-tbl="<?= $t->name; ?>" href="?tbl=<?= $t->name; ?>&load=1">
                                        <i class="glyphicon glyphicon-<?= $t->icon ?>"></i>
                                        <span class="text"><?= $t->title ?></span>
                                        <? if ($t->badge) { ?>
                                            <span class="badge bg-primary"><?= $t->badge ?></span>
                                        <? } ?>
                                    </a></li>
                            <? }
                        } ?>

                    </ul>
                </li>
            </ul>
        </nav>
    </div>


    <div id="col-right">
        <div class="container-fluid primary-content">


<? if (Loader::Current()){ ?>

    <?= Loader::Current()->ListingMvc->GetPanel(); ?>

            <form>
                <?= Loader::Current()->FormMvc->GetPanels(); ?>
<?
//TODO : CONTROLS HERE IN FIXED
//TODO : RELATION LIST IN DIFFRENT PANEL
?>


            </form>
<? } ?>
        </div>
    </div>
</body>
</html>