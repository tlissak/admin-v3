<?php
/**
 * Created by PhpStorm.
 * User: IR
 * Date: 1/14/2015
 * Time: 12:43 PM
 */

Hook::Add('menu','<li><a href="?sqler=1" ><i class="glyphicon glyphicon-screenshot"></i> <span class="text">SQLer</span></a></li>') ;

if (get('sqler')== '1') {
    Hook::Add('css', '<link href="module/sqler.css" rel="stylesheet" >');
    Hook::Add('js', '<script src="module/sqler.js"></script>');
    Hook::Add('dashboard', '<div id="sqler" class="clearfix">
<div id="sql_editor_controls" class="col-xs-1"><a href="javascript:" class="btn btn-danger">Execute</a> </div>
<div class="col-xs-11" id="sql_editor_outer"><div id="sql_editor">SELECT * FROM category</div></div>
<div id="results" class="col-xs-12"></div>
</div>');
}

Hook::Add('action','sqler_exec') ;

function sqler_exec(){
    if (get('sqler_exec')) {
        $sql = trim(get('sql'));
        $out = array('sql' => $sql);
        if (stripos($sql, 'SELECT') !== 0 && stripos($sql, 'UPDATE') !== 0) {
            $out['error'] = 'QueryNotAllowed';
        } else {
            global $db;
            $out['list'] = Config::$db_loader->fetch($sql);
            $out['error'] = Config::$db_loader->last_error;
        }
        header('Content-type: application/json');
        echo json_encode($out);
        die;
    }
}