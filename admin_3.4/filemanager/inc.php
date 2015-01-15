<?php

if (USE_ACCESS_KEYS == TRUE){
    if (!isset($_GET['akey'], $access_keys) || empty($access_keys)){
        die('Access Denied!');
    }

    $_GET['akey'] = strip_tags(preg_replace( "/[^a-zA-Z0-9\._-]/", '', $_GET['akey']));

    if (!in_array($_GET['akey'], $access_keys)){
        die('Access Denied!');
    }
}

$_SESSION['RF']["verify"] = "RESPONSIVEfilemanager";

if(isset($_POST['submit'])){

    include 'upload.php';
    die ;
}

include 'include/utils.php';


if (isset($_GET['fldr'])
    && !empty($_GET['fldr'])
    && strpos($_GET['fldr'],'../') === FALSE
    && strpos($_GET['fldr'],'./') === FALSE)
{
    $subdir = urldecode(trim(strip_tags($_GET['fldr']),"/") ."/");
    $_SESSION['RF']["filter"]='';
}
else { $subdir = ''; }

if($subdir == "")
{
    if(!empty($_COOKIE['last_position'])
        && strpos($_COOKIE['last_position'],'.') === FALSE)
        $subdir= trim($_COOKIE['last_position']);
}
//remember last position
setcookie('last_position',$subdir,time() + (86400 * 7));

if ($subdir == "/") { $subdir = ""; }

// If hidden folders are specified
if(count($hidden_folders)){
    // If hidden folder appears in the path specified in URL parameter "fldr"
    $dirs = explode('/', $subdir);
    foreach($dirs as $dir){
        if($dir !== '' && in_array($dir, $hidden_folders)){
            // Ignore the path
            $subdir = "";
            break;
        }
    }
}

/***
 *SUB-DIR CODE
 ***/

if (!isset($_SESSION['RF']["subfolder"]))
{
    $_SESSION['RF']["subfolder"] = '';
}
$rfm_subfolder = '';

if (!empty($_SESSION['RF']["subfolder"]) && strpos($_SESSION['RF']["subfolder"],'../') === FALSE
    && strpos($_SESSION['RF']["subfolder"],'./') === FALSE && strpos($_SESSION['RF']["subfolder"],"/") !== 0
    && strpos($_SESSION['RF']["subfolder"],'.') === FALSE)
{
    $rfm_subfolder = $_SESSION['RF']['subfolder'];
}

if ($rfm_subfolder != "" && $rfm_subfolder[strlen($rfm_subfolder)-1] != "/") { $rfm_subfolder .= "/"; }

if (!file_exists($current_path.$rfm_subfolder.$subdir))
{
    $subdir = '';
    if (!file_exists($current_path.$rfm_subfolder.$subdir))
    {
        $rfm_subfolder = "";
    }
}

if (trim($rfm_subfolder) == "")
{
    $cur_dir 	 = $upload_dir . $subdir;
    $cur_path 	 = $current_path . $subdir;
    $thumbs_path = $thumbs_base_path;
    $parent 	 = $subdir;
}
else
{
    $cur_dir 	 = $upload_dir . $rfm_subfolder.$subdir;
    $cur_path 	 = $current_path . $rfm_subfolder.$subdir;
    $thumbs_path = $thumbs_base_path. $rfm_subfolder;
    $parent 	 = $rfm_subfolder.$subdir;
}

//if (!file_exists($current_path.$parent."config.php")) die ('No config file');
//require_once $current_path.$parent."config.php";


if (!is_dir($thumbs_path.$subdir))
{
    create_folder(FALSE, $thumbs_path.$subdir);
}

if (isset($_GET['popup']))
{
    $popup = strip_tags($_GET['popup']);
}
else $popup=0;

//Sanitize popup
$popup=!!$popup;

if (isset($_GET['crossdomain']))
{
    $crossdomain = strip_tags($_GET['crossdomain']);
}
else $crossdomain=0;

//Sanitize crossdomain
$crossdomain=!!$crossdomain;

//view type
if(!isset($_SESSION['RF']["view_type"]))
{
    $view = $default_view;
    $_SESSION['RF']["view_type"] = $view;
}

if (isset($_GET['view']))
{
    $view = fix_get_params($_GET['view']);
    $_SESSION['RF']["view_type"] = $view;
}

$view = $_SESSION['RF']["view_type"];

//filter
$filter = "";
if(isset($_SESSION['RF']["filter"]))
{
    $filter = $_SESSION['RF']["filter"];
}

if(isset($_GET["filter"]))
{
    $filter = fix_get_params($_GET["filter"]);
}

if (!isset($_SESSION['RF']['sort_by']))
{
    $_SESSION['RF']['sort_by'] = 'name';
}

if (isset($_GET["sort_by"]))
{
    $sort_by = $_SESSION['RF']['sort_by'] = fix_get_params($_GET["sort_by"]);
}
else $sort_by = $_SESSION['RF']['sort_by'];


if (!isset($_SESSION['RF']['descending']))
{
    $_SESSION['RF']['descending'] = TRUE;
}

