/**
 * Created by IR on 12/24/2014.
 */


//TODO make sure it will not be override when RTE is loaded and input Loaded
function responsive_filemanager_callback(field_id){
    var f = $('#'+field_id);
    var url=f.val();
    if (url.indexOf("/photos/") === 0) {
        f.val( url.replace("/photos/",''))
    } ;
    $('#ModalFileManager').modal('hide');
}

$(function () {
    $('#ModalFileManager').on('hidden.bs.modal', function () {
        $('iframe',this).removeAttr('src');
    }).on('shown.bs.modal', function (e) {
        $('iframe',this).attr('src',$(e.relatedTarget).data("href") );
    })
})


tinymce.init({
    selector: "textarea.rte",
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
        "table contextmenu directionality emoticons paste textcolor responsivefilemanager"
    ],
    relative_urls: false,                //browser_spellcheck : true ,
    filemanager_title:"Responsive Filemanager",
    external_filemanager_path:"http://l/bs_admin/filemanager/",
    external_plugins: { "filemanager" : "http://l/bs_admin/filemanager/plugin.min.js"},
    image_advtab: true,
    toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
    toolbar2: "| responsivefilemanager | image | media | link unlink anchor | print preview code | forecolor backcolor"
});

$(document).ready(function(){

    var tbl = $('.panel-mainlist .table')
        .on('click-row.bs.table', function (e, row, $element) {
            var _params = {
                id : row.id,
                tbl : (new RegExp('[\\?&]tbl=([^&#]*)').exec(window.location.search))[1]
            }
            window.location = '?' + ($.param(_params) ) ;
        })
    var tbl = $('.panel-relationlist .table')
        .on('click-row.bs.table', function (e, row, $element) {


            /*
             TODO : finish this

             $(this).data('selection-type')

             ipt = RenderInput(row.id,$(this).data('left-key-input-name'),row[$(this).data('title-field')])

             check exsit value
             if (exists row.id name in table){
                return
             }else{
                if (checkbox){
                    add ipt
                }elseif (radio){
                    replace value
                }
             }
             */

        })

})

function RenderInput(val,name,title){
    return '<label>\
    <div class="input-group">\
    <div class="input-group-addon"> <input type="radio" checked="" value="'+val+'" name="'+name+'"> </div>\
    <div class="input-group-addon input-group-addon-clean"> '+title+'</div>\
    </div>\
    </label>' ;
}