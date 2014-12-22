<?php // v4
include('inc/config.php');
include('inc/func.php');

define('NL',"\r\n") ;

$db = new Db();
$cookie = new Cookie('x_admin_user');

include('controller/PanelMvc.php');
include('controller/Listing.php');
include('controller/ListingMvc.php');
include('controller/Form.php');
include('controller/FormMvc.php');
include('controller/Relation.php');
include('controller/RelationMvc.php');
include('controller/Loader.php');

include('controller/Postback.php');

include('Config.php');

/*INIT*/
Loader::Load() ;

//TODO  add bans ips system
//TODO token check
/*
 * TODO: Add bread crumbs / Add login
 * TODO save user state sorting / search for each table in cache
*/

if(post('set_form_ajax') ) {
    Loader::Current()->Submit();
}
if (get('ajax') == 'list') {
    //sleep(1);
    Loader::Current()->GetListing();
}


?><!DOCTYPE html>
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

        <script src="bs/bootstrap-table.1.5.0.js"></script>
        <link href="bs/bootstrap-table.1.5.0.css" rel="stylesheet">

        <script src="js/jquery.tableExport/jquery.base64.js"></script>
        <script src="js/jquery.tableExport/html2canvas.js"></script>
        <script src="js/jquery.tableExport/tableExport.js"></script>
        <script src="bs/bootstrap-table-export.min.js"></script>


<!--
        <script src="bs/wysihtml5-0.3.0.js"></script>
        <link href="bs/bootstrap-wysihtml5.css" rel="stylesheet"/>
        <script src="bs/bootstrap-wysihtml5.js"></script>
-->
        <script src="js/jquery-deparam.js"></script>

        <script src="bs/bootstrap-form-validator.min.js" ></script>

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
       <script src="bs/locale/bootstrap-table-fr-FR.min.js"></script>
        <link href="css/admin.css" id="admin-css" rel="stylesheet">

        <script>

            $(document).ready(function(){

                $('.rte').wysihtml5({
                    stylesheets: ["css/include-me-in-rte.css"]
                    ,"html":true
                    ,'locale':'en'
                });

                var tbl = $('#tab-list .table')
                    .on('click-row.bs.table', function (e, row, $element) {
                        params = $.deparam(window.location.search) ;
                        params.id = row.id ;
                        window.location = '?' + ($.param(params) ) ;
                })
                if (tbl.size() == 0 )
                    throw ("Unable to set links to table");
            })

        </script>
</head>

<body class="fixed-top-active">



<div class="wrapper">
    <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
        <div class="logo-area">
            <a class="btn btn-link btn-nav-sidebar-minified pull-left"  onclick="$('.wrapper').toggleClass('main-nav-minified')"><i class="icon ion-arrow-swap"></i></a>
            <a class="btn btn-link btn-off-canvas pull-left" onclick="$('.wrapper').removeClass('main-nav-minified').toggleClass('off-canvas-active')"><i class="icon ion-navicon"></i></a>
            <a class="navbar-brand" href="#" onclick="$('#admin-css').attr('href','#');">ADMINPANEL</a>

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
                                        <i class="<?= $t->icon ?>"></i>
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
    <form class="main-form tabbable tabs-left" data-toggle="validator" method="post">
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <i class="fa fa-pencil-square-o"></i>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><button type="submit" class="save"><i class="glyphicon glyphicon-save"></i> Save</a></button></li>
                        <li class="nav-divider"></li>
                        <li><a href="#"> <i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                        <li class="nav-divider"></li>
                        <li><a href="#"> <i class="glyphicon glyphicon-plus"></i> Dupplicate</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <li class="active"><a href="#tab-list" data-toggle="tab">List</a></li>
            <li><a href="#tab-form" data-toggle="tab">Form</a></li>
            <?= Loader::Current()->RelationMvc->GetTabs(); ?>
        </ul>

        <div id="my-tab-content" class="tab-content">
            <div class="tab-pane active" id="tab-list">
                <?= Loader::Current()->ListingMvc->GetPanel(); ?>
            </div>
            <div class="tab-pane" id="tab-form">
                <div class="row">
                   <?= Loader::Current()->FormMvc->GetPanels(); ?>
                </div>
            </div>

            <?= Loader::Current()->RelationMvc->GetTabsCont(); ?>

        </div>
    </form>
<? } ?>

    </div>
</div>
</div><!-- wrapper -->
</body>
</html>