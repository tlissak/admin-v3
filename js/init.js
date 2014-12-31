/**
 * Created by IR on 12/24/2014.
 */


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

$(function(){
    $("textarea.rte").tinymce({
        script_url: 'tinymce/tinymce.gzip.php',  //  selector: "textarea.rte",
        menubar: false,    toolbar_items_size: 'small',
        plugins: [  "advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking save table contextmenu directionality emoticons paste textcolor colorpicker"    //responsivefilemanager
        ],
        relative_urls: false,                //browser_spellcheck : true ,
        filemanager_title:"Responsive Filemanager",  external_filemanager_path:"filemanager/",
        external_plugins: { "filemanager" : "../filemanager/plugin.min.js"}, image_advtab: true,
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | cut copy paste removeformat searchreplace | bullist numlist outdent indent | styleselect fontsizeselect  | image media link unlink anchor | fullscreen preview code charmap visualchars visualblocks | forecolor backcolor | table | hr ltr rtl"   //responsivefilemanager
    });
})

$(window).on("beforeunload",function(){
    if (window.changed){
        return "Document not save ! are you sure ? ";
    }
})

$(document).ready(function(){


    $('.panel-mainlist .table').on('click-row.bs.table', function (e, row, $element) {
        var _params = {
            id : row.id,
            tbl : (new RegExp('[\\?&]tbl=([^&#]*)').exec(window.location.search))[1]
        }
        window.location = '?' + ($.param(_params) ) ;
    })



    $('form.main-form').on("submit",function() {
        $.ajax($(this).attr('action') ,{type:'POST'
            ,data:$(this).serialize(),success:function(s){
                window.changed = false ;
                json_data = $.parseJSON(s) ;
                $('#message').show().find(".alert-block").html(json_data.message)
            } }) ;
        return false;
    })

    $('#modal form').on("submit",function() {

        var context = $(this).data('context') ;

        $.ajax($(this).attr('action') ,{type:'POST'
            ,data:$(this).serialize(),success:function(s){
                json_data = $.parseJSON(s) ;
                $('#message').show().find(".alert-block").html(json_data.message)

                if (json_data.status < 400 ){
                    window.changed = true ;
                    State.Add(context,json_data.row);
                    $("#modal").modal('hide') ;
                    context.bootstrapTable("refresh")
                }
            } }) ;
        return false;
    })

    $("#modal").on('show.bs.modal', function (e) {
        $("form",this)
            .data('context',$(e.relatedTarget).closest('.tab-pane').find('.panel-relationlist .table[data-left-key]') )
            .attr("action",$(e.relatedTarget).data('href') + "&action=add&set_form_ajax=1");

        $.ajax($(e.relatedTarget).data('href'),{context:this,success:function(s){
            $(".modal-body",this).html(s);
            $('.table',this).bootstrapTable();
        }})

    })


    $('.panel-relationlist .table')
    .on('load-success.bs.table',function(e){
        State.Select(this) ;
    }).on('check.bs.table',  function (e, row, $element) {
        State.Add(this,row) ;
    }).on('uncheck.bs.table',  function (e, row, $element) {
        State.Del(this, row);
    })

})

var State ={
    Select:function(el_list_relation)  {
        var states = $(el_list_relation).closest('.tab-pane').find(".state-cont").parent() ;
        var state_value = states.find('input');
        var rows_input = $(el_list_relation).find("input[name='_id']") ;
        var selected = [];
        rows_input.each(function(){
            var that = $(this) ;
            var found = 0 ;
            for(var i=0;i<state_value.size();i++){
                if (state_value.eq(i).val() == that.val()){
                    found = 1 ;
                }
            }
            if (found)
                that.prop('checked',true) ;
        })
    }
    ,Del : function(el_list_relation,row){
        var states = $(el_list_relation).closest('.tab-pane').find(".state-cont").parent() ;
        var state_value = states.find('input')
        for (var i = 0; i< state_value.size() ; i++ ){
            if (state_value.eq(i).val() == row.id){
                state_value.eq(i).closest('.input-group').find('.close').trigger('click');
            }
        }
    }
    ,Add : function(el_list_relation,row){

            if (row.id === '' ){
                console.log("Error : Lookup on Postback->Set() ; :  $out['row']['id'] = is not set ;");

            }
 //           console.log("adding item to state",el_list_relation,row) ;

            var states = $(el_list_relation).closest('.tab-pane').find(".state-cont").parent() ;
            var states_cont = states.find('.state-cont') ;
            var state_value = states.find('input')
            var state_tpl = states.find('script[type="text/template"]').text();

            var d = $(el_list_relation).data();



//           console.log("elements of state ",state_tpl,state_value,states);
//           console.log("elements data types",d.selectionType,d.leftKey,d.titleField)



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
                itm = State.Template(state_tpl,vars) ;
                states_cont.html(itm) ;
            }

            if (d.selectionType == 'CHECKBOX' ) {
                vars = {
                    value : row.id ,
                    title : row[d.titleField] ,
                    left_key : d.leftKey +  '[]'
                    //right_key :
                }
                itm = State.Template(state_tpl,vars) ;
                states_cont.append(itm) ;
            }

            return false ;
    }
    ,Template : function (tpl,vars){
        for(el in vars) {
            regex = RegExp('\\{\\$'+el+'\\}',"g") ;// console.log(regex,vars[el]) ;
            tpl = tpl.replace(regex,vars[el]) ;
        }
        return tpl
    }


}
