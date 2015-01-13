<?php
include 'config/config.php';

include 'inc.php';

?><!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="robots" content="noindex,nofollow">
  <title>Responsive FileManager</title>
	<link rel="shortcut icon" href="img/ico/favicon.ico">
  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
  <link href="css/bootstrap-lightbox.min.css" rel="stylesheet" type="text/css" />
  <link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/dropzone.min.css" type="text/css" rel="stylesheet" />
	<?php
	$sprite_lang_file = 'img/spritemap_'.$lang.'.png';
	$sprite_lang_file2 = 'img/spritemap@2x_'.$lang.'.png';
	
	if ( ! file_exists($sprite_lang_file) || ! file_exists($sprite_lang_file2)){
		//fallback
		$sprite_lang_file = 'img/spritemap_en_EN.png';
		$sprite_lang_file2 = 'img/spritemap@2x_en_EN.png';
		if ( ! file_exists($sprite_lang_file) || ! file_exists($sprite_lang_file2)){
			// we are in deep ****
			echo '<script>console.log("Error: Spritemap not found!");</script>';
			// exit();
		}
	}
	?>
	<style>
		.dropzone .dz-default.dz-message,
		.dropzone .dz-preview .dz-error-mark,
		.dropzone-previews .dz-preview .dz-error-mark,
		.dropzone .dz-preview .dz-success-mark,
		.dropzone-previews .dz-preview .dz-success-mark,
		.dropzone .dz-preview .dz-progress .dz-upload,
		.dropzone-previews .dz-preview .dz-progress .dz-upload {
			background-image: url(<?= $sprite_lang_file; ?>);
		}

		@media all and (-webkit-min-device-pixel-ratio:1.5),(min--moz-device-pixel-ratio:1.5),(-o-min-device-pixel-ratio:1.5/1),(min-device-pixel-ratio:1.5),(min-resolution:138dpi),(min-resolution:1.5dppx) {
		  	.dropzone .dz-default.dz-message,
		  	.dropzone .dz-preview .dz-error-mark,
			.dropzone-previews .dz-preview .dz-error-mark,
			.dropzone .dz-preview .dz-success-mark,
			.dropzone-previews .dz-preview .dz-success-mark,
			.dropzone .dz-preview .dz-progress .dz-upload,
  			.dropzone-previews .dz-preview .dz-progress .dz-upload {
		    	background-image: url(<?= $sprite_lang_file; ?>);
		    }
		}
	</style>
	<link href="css/jquery.contextMenu.min.css" rel="stylesheet" type="text/css" />	
	<link href="css/bootstrap-modal.min.css" rel="stylesheet" type="text/css" />
	<link href="jPlayer/skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css">
	<!--[if lt IE 8]><style>
	.img-container span, .img-container-mini span {
	    display: inline-block;
	    height: 100%;
	}
	</style><![endif]-->

	<script src='js/jquery-2.1.1.min.js' type='text/javascript'></script>
