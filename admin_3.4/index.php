<?php // v4
include('inc/_config.php');
include('inc/settings.php');
include('inc/func.php');

include('controller/PanelMvc.php');
include('controller/Listing.php');
include('controller/ListingMvc.php');
include('controller/Form.php');
include('controller/FormMvc.php');
include('controller/Relation.php');
include('controller/RelationMvc.php');
include('controller/Loader.php');
include('controller/Postback.php');
include('controller/Hook.php');
include('controller/Config.php');
include('controller/Auth.php');

$config = new Config();

Hook::Action();

// TODO Add module of FileBackup
// TODO Auth tokens , Auth protect file manager
// TODO Add form input validator AND input chnaged should change window.changed = true
// TODO Add option todo Relation selection is Require sometime
// TODO Add temp database to undo any changes
// TODO FormMvc add Video type with preview
// TODO fields id can duplicate when relaton popup (filemanager callback issue)


if(get('set_form_ajax') ) {
    echo Loader::Current()->Submit();
    die;
}

$json_postback = array( 'status'=> 0  ) ;
if (get('set_form_classic')) {
    $json_postback =     json_decode(Loader::Current()->Submit(), true) ;
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

    <?= Loader::Current()->RelationMvc->GetTabsCont(1); ?>

</div>
<?
    die;
}

if (get('ajax') == 'list') {
   echo Loader::Current()->GetListing();
   die ;
}


?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin v<?= Config::$version ?></title>

        <script src="js/jquery-2.1.1.min.js"></script>

        <link href="css/ionicons.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">

        <link href="bs/bootstrap.min.css" rel="stylesheet">
        <script src="bs/bootstrap.min.js"></script>


        <link href="bs/bootstrap-table.1.6.0.css" rel="stylesheet">
        <script src="bs/bootstrap-table.1.6.0.js"></script>


        <script src="js/jquery.tableExport/jquery.base64.js"></script>
        <script src="js/jquery.tableExport/html2canvas.js"></script>
        <script src="js/jquery.tableExport/tableExport.js"></script>
        <script src="bs/bootstrap-table-export.min.js"></script>

        <link href="bs/bootstrap-colorpicker.css" rel="stylesheet"/>
        <script src="bs/bootstrap-colorpicker.js" ></script>

        <link href="bs/bootstrap-datetimepicker.css" rel="stylesheet"/>
        <script src="bs/bootstrap-datetimepicker.js" ></script>

        <link href="bs/bootstrap-slider.css" rel="stylesheet"/>
        <script src="bs/bootstrap-slider.js" ></script>

        <script src="tinymce/jquery.tinymce.min.js"></script>

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="bs/locale/bootstrap-table-fr-FR.js"></script>
        <link href="css/admin.css" id="admin-css" rel="stylesheet">

        <script type="text/javascript" src="js/init.js"></script>

<?
echo Hook::Css();
echo Hook::Js();
?>
</head>

<body>

<div class="wrapper">
    <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
        <div class="logo-area">
            <a class="btn btn-link btn-nav-sidebar-minified pull-left"  onclick="$('.wrapper').toggleClass('main-nav-minified')"><i class="icon ion-arrow-swap"></i></a>
            <a class="btn btn-link btn-off-canvas pull-left" onclick="$('.wrapper').removeClass('main-nav-minified').toggleClass('off-canvas-active')"><i class="icon ion-navicon-round"></i></a>
            <a class="navbar-brand" href="?" ><i class="glyphicon glyphicon-dashboard"></i> ADMINPANEL</a>
        </div>
    </nav>


    <div class="col-left" id="col-left">
        <nav class="main-nav" id="main-nav">

            <h3><a href="?"><i class="fa fa-dashboard"></i> Home</a></h3>
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

                    <?= Hook::Menu(); ?>
                </li>
            </ul>

            <h3><a href="?logout=1"><i class="glyphicon glyphicon-log-out"></i> Deconnexion</a></h3>
        </nav>
    </div>



<div id="col-right">

    <div id="message" >
        <? if ($json_postback['status']){
            echo '<div class="alert alert-info" ><a href="#" class="close btn btn-default" data-dismiss="alert">&times;</a>' .  $json_postback['message'] . ' '. $json_postback['status'] . '</div>' ;
        } ?>
    </div>
    <? if (Loader::Current()){ ?>
        <div class="container-fluid primary-content">


            <ol id="breadcrumb" class="breadcrumb">
            <?= Loader::Current()->GetBreadcrumb(); ?>
                <div class="toggle-c-view" onclick="$('#col-right').toggleClass('c-view')">
                    <i class="fa fa-columns"></i>
                </div>
            </ol>



    <div id="listing">
         <?= Loader::Current()->ListingMvc->GetPanel(); ?>
    </div>


    <form class="main-form tabbable tabs" data-toggle="validator" method="post"
          action="?set_form_classic=1&tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=<?= Loader::Current()->id ? 'mod' : 'add' ; ?>" >

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-controls-navbar-collapse">
                        <i class="fa fa-pencil-square-o"></i>
                    </button>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-tabs-navbar-collapse">
                        <i class="ionicons ion-android-more-horizontal"></i>
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
                        <?= Hook::Tabs(); ?>
                    </ul>
                </div>

                <!-- Controls -->
                <div class="collapse navbar-collapse" id="bs-controls-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><button type="submit" class="save"><i class="glyphicon glyphicon-save"></i> Enregistrer</a></button></li>



                        <li data-controller="devider" class="nav-divider"></li>
                        <li data-controller="del"><a data-confirm="Etes-vous certain de vouloir supprimer?" href="?tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=del"> <i class="glyphicon glyphicon-trash" ></i> Supprimer</a></li>
                        <li data-controller="devider" class="nav-divider"></li>
                        <li data-controller="dup"><a data-confirm="Etes-vous certain de vouloir dupliquer?" href="?tbl=<?= Loader::Current()->name ?>&id=<?= Loader::Current()->id ?>&action=dup"> <i class="glyphicon glyphicon-plus"></i> Dupliquer</a></li>

                        <?= Hook::Controls(); ?>

                    </ul>
                </div>
                <!-- / Controls -->
            </div>
        </nav>



        <ul id="tabs" class="nav nav-tabs nav-justified collapse in" data-tabs="tabs">
            <li class="active"><a href="#tab-form-<?= Loader::Current()->name ?>" data-toggle="tab">Form</a></li>
            <?= Loader::Current()->RelationMvc->GetTabs(); ?>
            <?= Hook::Tabs(); ?>
        </ul>

        <div id="my-tab-content" class="tab-content">

            <div class="tab-pane active" id="tab-form-<?= Loader::Current()->name ?>">
                <?= Loader::Current()->FormMvc->GetPanels(); ?>
            </div>

            <?= Loader::Current()->RelationMvc->GetTabsCont(); ?>
            <?= Hook::TabsCont(); ?>

        </div>

        <?= Hook::Footer(); ?>

    </form>
    </div>

    <? }else{ ?>

<?= Hook::Dashboard(); ?>
<div class="ga-dash clearfix"> </div>

<script>
    var GA_KEY = '<?= $config->GetGoogleAnalyticsKEY() ; ?>' ;
</script>
<script src="js/gadash.js" type="text/javascript"></script>

	<? } ?>
</div>
</div><!-- wrapper -->




<div class="modal fade modal-window" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
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

<div class="modal fade modal-window" id="modal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
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
<div class="scrolltop" style=" position:fixed;" onclick=" $('html, body').animate({scrollTop:0},300);"><i class="fa fa-arrow-up"></i></div>

</body>
</html>