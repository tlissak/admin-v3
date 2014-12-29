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



//tinymce.init
//
$(function(){
$("textarea.rte").tinymce({
    script_url: 'tinymce/tinymce.gzip.php',
  //  selector: "textarea.rte",
    menubar: false,
    toolbar_items_size: 'small',
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking save table contextmenu directionality emoticons paste textcolor colorpicker"
        //responsivefilemanager
    ],
    relative_urls: false,                //browser_spellcheck : true ,
    filemanager_title:"Responsive Filemanager",
    external_filemanager_path:"filemanager/",
    external_plugins: { "filemanager" : "../filemanager/plugin.min.js"},
    image_advtab: true,

    toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | cut copy paste removeformat searchreplace | bullist numlist outdent indent | styleselect fontsizeselect  | image media link unlink anchor | fullscreen preview code charmap visualchars visualblocks | forecolor backcolor | table | hr ltr rtl"
    //responsivefilemanager
});
})
$(document).ready(function(){

    $('form.main-form').on("submit",function() {
        $.ajax($(this).attr('action') ,{type:'POST'
            ,data:$(this).serialize(),success:function(a){
                $("#alert").modal('show').find(".modal-body").html(a)
            } }) ;
        return false;
    })

    $('#modal form').on("submit",function() {
        $.ajax($(this).attr('action') ,{type:'POST'
            ,data:$(this).serialize(),success:function(a){
                $("#alert").modal('show').find(".modal-body").html(a)
            } }) ;
        return false;
    })

    $("#modal").on('show.bs.modal', function (e) {
        //if (!data) return e.preventDefault() // stops modal from being shown
       // console.log(data) ;
        $("form",this).attr("action",$(e.relatedTarget).data('href') + "&action=add&set_form_ajax=1");
        $.ajax($(e.relatedTarget).data('href'),{context:this,success:function(s){
            $(".modal-body",this).html(s);
            $('.table',this).bootstrapTable();
        }})

    })

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

            var states = $(this).closest('.panel-relationlist').prev() ;
            var states_cont = states.find('.state-cont') ;
            var state_value = states.find('input')
            var state_tpl = states.find('script[type="text/template"]').text();

            var d = $(this).data();

            //console.log(state_tpl,state_value,states);
            //console.log(d.selectionType,d.leftKey,d.titleField)



            for (var i = 0; i< state_value.size() ; i++ ){

                if (state_value.eq(i).val() == row.id){
                    return false ;
                }
            }


            if (d.selectionType == 'RADIO' ) {
                vars = {
                    value : row.id ,
                    title : row[d.titleField] ,
                    left_key : d.leftKey
                }

                console.log(state_tpl) ;
                itm = Template(state_tpl,vars) ;
                console.log(itm) ;

                states_cont.html(itm) ;
            }

            if (d.selectionType == 'CHECKBOX' ) {
                vars = {
                    value : row.id ,
                    title : row[d.titleField] ,
                    left_key : d.leftKey +  '[]'
                    //right_key :
                }

                console.log(state_tpl) ;
                itm = Template(state_tpl,vars) ;
                console.log(itm) ;

                states_cont.append(itm) ;
             }

            return false ;
        })

})

function Template(tpl,vars){
    for(el in vars) {
        regex = RegExp('\\{\\$'+el+'\\}',"g") ;
        console.log(regex,vars[el]) ;
        tpl = tpl.replace(regex,vars[el]) ;
    }
    return tpl
}