$(function(){
    $.getScript("http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js", function(data, textStatus, jqxhr) {
        if ( ! window._sql_editor){
            window._sql_editor = ace.edit("sql_editor");
            window._sql_editor.getSession().setMode("ace/mode/sql");
        }
    });

    $('#sql_editor_controls .btn-danger').click(function () {
        $.ajax('?sqler_exec=1&sql='+ (window._sql_editor.getSession().getValue()),{success: function(o) {  callback_execute(o);   }  });
    })

    function callback_execute(s){
        try {
            var o = typeof s == 'object' ? s : $.parseJSON(o);
        }catch (e){
            var o = s ;
        }
        if ( typeof o == 'string'){
            Callback.Message('danger',o) ;
            $('#results').html("") ;
            return;
        }
        if (o.error){
            Callback.Message('warning', o.error + ' '+ o.sql) ;
        }
        if (o.list.length == 0 ) {
            $('#results').html("") ;
            return;
        }

        opts = [
            [ 'page-list' , '[5, 10, 20, 50, 100, 200]' ]
            , ['pagination', 'true']
            , [ 'page-size' , 10 ]
            , [ 'cache' , 'false' ]
            , [ 'classes' , 'table table-condensed' ]
            , [ 'show_export' , 'true' ]
        ];

        tbl = '<table class="table"'  ;
        for (var i=0; i <  opts.length ; i++){
            tbl += ' data-'+opts[i][0] +'="'+opts[i][1]+'" '  ;
        }
        tbl += '><thead><tr>' ;
        var kys = [] ;
        for(var k in o.list[0]){
            kys.push(k) ;
            tbl += '<th data-sortable="true">'+k+'</th>';
        }
        tbl += "</tr></thead><tbody>"  ;
        for(var i=0;i< o.list.length;i++){
            tbl +="<tr>" ;
            for (var j=0;j < kys.length ; j++){
                tbl += '<td><code>'+o.list[i][kys[j]]+'</code></td>';
            }
            tbl +="</tr>" ;
        }
        tbl += "</tbody></table>"  ;

        $('#results').html(tbl);
        $('#results .table').bootstrapTable() ;
    }
})