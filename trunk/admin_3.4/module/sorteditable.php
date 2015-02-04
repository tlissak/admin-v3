<?
Hook::Add('css', '<link href="bs/bootstrap-editable.css" rel="stylesheet" >');
Hook::Add('js', '<script src="bs/bootstrap-editable.min.js"></script>');
Hook::Add('js', '<script src="bs/bootstrap-table-editable.min.js"></script>');
Hook::Add('js', '<script src="module/sorteditable.js"></script>');



Hook::Add('action','sorteditable_exec') ;

function sorteditable_exec(){
    if (get('sorteditable_exec')) {
        $out = array();

        $sql = 'UPDATE ' . get('tbl') .' SET ' . get('fld') . ' = ' . get('value') .' WHERE id = '. get('id') ;
        $out['sql'] = $sql;

        $out['ok'] = Config::$db_loader->query($sql);
        $out['error'] = Config::$db_loader->last_error;

        header('Content-type: application/json');
        echo json_encode($out);
        die;

    }
}

?>