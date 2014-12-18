<?php

#region INIT



Loader('page','title_fr')
    ->View(array('title_fr'=>"nom") )
    ->FormControl('text','meta_title','Meta titre',array('panel'=>'meta','required'=>1))
    ->FormControl('textarea','meta_desc','Meta desc',array('panel'=>'meta','required'=>1))
    ->FormControl('text','title_fr','Nom FR',array('required'=>1))
    ->FormControl('text','title_en','Nom EN',array('panel'=>'english'))
    ->FormControl('rte','content_fr','Contenu FR')
    ->FormControl('rte','content_en','Contenu EN',array('panel'=>'english','extends'=>' style="height:800px" '))
    ->Attr("title",'CMS')
    ->Panel('english','Contenu anglais','icon ion-compose')
    ->Panel('meta','Meta','icon ion-compose')
    ->Attr('icon',"fa fa-file-o")
;

Loader('product','name_fr')
    ->View(array('name_fr'=>"nom",'model'=>'Model','category_id_inner'=>'categorie','image'=>'Couver.','sort'=>'Ordre') )
    ->Relation( 'marque' , array( 'type'=>'InnerSimple','left_key'=>'marque_id') )
    ->Relation( 'category',	array( 'type'=>'InnerSimple','left_key'=>'category_id'))
    ->FormControl('text','name_fr','Nom')
    ->FormControl('text','model','Model')
    ->FormControl('rte','description_fr','Résumé',array('panel'=>'rte'))
    ->FormControl('rte','description_long','Description',array('panel'=>'rte'))
    ->FormControl('rte','features','Caracteristiques',array('panel'=>'rte'))
    ->FormControl('text','url_alias','Url simplifier' ,array('panel'=>'meta','required'=>1))
    ->FormControl('textarea','keywords','Meta keywords',array('panel'=>'meta'))
    ->Attr('icon','fa fa-sitemap')


    ->Panel('meta','Meta','icon ion-compose')
    ->Panel('rte','Content','icon ion-compose')
    ;

//TODO File upload


Loader('category','title_fr')
    ->View(array('title_fr'=>'Nom','parent_id_inner'=>"parent",'level'=>'niveau') )
    ->Relation(	'category',array('type'=>'InnerSimple','by_tbl'=>'category','left_key'=>'parent_id') )
    ->FormControl('text','title_fr','Nom',array('required'=>1))
    ->FormControl('sort','sort','Sort')
    ->FormControl('number','level','Niveau',array('required'=>1))
    //->Attr('badge','')
    ->Attr('icon','glyphicon glyphicon-th')

;


Loader('marque','title')
    ->View(array('title'=>"Nom",'image'=>'Image') )
    ->FormControl('text','title','Nom')
    ->FormControl('file','image','Image')
    ->FormControl('check','valid','Valide')
    ->FormControl("rte",'content','Contenu') ;


Loader('order','order_date')
    //'client_id_inner'=>"Client",,'status_inner'=>'Status'
    ->View(array('order_date'=>'Date','total'=>"Total") )
   /* ->Relation('country',array('type'=>'InnerSimple','left_key'=>'country_id'))
    ->Relation( 'cart'		,array('type'=>'ManyToOneByKey','left_key'=>'id_guest',"right_key"=>'id_guest','readonly'=>1))
    ->Relation( 'transaction'		,array('type'=>'ManyToOneByKey','left_key'=>'order_id',"right_key"=>'id','readonly'=>1))
    ->Relation( 'client'		,array('type'=>'Simple','left_key'=>'client_id'))
    ->Relation( 'order_status'		,array('type'=>'InnerSimple','left_key'=>'status'))
   */
    ->FormControl('text' ,'order_date','Date de commande',array("readonly"=>1))
    ->FormControl('number' ,'id_guest', 'ID Session',array("readonly"=>1))
    ->FormControl('text' ,'total','Total',array("readonly"=>1))
   // ->_Html('<p><label></label><a href="'. U_BASE .'_vieworder.php" target="_blank" class="btn-red btn-large fac-gen"><i class="icon-invoice"></i> Voir la facture</a></p>')
    ->Attr('sort_name','id')
    ->Attr('sort_order','DESC')
 ;

#endregion
?>