</head>
<body>
	<input type="hidden" id="popup" value="<?= $popup; ?>" />
	<input type="hidden" id="crossdomain" value="<?= $crossdomain; ?>" />
	<input type="hidden" id="view" value="<?= $view; ?>" />
  <input type="hidden" id="subdir" value="<?= $subdir; ?>" />
  <input type="hidden" id="cur_dir" value="<?= $cur_dir; ?>" />
	<input type="hidden" id="cur_dir_thumb" value="<?= $thumbs_path.$subdir; ?>" />
	<input type="hidden" id="insert_folder_name" value="<?= lang_Insert_Folder_Name; ?>" />
	<input type="hidden" id="new_folder" value="<?= lang_New_Folder; ?>" />
	<input type="hidden" id="ok" value="<?= lang_OK; ?>" />
	<input type="hidden" id="cancel" value="<?= lang_Cancel; ?>" />
	<input type="hidden" id="rename" value="<?= lang_Rename; ?>" />
	<input type="hidden" id="lang_duplicate" value="<?= lang_Duplicate; ?>" />
	<input type="hidden" id="duplicate" value="<?php if($duplicate_files) echo 1; else echo 0; ?>" />
	<input type="hidden" id="base_url" value="<?= $base_url?>"/>
	<input type="hidden" id="base_url_true" value="<?= base_url(); ?>"/>
	<input type="hidden" id="fldr_value" value="<?= $subdir; ?>"/>
	<input type="hidden" id="sub_folder" value="<?= $rfm_subfolder; ?>"/>
  <input type="hidden" id="return_relative_url" value="<?= $return_relative_url == true ? 1 : 0;?>"/>
  <input type="hidden" id="lazy_loading_file_number_threshold" value="<?= $lazy_loading_file_number_threshold?>"/>
	<input type="hidden" id="file_number_limit_js" value="<?= $file_number_limit_js; ?>" />
	<input type="hidden" id="sort_by" value="<?= $sort_by; ?>" />
	<input type="hidden" id="descending" value="<?= $descending?1:0; ?>" />
	<input type="hidden" id="current_url" value="//<?= str_replace(array('&filter='.$filter),array(''),$base_url.$_SERVER['REQUEST_URI']); ?>" />
	<input type="hidden" id="lang_show_url" value="<?= lang_Show_url; ?>" />
	<input type="hidden" id="copy_cut_files_allowed" value="<?php if($copy_cut_files) echo 1; else echo 0; ?>" />
	<input type="hidden" id="copy_cut_dirs_allowed" value="<?php if($copy_cut_dirs) echo 1; else echo 0; ?>" />
	<input type="hidden" id="copy_cut_max_size" value="<?= $copy_cut_max_size; ?>" />
	<input type="hidden" id="copy_cut_max_count" value="<?= $copy_cut_max_count; ?>" />
	<input type="hidden" id="lang_copy" value="<?= lang_Copy; ?>" />
	<input type="hidden" id="lang_cut" value="<?= lang_Cut; ?>" />
	<input type="hidden" id="lang_paste" value="<?= lang_Paste; ?>" />
	<input type="hidden" id="lang_paste_here" value="<?= lang_Paste_Here; ?>" />
	<input type="hidden" id="lang_paste_confirm" value="<?= lang_Paste_Confirm; ?>" />
	<input type="hidden" id="lang_files_on_clipboard" value="<?= lang_Files_ON_Clipboard; ?>" />
	<input type="hidden" id="clipboard" value="<?= ((isset($_SESSION['RF']['clipboard']['path']) && trim($_SESSION['RF']['clipboard']['path']) != null) ? 1 : 0); ?>" />
	<input type="hidden" id="lang_clear_clipboard_confirm" value="<?= lang_Clear_Clipboard_Confirm; ?>" />
	<input type="hidden" id="lang_file_permission" value="<?= lang_File_Permission; ?>" />
	<input type="hidden" id="chmod_files_allowed" value="<?php if($chmod_files) echo 1; else echo 0; ?>" />
	<input type="hidden" id="chmod_dirs_allowed" value="<?php if($chmod_dirs) echo 1; else echo 0; ?>" />
	<input type="hidden" id="lang_lang_change" value="<?= lang_Lang_Change; ?>" />
	<input type="hidden" id="edit_text_files_allowed" value="<?php if($edit_text_files) echo 1; else echo 0; ?>" />
	<input type="hidden" id="lang_edit_file" value="<?= lang_Edit_File; ?>" />
	<input type="hidden" id="lang_new_file" value="<?= lang_New_File; ?>" />
	<input type="hidden" id="lang_filename" value="<?= lang_Filename; ?>" />
	<input type="hidden" id="lang_file_info" value="<?= fix_strtoupper(lang_File_info); ?>" />
	<input type="hidden" id="lang_edit_image" value="<?= lang_Edit_image; ?>" />
	<input type="hidden" id="lang_extract" value="<?= lang_Extract; ?>" />
	<input type="hidden" id="transliteration" value="<?= $transliteration?"true":"false"; ?>" />
	<input type="hidden" id="convert_spaces" value="<?= $convert_spaces?"true":"false"; ?>" />
    <input type="hidden" id="replace_with" value="<?= $convert_spaces? $replace_with : ""; ?>" />
<?php if($upload_files){ ?>
<!-- uploader div start -->

<div class="uploader">
    <div class="text-center">
    	<button class="btn btn-inverse close-uploader"><i class="icon-backward icon-white"></i> <?= lang_Return_Files_List?></button>
    </div>
	<div class="space10"></div><div class="space10"></div>
	<div class="tabbable upload-tabbable"> <!-- Only required for left/right tabs -->
		<?php if($java_upload){ ?>
	    <ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab"><?= lang_Upload_base; ?></a></li>
			<li><a href="#tab2" id="uploader-btn" data-toggle="tab"><?= lang_Upload_java; ?></a></li>
	    </ul>
	    <div class="tab-content">
			<div class="tab-pane active" id="tab1">
		    	<?php } ?>
				<form action="dialog.php" method="post" enctype="multipart/form-data" id="myAwesomeDropzone" class="dropzone">
				    <input type="hidden" name="path" value="<?= $cur_path?>"/>
				    <input type="hidden" name="path_thumb" value="<?= $thumbs_path.$subdir?>"/>
				    <div class="fallback">
					<?=  lang_Upload_file?>:<br/>
					<input name="file" type="file" />
					<input type="hidden" name="fldr" value="<?= $subdir; ?>"/>
					<input type="hidden" name="view" value="<?= $view; ?>"/>
					<input type="hidden" name="type" value="<?= $type_param; ?>"/>
					<input type="hidden" name="field_id" value="<?= $field_id; ?>"/>
          <input type="hidden" name="relative_url" value="<?= $return_relative_url; ?>"/>
					<input type="hidden" name="popup" value="<?= $popup; ?>"/>
					<input type="hidden" name="lang" value="<?= $lang; ?>"/>
					<input type="hidden" name="filter" value="<?= $filter; ?>"/>
					<input type="submit" name="submit" value="<?= lang_OK?>" />
				</form>
			</div>
		    <div class="upload-help"><?= lang_Upload_base_help; ?></div>
			<?php if($java_upload){ ?>
			</div>
			<div class="tab-pane" id="tab2">
		    	<div id="iframe-container"></div>
		    	<div class="upload-help"><?= lang_Upload_java_help; ?></div>
			<?php } ?>
			</div>
	    </div>
	</div>
	
</div>
<!-- uploader div start -->

<?php } ?>		
          <div class="container-fluid">
          