if (isset($_GET["descending"]))
{
    $descending = $_SESSION['RF']['descending'] = fix_get_params($_GET["descending"])==="true";
}
else $descending = $_SESSION['RF']['descending'];
$boolarray = Array(false => 'false', true => 'true');

$return_relative_url = isset($_GET['relative_url']) && $_GET['relative_url'] == "1" ? true : false;

// language
if (!isset($_SESSION['RF']['language'])
    || file_exists($_SESSION['RF']['language_file']) === FALSE
    || !is_readable($_SESSION['RF']['language_file']))
{
    $lang = $default_language;

    if (isset($_GET['lang']) && $_GET['lang'] != 'undefined' && $_GET['lang']!='')
    {
        $lang = fix_get_params($_GET['lang']);
        $lang = trim($lang);
    }

    $language_file = 'lang/'.$default_language.'.php';
    if ($lang != $default_language)
    {
        $path_parts = pathinfo($lang);

        if (is_readable('lang/' .$path_parts['basename']. '.php'))
        {
            $language_file = 'lang/' .$path_parts['basename']. '.php';
        }
        else
        {
            echo "<script>console.log('The ".$lang." language file is not readable! Falling back...');</script>";
        }
    }

    // add lang file to session for easy include
    $_SESSION['RF']['language'] = $lang;
    $_SESSION['RF']['language_file'] = $language_file;
}
else
{
    $lang = $_SESSION['RF']['language'];
    $language_file = $_SESSION['RF']['language_file'];
}

require_once $language_file;

if (!isset($_GET['type'])) $_GET['type'] = 0;
if (!isset($_GET['field_id'])) $_GET['field_id'] = '';

$field_id = isset($_GET['field_id']) ? fix_get_params($_GET['field_id']) : '';
$type_param = fix_get_params($_GET['type']);

$get_params = http_build_query(array(
    'type'      => $type_param,
    'lang'      => $lang,
    'popup'     => $popup,
    'crossdomain' => $crossdomain,
    'field_id'  => $field_id,
    'relative_url' => $return_relative_url,
    'akey' 		=> (isset($_GET['akey']) && $_GET['akey'] != '' ? $_GET['akey'] : 'key'),
    'fldr'      => ''
));


/*****************************/

$class_ext = '';
$src = '';

if ($_GET['type']==1) 	 $apply = 'apply_img';
elseif($_GET['type']==2) $apply = 'apply_link';
elseif($_GET['type']==0 && $_GET['field_id']=='') $apply = 'apply_none';
elseif($_GET['type']==3) $apply = 'apply_video';
else $apply = 'apply';

$files = scandir($current_path.$rfm_subfolder.$subdir);
$n_files=count($files);

//php sorting
$sorted=array();
$current_folder=array();
$prev_folder=array();

foreach($files as $k=>$file){
    if($file==".") $current_folder=array('file'=>$file);
    elseif($file=="..") $prev_folder=array('file'=>$file);
    elseif(is_dir($current_path.$rfm_subfolder.$subdir.$file)){
        $date=filemtime($current_path.$rfm_subfolder.$subdir. $file);
        if($show_folder_size){
            $size=foldersize($current_path.$rfm_subfolder.$subdir. $file);
        } else {
            $size=0;
        }
        $file_ext=lang_Type_dir;
        $sorted[$k]=array('file'=>$file,'file_lcase'=>strtolower($file),'date'=>$date,'size'=>$size,'extension'=>$file_ext,'extension_lcase'=>strtolower($file_ext));
    }else{
        $file_path=$current_path.$rfm_subfolder.$subdir.$file;
        $date=filemtime($file_path);
        $size=filesize($file_path);
        $file_ext = substr(strrchr($file,'.'),1);
        $sorted[$k]=array('file'=>$file,'file_lcase'=>strtolower($file),'date'=>$date,'size'=>$size,'extension'=>$file_ext,'extension_lcase'=>strtolower($file_ext));
    }
}

// Should lazy loading be enabled
$lazy_loading_enabled= ($lazy_loading_file_number_threshold == 0 || $lazy_loading_file_number_threshold != -1 && $n_files > $lazy_loading_file_number_threshold) ? true : false;

function filenameSort($x, $y) {
    return $x['file_lcase'] <  $y['file_lcase'];
}
function dateSort($x, $y) {
    return $x['date'] <  $y['date'];
}
function sizeSort($x, $y) {
    return $x['size'] <  $y['size'];
}
function extensionSort($x, $y) {
    return $x['extension_lcase'] <  $y['extension_lcase'];
}

switch($sort_by){
    case 'date':
        usort($sorted, 'dateSort');
        break;
    case 'size':
        usort($sorted, 'sizeSort');
        break;
    case 'extension':
        usort($sorted, 'extensionSort');
        break;
    default:
        usort($sorted, 'filenameSort');
        break;
}

if(!$descending){
    $sorted=array_reverse($sorted);
}

$files=array_merge(array($prev_folder),array($current_folder),$sorted);
?>