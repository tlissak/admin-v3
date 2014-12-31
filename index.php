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

include('controller/FileUpload.php');

include('controller/Postback.php');

include('Config.php');

/*INIT*/
Loader::Load() ;

// TODO add bans ips system
// TODO token check
// TODO image preview for List Mvc/ State Mvc
// TODO Add bread crumbs
// TODO Add login and auth system
// TODO save in cache user state for each table sorting and view

if (get('upload')){
    header('Content-type: application/json');
    echo ( new FileUpload(get('tbl').DS.get('fld').DS.get('id').DS) );
    die ;
}
if(get('set_form_ajax') ) {
    echo Loader::Current()->Submit();
    die;
}
if (get('ajax') == 'form'){
?>
<ul class="nav nav-tabs nav-justified" data-tabs="tabs">
            <li class="active"><a href="#tab-form-<?= Loader::Current()->name ?>-ajax" data-toggle="tab">Form</a></li>
            <?= Loader::Current()->RelationMvc->GetTabs(); ?>
</ul>

<div id="my-tab-content-0" class="tab-content">

    <div class="tab-pane active" id="tab-form-<?= Loader::Current()->name ?>-ajax">
        <?= Loader::Current()->FormMvc->GetPanels(); ?>
    </div>

    <?= Loader::Current()->RelationMvc->GetTabsCont(); ?>

</div>
<?
    die;
}

if (get('ajax') == 'list') {
    //header('Content-type: application/json');
   echo Loader::Current()->GetListing();
   die ;
}


?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin V4</title>

        <script src="js/jquery-2.1.1.min.js"></script>

        <link href="css/ionicons.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">

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
        <!--
       <script src="js/jquery-deparam.js"></script>
//TODO change bootstrap validator
               <script src="bs/bootstrap-form-validator.min.js" ></script>

               <link rel="stylesheet" type="text/css" href="bs/bootstrap3-wysihtml5.css" />
               <script src="bs/wysihtml5x-toolbar.0.5.min.js"></script>
               <script src="bs/bootstrap3-wysihtml5.js"></script>
       -->
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

        <script src="tinymce/jquery.tinymce.min.js"></script>


<!--

        <script src="tinymce/tinymce.gzip.js"></script>
<script src="tinymce/tinymce.min.js"></script>
        <link href="js/jquery.fancybox.css" />
        <script src="js/jquery.fancybox.pack.js"></script>
        -->
<!--
        <link href="bs/fileinput.min.css" rel="stylesheet"/>
        <script src="bs/fileinput.min.js" ></script>
-->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
       <script src="bs/locale/bootstrap-table-fr-FR.min.js"></script>
        <link href="css/admin.css" id="admin-css" rel="stylesheet">


            <script type="text/javascript" src="js/init.js"></script>
</head>

<body>

<div class="wrapper">
    <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
        <div class="logo-area">
            <a class="btn btn-link btn-nav-sidebar-minified pull-left"  onclick="$('.wrapper').toggleClass('main-nav-minified')"><i class="icon ion-arrow-swap"></i></a>
            <a class="btn btn-link btn-off-canvas pull-left" onclick="$('.wrapper').removeClass('main-nav-minified').toggleClass('off-canvas-active')"><i class="icon ion-navicon"></i></a>
            <a class="navbar-brand" href="#" >ADMINPANEL</a>

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


    <? if (Loader::Current()){ ?>
        <div class="container-fluid primary-content">
            <div id="message" class="alert alert-success" style="display: none;"><a href="#" class="close" onclick="$(this).parent().hide();">&times;</a><div class="alert-block"></div></div>
    <div id="listing">
         <?= Loader::Current()->ListingMvc->GetPanel(); ?>
    </div>


    <form class="main-form tabbable tabs" data-toggle="validator" method="post"
          action="?set_form_ajax=1&tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=<?= Loader::Current()->id ? 'mod' : 'add' ; ?>" >

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-controls-navbar-collapse">
                        <i class="fa fa-pencil-square-o"></i>
                    </button>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-tabs-navbar-collapse">
                        <i class="icon ion-navicon"></i> Panels
                    </button>


                </div>

                <div class="navbar-collapse collapse" id="bs-tabs-navbar-collapse">
                    <ul  class="nav navbar-nav" data-tabs="tabs">
                        <li>
                            <label> <a data-toggle="collapse" data-target="#form-panel-listing-<?= Loader::Current()->name ?>">  <input type="checkbox">  Listing</a></label>
                        </li>
                        <li class="nav-divider"></li>
                        <li><a href="#tab-form-<?= Loader::Current()->name ?>" data-toggle="tab">Form</a></li>
                        <?= Loader::Current()->RelationMvc->GetTabs(); ?>
                    </ul>
                </div>

                <!-- Controls -->
                <div class="collapse navbar-collapse" id="bs-controls-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><button type="submit" class="save"><i class="glyphicon glyphicon-save"></i> Save</a></button></li>

                <!-- TODO When ajax add ok show controls -->

                        <li class="nav-divider"></li>
                        <li><a href="?set_form_ajax=1&tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=del"> <i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                        <li class="nav-divider"></li>
                        <li><a href="?set_form_ajax=1&tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=dup"> <i class="glyphicon glyphicon-plus"></i> Dupplicate</a></li>


                    </ul>
                </div>
                <!-- / Controls -->
            </div>
        </nav>



        <ul id="tabs" class="nav nav-tabs nav-justified collapse in" data-tabs="tabs">
            <li class="active"><a href="#tab-form-<?= Loader::Current()->name ?>" data-toggle="tab">Form</a></li>
            <?= Loader::Current()->RelationMvc->GetTabs(); ?>
        </ul>

        <div id="my-tab-content" class="tab-content">

            <div class="tab-pane active" id="tab-form-<?= Loader::Current()->name ?>">
                <?= Loader::Current()->FormMvc->GetPanels(); ?>
            </div>

            <?= Loader::Current()->RelationMvc->GetTabsCont(); ?>

        </div>
    </form>
    </div>

    <? } ?>
</div>
</div><!-- wrapper -->



<div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <input type="submit" class="btn btn-primary" value="Envoyer ma demande" name="contactSubmit">
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade " id="alert" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="ModalFileManager" tabindex="-1" role="dialog" aria-labelledby="ModalFileManagerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ModalFileManagerLabel">File Manager</h4>
            </div>
            <div class="modal-body">
                <iframe src="" style="zoom:0.40" frameborder="0" height="550" width="99.6%"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>