<!-- header div start -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
	    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	    </button>
	    <div class="brand"><?= lang_Toolbar; ?> -></div>
	    <div class="nav-collapse collapse">
		<div class="filters">
		    <div class="row-fluid">
			<div class="span4 half"> 
				  <span class="mobile-inline-visible"><?= lang_Operations; ?>:</span>
			    <?php if($upload_files){ ?>
						    <button class="tip btn upload-btn" title="<?=  lang_Upload_file; ?>"><i class="rficon-upload"></i></button> 
			    <?php } ?>
			    <?php if($create_text_files){ ?>
						    <button class="tip btn create-file-btn" title="<?=  lang_New_File; ?>"><i class="icon-plus"></i><i class="icon-file"></i></button> 
			    <?php } ?>
			    <?php if($create_folders){ ?>
						    <button class="tip btn new-folder" title="<?=  lang_New_Folder?>"><i class="icon-plus"></i><i class="icon-folder-open"></i></button> 
			    <?php } ?>
			    <?php if($copy_cut_files || $copy_cut_dirs){ ?>
				    <button class="tip btn paste-here-btn" title="<?= lang_Paste_Here; ?>"><i class="rficon-clipboard-apply"></i></button> 
				    <button class="tip btn clear-clipboard-btn" title="<?= lang_Clear_Clipboard; ?>"><i class="rficon-clipboard-clear"></i></button> 
				<?php } ?>
			</div>
			<div class="span2 half view-controller">
				  <span class="mobile-inline-visible"><?= lang_View; ?>:</span>
			    <button class="btn tip<?php if($view==0) echo " btn-inverse"; ?>" id="view0" data-value="0" title="<?= lang_View_boxes; ?>"><i class="icon-th <?php if($view==0) echo "icon-white"; ?>"></i></button>
			    <button class="btn tip<?php if($view==1) echo " btn-inverse"; ?>" id="view1" data-value="1" title="<?= lang_View_list; ?>"><i class="icon-align-justify <?php if($view==1) echo "icon-white"; ?>"></i></button>
			    <button class="btn tip<?php if($view==2) echo " btn-inverse"; ?>" id="view2" data-value="2" title="<?= lang_View_columns_list; ?>"><i class="icon-fire <?php if($view==2) echo "icon-white"; ?>"></i></button>
			</div>
			<div class="span6 half types"> 
				<span><?= lang_Filters; ?>:</span>
			    <?php if($_GET['type']!=1 && $_GET['type']!=3){ ?>
			    <input id="select-type-1" name="radio-sort" type="radio" data-item="ff-item-type-1" checked="checked"  class="hide"  />
			    <label id="ff-item-type-1" title="<?= lang_Files; ?>" for="select-type-1" class="tip btn ff-label-type-1"><i class="icon-file"></i></label>
			    <input id="select-type-2" name="radio-sort" type="radio" data-item="ff-item-type-2" class="hide"  />
			    <label id="ff-item-type-2" title="<?= lang_Images; ?>" for="select-type-2" class="tip btn ff-label-type-2"><i class="icon-picture"></i></label>
			    <input id="select-type-3" name="radio-sort" type="radio" data-item="ff-item-type-3" class="hide"  />
			    <label id="ff-item-type-3" title="<?= lang_Archives; ?>" for="select-type-3" class="tip btn ff-label-type-3"><i class="icon-inbox"></i></label>
			    <input id="select-type-4" name="radio-sort" type="radio" data-item="ff-item-type-4" class="hide"  />
			    <label id="ff-item-type-4" title="<?= lang_Videos; ?>" for="select-type-4" class="tip btn ff-label-type-4"><i class="icon-film"></i></label>
			    <input id="select-type-5" name="radio-sort" type="radio" data-item="ff-item-type-5" class="hide"  />
			    <label id="ff-item-type-5" title="<?= lang_Music; ?>" for="select-type-5" class="tip btn ff-label-type-5"><i class="icon-music"></i></label>
			    <?php } ?>
			    <input accesskey="f" type="text" class="filter-input <?= (($_GET['type']!=1 && $_GET['type']!=3) ? '' : 'filter-input-notype'); ?>" id="filter-input" name="filter" placeholder="<?= fix_strtolower(lang_Text_filter); ?>..." value="<?= $filter; ?>"/><?php if($n_files>$file_number_limit_js){ ?><label id="filter" class="btn"><i class="icon-play"></i></label><?php } ?>
			    
			    <input id="select-type-all" name="radio-sort" type="radio" data-item="ff-item-type-all" class="hide"  />
			     <label id="ff-item-type-all" title="<?= lang_All; ?>" <?php if($_GET['type']==1 || $_GET['type']==3){ ?>style="visibility: hidden;" <?php } ?> data-item="ff-item-type-all" for="select-type-all" style="margin-rigth:0px;" class="tip btn btn-inverse ff-label-type-all"><i class="icon-align-justify icon-white"></i></label>
			    
			</div>
		    </div>
		</div>
	    </div>
	</div>
    </div>
</div>

