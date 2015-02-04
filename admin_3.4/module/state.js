
!function($) {

    //$.removeAllStorages() ;

    'use strict';

    $.extend($.fn.bootstrapTable.defaults, {
        state: true,
        onStateInit: function () {return false;},
        onStateSave: function (field, row, oldValue, $el) {return false;}
    });

    $.extend($.fn.bootstrapTable.Constructor.EVENTS, {
        'state-init.bs.table': 'onStateInit',
        'state-save.bs.table': 'onStateSave'
    });

    var BootstrapTable = $.fn.bootstrapTable.Constructor,
        _initTable = BootstrapTable.prototype.initTable,
        _initBody = BootstrapTable.prototype.initBody;


    /**************/
    //   Search in plugin !
    /*************/

    var _initServer = BootstrapTable.prototype.initServer;
    BootstrapTable.prototype.initServer = function (silent, query) {
        var searchIn = [] ;
        for(var i in this.options.columns) {
            if (this.options.columns[i].visible && this.options.columns[i].field != 'id') {
                searchIn.push(this.options.columns[i].field) ;
            }
        }
        _initServer.apply(this,  [false , {'searchin':searchIn }] ) ;
    }

    /*********************/
    //  State localstorage plugin
    /*********************/

    BootstrapTable.prototype.initTable = function () {
        _initTable.apply(this, Array.prototype.slice.apply(arguments));
        if (!this.options.state) {            return;        }
        var ns_storage=$.initNamespaceStorage('ns_table_state');
        var storage = ns_storage.sessionStorage;
        if (storage.isSet('table-options',this.options.context)) {
            var opts = storage.get('table-options',this.options.context);
           // console.log('Load state', opts);
            getSetOpt(opts,this)  ;
        }
        this.trigger('state-init');
    };

    function getSetOpt(o,s) {
        if (!s) var s = {options:{ columns:{} }};
        if (o.searchText)              {
            s.searchText = o.searchText;
            if (s.$container){
                s.$container.on('load-success.bs.table',function(){
                    $(this).find(".search input").val(s.searchText) ;
                })
            }
        }

        if ($.type(o.options.columns ) == 'object'){
            for (var i in o.options.columns){
                s.options.columns[i].visible = o.options.columns[i].visible ;
            }
        }else if($.type(o.options.columns ) == 'array'){
            for (var i=0;i < o.options.columns.length;i++){
                s.options.columns[i]  = {
                    visible: o.options.columns[i].visible
                };
            }
        }

        if (o.options.pageNumber)       s.options.pageNumber = o.options.pageNumber
        if (o.options.pageSize)         s.options.pageSize = o.options.pageSize
        if (o.options.sortName)         s.options.sortName = o.options.sortName
        if (o.options.sortOrder)        s.options.sortOrder = o.options.sortOrder
        if (o.options.pageNumber)       s.options.pageNumber = o.options.pageNumber
        if (o.options.pageNumber)       s.options.pageNumber = o.options.pageNumber

        return s ;
    }

    function c(s){         console.log($.extend({},s)) ;    }

    BootstrapTable.prototype.initBody = function () {
        _initBody.apply(this, Array.prototype.slice.apply(arguments));
        if (!this.options.state) {  return;   }
        var ns_storage=$.initNamespaceStorage('ns_table_state');
        var storage = ns_storage.sessionStorage;
        storage.set('table-options',this.options.context,getSetOpt(this)) ;
        //console.log('Save state !',this,this.options);
        this.trigger('state-save');
    };

}(jQuery);