/**
 * Created by IR on 2/4/2015.
 */

$(document).ready(function(){
    $('.table').on('editable-save.bs.table',function(e,field,row,old_value,el_caller){
        //console.log(e,field,row,value,el_caller) ;
        $.ajax('?sorteditable_exec=1&tbl='+ row._tbl +"&id="+row.id +'&fld='+field+'&value='+row[field] , {success: function (s) {  /**/  }  });
    });
})