<!-- header div end -->

    <!-- breadcrumb div start -->
    
    <div class="row-fluid">
	<?php	
	$link="dialog.php?".$get_params;
	?>
	<ul class="breadcrumb">	
	<li class="pull-left"><a href="<?= $link?>/"><i class="icon-home"></i></a></li>
	<li><span class="divider">/</span></li>
	<?php
	$bc=explode("/",$subdir);
	$tmp_path='';
	if(!empty($bc))
	foreach($bc as $k=>$b){ 
		$tmp_path.=$b."/";
		if($k==count($bc)-2){
	?> <li class="active"><?= $b?></li><?php
		}elseif($b!=""){ ?>
		<li><a href="<?= $link.$tmp_path?>"><?= $b?></a></li><li><span class="divider"><?= "/"; ?></span></li>
	<?php }
	}
	?>
	<li class="pull-right"><a class="btn-small" href="javascript:void('')" id="info"><i class="icon-question-sign"></i></a></li>
	<li class="pull-right"><a class="btn-small" href="javascript:void('')" id="change_lang_btn"><i class="icon-globe"></i></a></li>
	<li class="pull-right"><a id="refresh" class="btn-small" href="dialog.php?<?= $get_params.$subdir."&".uniqid() ?>"><i class="icon-refresh"></i></a></li>
	
	<li class="pull-right">
	    <div class="btn-group">
		<a class="btn dropdown-toggle sorting-btn" data-toggle="dropdown" href="#">
		  <i class="icon-signal"></i> 
		  <span class="caret"></span>
		</a>
		<ul class="dropdown-menu pull-left sorting">
		    <li class="text-center"><strong><?= lang_Sorting ?></strong></li>
		<li><a class="sorter sort-name <?php if($sort_by=="name"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="name"><?= lang_Filename; ?></a></li>
		<li><a class="sorter sort-date <?php if($sort_by=="date"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="date"><?= lang_Date; ?></a></li>
		<li><a class="sorter sort-size <?php if($sort_by=="size"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="size"><?= lang_Size; ?></a></li>
		<li><a class="sorter sort-extension <?php if($sort_by=="extension"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="extension"><?= lang_Type; ?></a></li>
		</ul>
	      </div>
	</li>
	</ul>
    </div>
    <!-- breadcrumb div end -->
    <div class="row-fluid ff-container">
	<div class="span12">	    
	    <?php if(@opendir($current_path.$rfm_subfolder.$subdir)===FALSE){ ?>
	    <br/>
	    <div class="alert alert-error">There is an error! The upload folder there isn't. Check your config.php file. </div> 
	    <?php }else{ ?>
	    <h4 id="help"><?= lang_Swipe_help; ?></h4>
	    <?php if(isset($folder_message)){ ?>
		<div class="alert alert-block"><?= $folder_message; ?></div>
	    <?php } ?>
	    <?php if($show_sorting_bar){ ?>
	    <!-- sorter -->
	    <div class="sorter-container <?= "list-view".$view; ?>">
		<div class="file-name"><a class="sorter sort-name <?php if($sort_by=="name"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="name"><?= lang_Filename; ?></a></div>
		<div class="file-date"><a class="sorter sort-date <?php if($sort_by=="date"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="date"><?= lang_Date; ?></a></div>
		<div class="file-size"><a class="sorter sort-size <?php if($sort_by=="size"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="size"><?= lang_Size; ?></a></div>
		<div class='img-dimension'><?= lang_Dimension; ?></div>
		<div class='file-extension'><a class="sorter sort-extension <?php if($sort_by=="extension"){ echo ($descending)?"descending":"ascending"; } ?>" href="javascript:void('')" data-sort="extension"><?= lang_Type; ?></a></div>
		<div class='file-operations'><?= lang_Operations; ?></div>
	    </div>
	    <?php } ?>
	    
	    <input type="hidden" id="file_number" value="<?= $n_files; ?>" />
	    <!--ul class="thumbnails ff-items"-->
	    <ul class="grid cs-style-2 <?= "list-view".$view; ?>" id="main-item-container">
		<?php
		$jplayer_ext=array("mp4","flv","webmv","webma","webm","m4a","m4v","ogv","oga","mp3","midi","mid","ogg","wav");
		foreach ($files as $file_array) {
		  $file=$file_array['file'];
			if($file == '.' || (isset($file_array['extension']) && $file_array['extension']!=lang_Type_dir) || ($file == '..' && $subdir == '') || in_array($file, $hidden_folders) || ($filter!='' && $n_files>$file_number_limit_js && $file!=".." && strpos($file,$filter)===false))
			  continue;
			$new_name=fix_filename($file,$transliteration);
			if($file!='..' && $file!=$new_name){
			    //rename
			    rename_folder($current_path.$subdir.$new_name,$new_name,$transliteration);
			    $file=$new_name;
			}
			//add in thumbs folder if not exist 
			if (!file_exists($thumbs_path.$subdir.$file))
				create_folder(false,$thumbs_path.$subdir.$file);
			$class_ext = 3;			
			if($file=='..' && trim($subdir) != '' ){
				$src = explode("/",$subdir);
				unset($src[count($src)-2]);
				$src=implode("/",$src);
		   		if($src=='')
					$src="/";
			}elseif ($file!='..') {
			    $src = $subdir . $file."/";
			}
			?>
			    <li data-name="<?= $file ?>" class="<?php if($file=='..') echo 'back'; else echo 'dir'; ?>" <?php if(($filter!='' && strpos($file,$filter)===false)) echo ' style="display:none;"'; ?>><?php 
			    $file_prevent_rename = false;
			    $file_prevent_delete = false;
			    if (isset($filePermissions[$file])) {
				$file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
				$file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
			    }
			    ?><figure data-name="<?= $file ?>" class="<?php if($file=="..") echo "back-"; ?>directory" data-type="<?php if($file!=".."){ echo "dir"; } ?>">
			    <?php if($file==".."){ ?>
			    	<input type="hidden" class="path" value="<?= str_replace('.','',dirname($rfm_subfolder.$subdir)); ?>"/>
			    	<input type="hidden" class="path_thumb" value="<?= dirname($thumbs_path.$subdir)."/"; ?>"/>
			    <?php } ?>
				  <a class="folder-link" href="dialog.php?<?= $get_params.rawurlencode($src)."&".uniqid() ?>">
					  <div class="img-precontainer">
							<div class="img-container directory"><span></span>
							<img class="directory-img"  src="img/<?= $icon_theme; ?>/folder<?php if($file==".."){ echo "_back"; }?>.png" />
							</div>
					  </div>
				    <div class="img-precontainer-mini directory">
							<div class="img-container-mini">
						    <span></span>
						    <img class="directory-img"  src="img/<?= $icon_theme; ?>/folder<?php if($file==".."){ echo "_back"; }?>.png" />
							</div>
				    </div>
			<?php if($file==".."){ ?>
				    <div class="box no-effect">
					<h4><?= lang_Back ?></h4>
				    </div>
				    </a>
				    
			<?php }else{ ?>
				    </a>
				    <div class="box">
					<h4 class="<?php if($ellipsis_title_after_first_row){ echo "ellipsis"; } ?>"><a class="folder-link" data-file="<?= $file ?>" href="dialog.php?<?= $get_params.rawurlencode($src)."&".uniqid() ?>"><?= $file; ?></a></h4>
				    </div>
				    <input type="hidden" class="name" value="<?= $file_array['file_lcase'];  ?>"/>
				    <input type="hidden" class="date" value="<?= $file_array['date']; ?>"/>
				    <input type="hidden" class="size" value="<?= $file_array['size'];  ?>"/>
				    <input type="hidden" class="extension" value="<?= lang_Type_dir; ?>"/>
				    <div class="file-date"><?= date(lang_Date_type,$file_array['date'])?></div>
				    <?php if($show_folder_size){ ?><div class="file-size"><?= makeSize($file_array['size'])?></div><?php } ?>
				    <div class='file-extension'><?= lang_Type_dir; ?></div>
				    <figcaption>
					    <a href="javascript:void('')" class="tip-left edit-button rename-file-paths <?php if($rename_folders && !$file_prevent_rename) echo "rename-folder"; ?>" title="<?= lang_Rename?>" data-path="<?= $rfm_subfolder.$subdir.$file; ?>" data-thumb="<?= $thumbs_path.$subdir.$file; ?>">
					    <i class="icon-pencil <?php if(!$rename_folders || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>
					    <a href="javascript:void('')" class="tip-left erase-button <?php if($delete_folders && !$file_prevent_delete) echo "delete-folder"; ?>" title="<?= lang_Erase?>" data-confirm="<?= lang_Confirm_Folder_del; ?>" data-path="<?= $rfm_subfolder.$subdir.$file; ?>"  data-thumb="<?= $thumbs_path.$subdir .$file; ?>">
					    <i class="icon-trash <?php if(!$delete_folders || $file_prevent_delete) echo 'icon-white'; ?>"></i>
					    </a>
				    </figcaption>
			<?php } ?>
			    </figure>
			</li>
			<?php
		    }
			
            $files_prevent_duplicate = array();
		    foreach ($files as $nu=>$file_array) {		
			$file=$file_array['file'];
		    
			    if($file == '.' || $file == '..' || is_dir($current_path.$rfm_subfolder.$subdir.$file) || in_array($file, $hidden_files) || !in_array(fix_strtolower($file_array['extension']), $ext) || ($filter!='' && $n_files>$file_number_limit_js && strpos($file,$filter)===false))
				    continue;
			    
			    $file_path=$current_path.$rfm_subfolder.$subdir.$file;
			    //check if file have illegal caracter
			    
			    $filename=substr($file, 0, '-' . (strlen($file_array['extension']) + 1));
			    
			    if($file!=fix_filename($file,$transliteration)){
				$file1=fix_filename($file,$transliteration);
				$file_path1=($current_path.$rfm_subfolder.$subdir.$file1);
				if(file_exists($file_path1)){
				    $i = 1;
				    $info=pathinfo($file1);
				    while(file_exists($current_path.$rfm_subfolder.$subdir.$info['filename'].".[".$i."].".$info['extension'])) {
					    $i++;
				    }
				    $file1=$info['filename'].".[".$i."].".$info['extension'];
				    $file_path1=($current_path.$rfm_subfolder.$subdir.$file1);
				}
				
				$filename=substr($file1, 0, '-' . (strlen($file_array['extension']) + 1));
				rename_file($file_path,fix_filename($filename,$transliteration),$transliteration);
				$file=$file1;
				$file_array['extension']=fix_filename($file_array['extension'],$transliteration);
				$file_path=$file_path1;
			    }
			    
			    $is_img=false;
			    $is_video=false;
			    $is_audio=false;
			    $show_original=false;
			    $show_original_mini=false;
			    $mini_src="";
			    $src_thumb="";
			    $extension_lower=fix_strtolower($file_array['extension']);
			    if(in_array($extension_lower, $ext_img)){
				$src =  $cur_path . rawurlencode($file);
				$mini_src = $src_thumb = $thumbs_path.$subdir. $file;
				//add in thumbs folder if not exist
				if(!file_exists($src_thumb)){
			    try {
			    	if(!create_img($file_path, $src_thumb, 122, 91)){
							$src_thumb=$mini_src="";
						}else{
							new_thumbnails_creation($current_path.$rfm_subfolder.$subdir,$file_path,$file,$current_path,'','','','','','','',$fixed_image_creation,$fixed_path_from_filemanager,$fixed_image_creation_name_to_prepend,$fixed_image_creation_to_append,$fixed_image_creation_width,$fixed_image_creation_height,$fixed_image_creation_option);
						}
			    } catch (Exception $e) {
						$src_thumb=$mini_src="";
			    }
				}
				$is_img=true;
				//check if is smaller than thumb
				list($img_width, $img_height, $img_type, $attr)=getimagesize($file_path);
				if($img_width<122 && $img_height<91){ 
					$src_thumb=$current_path.$rfm_subfolder.$subdir.$file;
					$show_original=true;
				}
				
				if($img_width<45 && $img_height<38){
				    $mini_src=$current_path.$rfm_subfolder.$subdir.$file;
				    $show_original_mini=true;
				}
			    }
			    $is_icon_thumb=false;
			    $is_icon_thumb_mini=false;
			    $no_thumb=false;
			    if($src_thumb==""){
				$no_thumb=true;
				if(file_exists('img/'.$icon_theme.'/'.$extension_lower.".jpg")){
					$src_thumb ='img/'.$icon_theme.'/'.$extension_lower.".jpg";
				}else{
					$src_thumb = "img/".$icon_theme."/default.jpg";
				}
				$is_icon_thumb=true;
			    }
			    if($mini_src==""){
				$is_icon_thumb_mini=false;
			    }
			    
			    $class_ext=0;
			    if (in_array($extension_lower, $ext_video)) {
				    $class_ext = 4;
				    $is_video=true;
			    }elseif (in_array($extension_lower, $ext_img)) {
				    $class_ext = 2;
			    }elseif (in_array($extension_lower, $ext_music)) {
				    $class_ext = 5;
				    $is_audio=true;
			    }elseif (in_array($extension_lower, $ext_misc)) {
				    $class_ext = 3;
			    }else{
				    $class_ext = 1;
			    }
			    if((!($_GET['type']==1 && !$is_img) && !(($_GET['type']==3 && !$is_video) && ($_GET['type']==3 && !$is_audio))) && $class_ext>0){
?>
		    <li class="ff-item-type-<?= $class_ext; ?> file"  data-name="<?= $file; ?>" <?php if(($filter!='' && strpos($file,$filter)===false)) echo ' style="display:none;"'; ?>><?php
		    $file_prevent_rename = false;
		    $file_prevent_delete = false;
		    if (isset($filePermissions[$file])) {
			if (isset($filePermissions[$file]['prevent_duplicate']) && $filePermissions[$file]['prevent_duplicate']) {
			    $files_prevent_duplicate[] = $file;
			}
			$file_prevent_rename = isset($filePermissions[$file]['prevent_rename']) && $filePermissions[$file]['prevent_rename'];
			$file_prevent_delete = isset($filePermissions[$file]['prevent_delete']) && $filePermissions[$file]['prevent_delete'];
		    }
            ?>		<figure data-name="<?= $file ?>" data-type="<?php if($is_img){ echo "img"; }else{ echo "file"; } ?>">
				<a href="javascript:void('')" class="link" data-file="<?= $file; ?>" data-field_id="<?= $field_id; ?>" data-function="<?= $apply; ?>">
				<div class="img-precontainer">
				    <?php if($is_icon_thumb){ ?><div class="filetype"><?= $extension_lower ?></div><?php } ?>
				    <div class="img-container">
					    <span></span>
					    <img class="<?= $show_original ? "original" : "" ?><?= $is_icon_thumb ? " icon" : "" ?><?= $lazy_loading_enabled ? " lazy-loaded" : ""?>" <?= $lazy_loading_enabled ? "data-original" : "src"?>="<?= $src_thumb; ?>">
				    </div>
				</div>
				<div class="img-precontainer-mini <?php if($is_img) echo 'original-thumb' ?>">
				    <div class="filetype <?= $extension_lower ?> <?php if(in_array($extension_lower, $editable_text_file_exts)) echo 'edit-text-file-allowed' ?> <?php if(!$is_icon_thumb){ echo "hide"; }?>"><?= $extension_lower ?></div>
				    <div class="img-container-mini">
					<span></span>
					<?php if($mini_src!=""){ ?>
					<img class="<?= $show_original_mini ? "original" : "" ?><?= $is_icon_thumb_mini ? " icon" : "" ?><?= $lazy_loading_enabled ? " lazy-loaded" : ""?>" <?= $lazy_loading_enabled ? "data-original" : "src"?>="<?= $mini_src; ?>">
					<?php } ?>
				    </div>
				</div>
				<?php if($is_icon_thumb){ ?>
				<div class="cover"></div>
				<?php } ?>
				</a>	
				<div class="box">				
				<h4 class="<?php if($ellipsis_title_after_first_row){ echo "ellipsis"; } ?>"><a href="javascript:void('')" class="link" data-file="<?= $file; ?>" data-field_id="<?= $field_id; ?>" data-function="<?= $apply; ?>">
				<?= $filename; ?></a> </h4>
				</div>
				<input type="hidden" class="date" value="<?= $file_array['date']; ?>"/>
				<input type="hidden" class="size" value="<?= $file_array['size'] ?>"/>
				<input type="hidden" class="extension" value="<?= $extension_lower; ?>"/>
				<input type="hidden" class="name" value="<?= $file_array['file_lcase']; ?>"/>
				<div class="file-date"><?= date(lang_Date_type,$file_array['date'])?></div>
				<div class="file-size"><?= makeSize($file_array['size'])?></div>
				<div class='img-dimension'><?php if($is_img){ echo $img_width."x".$img_height; } ?></div>
				<div class='file-extension'><?= $extension_lower; ?></div>
				<figcaption>
				    <form action="force_download.php" method="post" class="download-form" id="form<?= $nu; ?>">
					<input type="hidden" name="path" value="<?= $rfm_subfolder.$subdir?>"/>
					<input type="hidden" class="name_download" name="name" value="<?= $file?>"/>
					
				    <a title="<?= lang_Download?>" class="tip-right" href="javascript:void('')" onclick="$('#form<?= $nu; ?>').submit();"><i class="icon-download"></i></a>
				    <?php if($is_img && $src_thumb!=""){ ?>
				    <a class="tip-right preview" title="<?= lang_Preview?>" data-url="<?= $src;?>" data-toggle="lightbox" href="#previewLightbox"><i class=" icon-eye-open"></i></a>
				    <?php }elseif(($is_video || $is_audio) && in_array($extension_lower,$jplayer_ext)){ ?>
				    <a class="tip-right modalAV <?php if($is_audio){ echo "audio"; }else{ echo "video"; } ?>"
					title="<?= lang_Preview?>" data-url="ajax_calls.php?action=media_preview&title=<?= $filename; ?>&file=<?= $current_path.$rfm_subfolder.$subdir.$file; ?>"
					href="javascript:void('');" ><i class=" icon-eye-open"></i></a>
						<?php }elseif($preview_text_files && in_array($extension_lower,$previewable_text_file_exts)){ ?>
					    <a class="tip-right file-preview-btn" title="<?= lang_Preview?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=text&title=<?= $filename; ?>&file=<?= $current_path.$rfm_subfolder.$subdir.$file; ?>"
						href="javascript:void('');" ><i class=" icon-eye-open"></i></a>
						<?php }elseif($googledoc_enabled && in_array($extension_lower,$googledoc_file_exts)){ ?>
					    <a class="tip-right file-preview-btn" title="<?= lang_Preview?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=google&title=<?= $filename; ?>&file=<?= $current_path.$rfm_subfolder.$subdir.$file; ?>"
						href="docs.google.com;" ><i class=" icon-eye-open"></i></a>	

						<?php }elseif($viewerjs_enabled && in_array($extension_lower,$viewerjs_file_exts)){ ?>
					    <a class="tip-right file-preview-btn" title="<?= lang_Preview?>" data-url="ajax_calls.php?action=get_file&sub_action=preview&preview_mode=viewerjs&title=<?= $filename; ?>&file=<?= $current_path.$rfm_subfolder.$subdir.$file; ?>"
						href="docs.google.com;" ><i class=" icon-eye-open"></i></a>			    
						
				    <?php }else{ ?>
				    <a class="preview disabled"><i class="icon-eye-open icon-white"></i></a>
				    <?php } ?>
				    <a href="javascript:void('')" class="tip-left edit-button rename-file-paths <?php if($rename_files && !$file_prevent_rename) echo "rename-file"; ?>" title="<?= lang_Rename?>" data-path="<?= $rfm_subfolder.$subdir .$file; ?>" data-thumb="<?= $thumbs_path.$subdir .$file; ?>">
 				    <i class="icon-pencil <?php if(!$rename_files || $file_prevent_rename) echo 'icon-white'; ?>"></i></a>

				    <a href="javascript:void('')" class="tip-left erase-button <?php if($delete_files && !$file_prevent_delete) echo "delete-file"; ?>" title="<?= lang_Erase?>" data-confirm="<?= lang_Confirm_del; ?>" data-path="<?= $rfm_subfolder.$subdir.$file; ?>" data-thumb="<?= $thumbs_path.$subdir .$file; ?>">
 				    <i class="icon-trash <?php if(!$delete_files || $file_prevent_delete) echo 'icon-white'; ?>"></i>
				    </a>
				    </form>
				</figcaption>
			</figure>			
		</li>
			<?php
			}
		    }
		
	?></div>
	    </ul>
	    <?php } ?>
	</div>
    </div>
