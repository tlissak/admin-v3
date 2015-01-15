/**
 * Created by IR on 12/24/2014.
 */

jQuery.deparam = function (querystring) {
    querystring = querystring.substring(querystring.indexOf('?')+1).split('&');
    var params = {}, pair, d = decodeURIComponent, i;
    for (i = querystring.length; i > 0;) {
        pair = querystring[--i].split('=');
        params[d(pair[0])] = d(pair[1]);
    }
    return params;
};

$(function() {
    $(document).on('click','a[data-confirm]' ,function(ev) {
        var href = $(this).attr('href');
        if (!$('#dataConfirmModal').length) {
            $('body').append('<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="dataConfirmLabel">Merci de confirmer</h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Non</button><a class="btn btn-danger" id="dataConfirmOK">Oui</a></div></div></div></div>');
        }
        $('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
        $('#dataConfirmOK').attr('href', href);
        $('#dataConfirmModal').modal({show:true});
        return false;
    });
});

function responsive_filemanager_callback(field_id){
    var f = $('#'+field_id);
    var url=f.val();
    if (url.indexOf("/photos/") === 0) {
        f.val( url.replace("/photos/",''))
    } ;
    //TODO : Add image preview
    $('#ModalFileManager').modal('hide');
}

$(function () {
    $('#ModalFileManager').on('hidden.bs.modal', function () {
        $('iframe',this).removeAttr('src');
    }).on('shown.bs.modal', function (e) {
        var _href =  $(e.relatedTarget).data("href")
        var curr_val = $(e.relatedTarget).next().val() ;
        if (curr_val){
            if (curr_val.indexOf('/')>0){
                _href += '&fldr=' + curr_val.substr(0,curr_val.lastIndexOf('/')) ;
            }
        }
        console.log(_href) ;
        $('iframe',this).attr('src',_href );
    })
})

$(function(){
    $("textarea.rte").tinymce({
        script_url: 'tinymce/tinymce.gzip.php',  //  selector: "textarea.rte",
        menubar: false,    toolbar_items_size: 'small',
        plugins: [  "advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking save table contextmenu directionality emoticons paste textcolor colorpicker"    //responsivefilemanager
        ],
        relative_urls: false,                //browser_spellcheck : true ,
        filemanager_title:"Responsive Filemanager",  external_filemanager_path:"filemanager/","filemanager_access_key":"7B6YhaP5en6B6lcxD5l3Bg" ,
        external_plugins: { "filemanager" : "../filemanager/plugin.min.js"}, image_advtab: true,
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | cut copy paste removeformat searchreplace | bullist numlist outdent indent | styleselect fontsizeselect  | image media link unlink anchor | fullscreen preview code charmap visualchars visualblocks | forecolor backcolor | table | hr ltr rtl"   //responsivefilemanager
    });
})

$(window).on("beforeunload",function(){
    if (window.changed){
        return "Document modified ";
    }
})

function mainFormater(value,row){
    return '<a href="?tbl='+ row._tbl +'&id='+row.id+'">' + value + '</a>';
}
function relationFormater(value,row){
    return ([
        '<a class="edit" data-action="mod" data-target="#modal"  data-toggle="modal" '
        ,'data-href="?tbl='+row._tbl+'&amp;ajax=form&id='+row.id+'"  href="#tbl='+row._tbl+'&amp;ajax=form&id='+row.id+'" title="Edit">',
        '<i class="glyphicon glyphicon-edit"></i></a>',
        ' &nbsp; ',
        '<a class="remove" data-confirm="Etes-vous certain de vouloir supprimer?" '
        ,' href="?tbl='+row._tbl+'&action=del&id='+row.id+'&set_form_ajax=1&relation=1" >',
        '<i class="glyphicon glyphicon-remove"></i></a>'
    ].join('')) ;
}
$(document).ready(function(){

    //$.fn.datepicker.defaults.format = "dd/mm/yy";
    $('.date_picker').parent().datetimepicker({format:'dd/MM/yyyy'});//
    $('.color_picker').colorpicker();

    var touchScreen = 'ontouchstart' in window /* works on most browsers */ || 'onmsgesturechange' in window; /* works on ie10*/;
    if (touchScreen) $("body").addClass('touch') ;
    $( window ).scroll(function() {
        if (window.scroll_timeout) clearTimeout (scroll_timeout);
        window.scroll_timeout = setTimeout(function(){
            $(document.body).toggleClass( 'scrolled', $(this).scrollTop() > 100 );
        },100) ;
    }).trigger("scroll");

    $(document).on('click','#dataConfirmOK',function(e) {
        e.preventDefault();
        $('#dataConfirmModal').modal('hide');
        $.ajax($(this).attr("href"),{success:function(s){
            Callback.Postback(s) ;
        }})
    })

    //Act readonly as disabled becouse disabled dosent serialize()
    $(document).on('click','input[readonly]', function (e) {  e.preventDefault();  })

    $(document).on('click','.state-cont .close', function(e) {
        window.changed = true;
    });

    $('form.main-form').on("submit",function() {
        $.ajax($(this).attr('action') ,{type:'POST',data:$(this).serialize(),success:function(s){
            Callback.Mainform(s);
        }}) ;
        return false;
    })


    $('#modal form').on("submit",function() {
        var context = $(this).data('context') ;
        $.ajax($(this).attr('action') ,{type:'POST' ,data:$(this).serialize(),success:function(s){
                Callback.Relation(s,context) ;
            } }) ;
        return false;
    })

    $("#modal").on('show.bs.modal', function (e) {
        $("form", this).data('context', $(e.relatedTarget).closest('.tab-pane').find('.panel-relationlist .table[data-left-key]'))
            .attr("action", $(e.relatedTarget).data('href') + "&action="+ $(e.relatedTarget).data('action') +"&set_form_ajax=1");
        $.ajax($(e.relatedTarget).data('href'), {
            context: this, success: function (s) {
                $(".modal-body", this).html(s);
                $('.table', this).bootstrapTable();
                //TODO: Load rte
            }
        })
    })


    $('.panel-relationlist .table').on('load-success.bs.table',function(e){
        State.Select(this) ;
    }).on('check.bs.table',  function (e, row, $element) {
        State.Add(this,row) ;
    }).on('uncheck.bs.table',  function (e, row, $element) {
        State.Del(this, row);
    })

    if ($('.main-form').size()) {
        Callback.Init($.deparam($('.main-form').attr('action')))
    }
})


var Callback = {
    ChangeAttribute:function(elm,atr,keys,obj){
        //use $.extend ?
        var n_obj = $.deparam(elm.attr(atr));
        for(var i = 0;i<keys.length;i++){
            n_obj[keys[i]] = obj[keys[i]] ;
        }
        elm.attr(atr, '?'+$.param(n_obj)) ;
    }
    ,Init : function(o){
        //Uodates :
        // * Controls & Href
        // * Mainform Action
        // * Breadcrumbs Text

        if (o.id > 0 ) {
            $("li[data-controller]").show();
            this.ChangeAttribute($("li[data-controller='del'] a"),'href',['id'],o);
            this.ChangeAttribute($("li[data-controller='dup'] a"),'href',['id'],o);
            this.ChangeAttribute($(".main-form"),'action',['id','action'], $.extend(o,{action:'mod'}));

            $('#breadcrumb .active').text("Edit #"+ o.id);

            $('.panel-mainlist .table').bootstrapTable("refresh") ;
        }else{
            $("li[data-controller]").hide();
            this.ChangeAttribute($(".main-form"),'action',['id','action'], $.extend(o,{action:'add'}));
            $('#breadcrumb .active').text("Add");
            //TODO : reset form !
        }
    }
    ,Message : function(cls,msg) {
        $('html, body').animate({scrollTop:0},500);
        $("#message").html('<div class="alert alert-'+cls+'" ><a href="#" class="close btn btn-default" data-dismiss="alert">&times;</a>' +
        '<div class="collapse in">'+msg+'</div>' +
        '</div>' );//prepend
    }
    ,GetJson : function(s){
        try {
            return $.parseJSON(s);
        }catch (e){
            return false ;
        }
    }
    ,Postback : function(s) {
        window.changed = false ;
        o = this.GetJson(s)
        if (! o){
            this.Message('danger',s) ;
            return;
        }
        if (o.status < 300) {
            if (o.action == 'del'){
                this.Message('info','Object deleted successfuly') ;
            }
            if (o.action == 'dup'){
                this.Message('success','Object duplicated successfuly') ;
            }
            this.Init(o) ;
        } else {
            this.Message('warning', 'Object changed with error #' + o.status + ' details : ' + o.message) ;
        }
    }
    ,Mainform : function(s){
        window.changed = false ;
        o = this.GetJson(s)
        if (! o){
            this.Message('danger',s) ;
            return;
        }
        if (o.status < 300) {
            if (o.action == 'mod'){
                this.Message('info','Object modified successfuly') ;
            }
            if (o.action == 'add'){
                this.Message('success','Object add successfuly') ;
            }
            this.Init(o) ;
        } else {
            this.Message('warning', 'Object saved with error #' + o.status + ' details : ' + o.message) ;
        }
    }
    ,Relation : function(s,context){
        o = this.GetJson(s)
        if (! o){
            this.Message('danger',s) ;
            return;
        }
        window.changed = true ;

        if (o.status < 300) {
            if (o.action == 'mod'){
                this.Message('info','Relation Object modified successfuly') ;
                State.Add(context,o.row);
            }
            if (o.action == 'add'){
                this.Message('success','Relation Object add successfuly') ;
                State.Add(context,o.row);
            }
            $("#modal").modal('hide') ;
            context.bootstrapTable("refresh") ;
        } else {
            this.Message('warning', 'Relation object saved with error #' + o.status + ' details : ' + o.message) ;
        }
    }
}


var State ={
    Select:function(el_list_relation)  {
        var states = $(el_list_relation).closest('.tab-pane').find(".state-cont").parent() ;
        var state_value = states.find('input');
        var rows_input = $(el_list_relation).find("tr input") ;
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
                window.changed = true ;
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

            window.changed = true ;

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
