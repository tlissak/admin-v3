//source : https://ga-dev-tools.appspot.com/embed-api/

(function(){
    var panels = [
        {
            html  : '<div class="alert"><div id="embed-api-auth-container" class="alert"></div></div><div class="alert" id="view-selector-container"></div>'
            ,id : 0  ,cls : 'col-md-2' ,title:'Config'
        }
        , {
            html : '<div id="chart-1-container"></div>'
            ,id : '1' ,cls : 'col-md-4' ,title : 'Pays 90j'
        }
       , {
            html : '<div id="chart-2-container"></div>'
            ,id : '2' ,cls : 'col-md-6' ,title : 'Sessions 30j'
        }
        , {
            html : '<div id="chart-3-container"></div>'
            ,id : '3' ,cls : 'col-md-6'  ,title : 'pageviews 30j'
        }
        , {
            html : '<div id="chart-4-container"></div>'
            ,id : '4'  ,cls : 'col-md-6' ,title : 'visitors 90j'
        }
        ,{
            html : '<div id="chart-5-container"></div>'
            ,id : '5' ,cls : 'col-md-12' ,title : 'visitors 180j'
        },
        {
            html : '<div id="chart-6-container"></div>'
            ,id : '6' ,cls : 'col-md-12'  ,title : 'Source'
        }
    ] ;

    for (var i=0 ; i< panels.length;i++){
        var p = panels[i] ;

        $('.ga-dash').append('<div class="panel-compact panel-gadash '+ p.cls+'">\
        <div class="panel panel-default"><div class="panel-heading" >\
        <a class="btn pull-right"  href="javascript:void(0)" data-toggle="collapse" data-target="#panel-gadash-'+ p.id
        +'" aria-expanded="true"  ><i class="icon ion-ios-arrow-up"></i></a>\
        <h3 class="panel-title" data-toggle="collapse" data-target="#panel-gadash-'+ p.id +'"\
        aria-expanded="true" ><i class="fa fa-google"></i> Google Analytics - '+ p.title
        +'</h3> </div> <div class="panel-body collapse in" id="panel-gadash-'+ p.id+'">'+ p.html +' </div></div></div>') ;

    }

})() ;

(function(w,d,s,g,js,fs){
    g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
    js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
    js.src='https://apis.google.com/js/platform.js';
    fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
}(window,document,'script'));

gapi.analytics.ready(function() {

    gapi.analytics.auth.authorize({
        container: 'embed-api-auth-container',
        //from : https://console.developers.google.com/project/290171304204/apiui/credential?authuser=0
        //290171304204-aq4f7d1mahkhntlvsdem1c94miqomrnt.apps.googleusercontent.com
        // query editor :  https://ga-dev-tools.appspot.com/explorer/
        /* for best keywords : ga:searchKeyword  , ga:sessions , -ga:sessions on -yearAgo*/
        clientid: '921646434115-4ub1v8m4rmo1qn4odstbjhpcuev4qoms.apps.googleusercontent.com'
    });
    var viewSelector = new gapi.analytics.ViewSelector({container: 'view-selector-container'});
    viewSelector.execute();

    var dataChart1 = new gapi.analytics.googleCharts.DataChart({
        query: {metrics: 'ga:sessions',  dimensions: 'ga:country','start-date': '90daysAgo','end-date': 'yesterday','max-results': 8, sort: '-ga:sessions'},
        chart: {container: 'chart-1-container',type: 'PIE',options: {width: '100%'}}
    });

    var dataChart4 = new gapi.analytics.googleCharts.DataChart({
        query: {metrics: 'ga:sessions',dimensions: 'ga:date','start-date': '30daysAgo','end-date': 'yesterday'},
        chart: {container: 'chart-2-container',type: 'LINE',options: {width: '100%'}}
    });
    var dataChart2 = new gapi.analytics.googleCharts.DataChart({
        query: {metrics: 'ga:pageviews',dimensions: 'ga:date','start-date': '30daysAgo','end-date': 'yesterday'},
        chart: {container: 'chart-3-container',type: 'LINE',options: {width: '100%'}}
    });
    var dataChart3 = new gapi.analytics.googleCharts.DataChart({
        query: {  metrics: 'ga:visitors', dimensions: 'ga:date',  'start-date': '90daysAgo', 'end-date': 'yesterday'  },
        chart: {  container: 'chart-4-container',  type: 'LINE', options: {  width: '100%' } }
    });
    var dataChart5 = new gapi.analytics.googleCharts.DataChart({    //add comparation with another period
        query: {metrics: 'ga:sessions',dimensions: 'ga:date','start-date': '180daysAgo','end-date': 'yesterday'},
        chart: {container: 'chart-5-container',type: 'LINE',options: {width: '100%'}}
    });
    var dataChart6 = new gapi.analytics.googleCharts.DataChart({/* Search engings */
        query: {'dimensions': 'ga:source','metrics': 'ga:organicSearches','sort': '-ga:organicSearches','max-results': '10'},
        chart: {type: 'TABLE',container: 'chart-6-container',options: {width: '100%'}}
    });

    viewSelector.on('change', function(ids) {
        dataChart1.set({query: {ids: ids}}).execute();
        dataChart2.set({query: {ids: ids}}).execute();
        dataChart3.set({query: {ids: ids}}).execute();
        dataChart4.set({query: {ids: ids}}).execute();
        dataChart5.set({query: {ids: ids}}).execute();
        dataChart6.set({query: {ids: ids}}).execute();
    });

});