</div>
<script>
    var files_prevent_duplicate = new Array();
    <?php
    foreach ($files_prevent_duplicate as $key => $value): ?>
        files_prevent_duplicate[<?= $key;?>] = '<?= $value; ?>';
    <?php endforeach; ?>
</script>

    <!-- lightbox div start -->    
    <div id="previewLightbox" class="lightbox hide fade"  tabindex="-1" role="dialog" aria-hidden="true">
	    <div class='lightbox-content'>
		    <img id="full-img" src="">
	    </div>    
    </div>
    <!-- lightbox div end -->

    <!-- loading div start -->  
    <div id="loading_container" style="display:none;">
	    <div id="loading" style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:100000"></div>
	    <img id="loading_animation" src="img/storing_animation.gif" alt="loading" style="z-index:10001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
    </div>
    <!-- loading div end -->
    
    <!-- player div start -->
    <div class="modal hide fade" id="previewAV">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?= lang_Preview; ?></h3>
      </div>
      <div class="modal-body">
      	<div class="row-fluid body-preview">
				</div>
      </div>
      
    </div>
    <!-- player div end -->
    <img id='aviary_img' src='' class="hide"/>

	<script src="lazyload.c.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/modernizr.custom.js"></script>
	<script type="text/javascript" src="js/dropzone.min.js"></script>
	<script type="text/javascript" src="js/jquery.touchSwipe.min.js"></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="jPlayer/jquery.jplayer.min.js"></script>
	<script type="text/javascript" src="js/ZeroClipboard.min.js"></script>
	<?php
	if ($aviary_active){
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) { ?>
			<script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/js/feather.js"></script>
		<?php }else{ ?>
			<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
		<?php }} ?>

	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
	<![endif]-->
	<script src="js/jquery.ui.position.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.10.4.custom.js" type="text/javascript"></script>
	<script src="js/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
	<script src="js/jquery.contextMenu.min.js" type="text/javascript"></script>

	<script>
		var ext_img=new Array('<?= implode("','", $ext_img)?>');
		var allowed_ext=new Array('<?= implode("','", $ext)?>');
		var image_editor=<?= $aviary_active?"true":"false"; ?>;
		//dropzone config
		Dropzone.options.myAwesomeDropzone = {
			dictInvalidFileType: "<?= lang_Error_extension;?>",
			dictFileTooBig: "<?= lang_Error_Upload; ?>",
			dictResponseError: "SERVER ERROR",
			paramName: "file", // The name that will be used to transfer the file
			maxFilesize: <?= $MaxSizeUpload; ?>, // MB
			url: "upload.php",
			accept: function(file, done) {
				var extension=file.name.split('.').pop();
				extension=extension.toLowerCase();
				if ($.inArray(extension, allowed_ext) > -1) {
					done();
				}
				else {
					done("<?= lang_Error_extension;?>");
				}
			}
		};
		if (image_editor) {
			var featherEditor = new Aviary.Feather({
			<?php
                if (empty($aviary_options) || !is_array($aviary_options)) { $aviary_options = array(); }
                // First load any old format options into the array as needed
                $aviary_config_defaults = array(
                    'theme' => 'light',
                    'tools' => 'all'
                );
                if (!empty($aviary_key)) { $aviary_config_defaults['apiKey'] = $aviary_key; }
                if (!empty($aviary_version)) { $aviary_config_defaults['apiVersion'] = $aviary_version; }
                if (!empty($aviary_language)) { $aviary_config_defaults['language'] = $aviary_language; }
                $aviary_config = array_merge($aviary_config_defaults, $aviary_options);
                foreach ($aviary_config as $aopt_key => $aopt_val) {
               ?>
			<?= $aopt_key; ?>: <?= json_encode($aopt_val); ?>,
			<?php } ?>
			onSave: function(imageID, newURL) {
				show_animation();
				var img = document.getElementById(imageID);
				img.src = newURL;
				$.ajax({
					type: "POST",
					url: "ajax_calls.php?action=save_img",
					data: { url: newURL, path:$('#sub_folder').val()+$('#fldr_value').val(), name:$('#aviary_img').data('name') }
				}).done(function( msg ) {
					featherEditor.close();
					d = new Date();
					$("figure[data-name='"+$('#aviary_img').data('name')+"']").find('img').each(function(){
						$(this).attr('src',$(this).attr('src')+"?"+d.getTime());
					});
					$("figure[data-name='"+$('#aviary_img').data('name')+"']").find('figcaption a.preview').each(function(){
						$(this).data('url',$(this).data('url')+"?"+d.getTime());
					});
					hide_animation();
				});
				return false;
			},
			onError: function(errorObj) {
				bootbox.alert(errorObj.message);
			}

		});
		}
	</script>

    <?php if ($lazy_loading_enabled) { /* ?>
        <script src="js/jquery.lazyload.js" type="text/javascript"></script>
        <script src="js/jquery.scrollstop.js" type="text/javascript"></script>

        <script>
        	$(function(){
        		$(".lazy-loaded").lazyload({
				        event: 'scrollstop'
				    });
        	});
        </script>
    <?php */} ?>


	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-lightbox.min.js"></script>

	<script type="text/javascript" src="js/bootstrap-modal.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-modalmanager.min.js"></script>

	<script type="text/javascript" src="js/include.js"></script>

</body>
</html>
