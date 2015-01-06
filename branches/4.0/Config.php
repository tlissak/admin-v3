<?php
#region Page
Loader('page','title_fr')
    ->View(array('title_fr'=>"nom") )
    ->FormControl('text','meta_title','Meta titre',array('panel'=>'meta','required'=>1))
    ->FormControl('textarea','meta_desc','Meta desc',array('panel'=>'meta','required'=>1))
    ->FormControl('text','title_fr','Nom FR',array('required'=>1))
    ->FormControl('text','title_en','Nom EN',array('panel'=>'english'))
    ->FormControl('rte','content_fr','Contenu FR',array('extends'=>' style="height:800px" '))
    ->FormControl('rte','content_en','Contenu EN',array('panel'=>'english'))
    ->Attr("title",'CMS')
    ->Panel('meta','Meta','icon ion-compose')
    ->Panel('english','Contenu anglais','icon ion-compose')
    ->Attr('icon',"fa fa-file-o")
;
#endregion

#region Product
Loader('product','name_fr')
    ->View(array('name_fr'=>"nom",'model'=>'Model','category_id_inner'=>'categorie') ) /*,'image'=>'Couver.','sort'=>'Ordre'*/
    ->Relation( 'marque' , array( 'type'=>'InnerSimple','left_key'=>'marque_id') )
    ->Relation( 'category',	array( 'type'=>'InnerSimple','left_key'=>'category_id'))
    ->Relation('images',array('type'=>'ManyToMany','by_tbl'=>'product_image','left_key'=>'id_image',"right_key"=>'id_product'))
    ->Relation("video",array( 'type'=>"ManyToManySelect",'by_tbl'=>'product_video' ,'left_key'=>'id_video',"right_key"=>'id_product'))
    ->Relation("file",array('type'=>'ManyToManySelect','by_tbl'=>'product_file','left_key'=>'id_file',"right_key"=>'id_product'))
    ->FormControl('text','name_fr','Nom')
    ->FormControl('text','model','Model')
    ->FormControl('rte','description_fr','Résumé',array('panel'=>'rte'))
    ->FormControl('rte','description_long','Description',array('panel'=>'rte'))
    ->FormControl('rte','features','Caracteristiques',array('panel'=>'rte'))
    ->FormControl('text','url_alias','Url simplifier' ,array('panel'=>'meta')) //,'required'=>1
    ->FormControl('textarea','keywords','Meta keywords',array('panel'=>'meta'))
    ->Attr('icon','fa fa-sitemap')
    ->Panel('meta','Meta','icon ion-compose')
    ->Panel('rte','Content','icon ion-compose')
    ->Attr('table_height',1350)
    ->Attr('table_count',28)
    ;
#endregion

#region Category
Loader('category','title_fr')
    ->View(array('title_fr'=>'Nom','parent_id_inner'=>"parent",'level'=>'niveau') )
    ->Relation(	'category',array('type'=>'InnerSimple','by_tbl'=>'category','left_key'=>'parent_id') )
    ->FormControl('text','title_fr','Nom',array('required'=>1))
    ->FormControl('sort','sort','Sort')
    ->FormControl('number','level','Niveau',array('required'=>1))
    //->Attr('badge','')

    ->Attr('icon','glyphicon glyphicon-th')

;
#endregion

#region Marque
Loader('marque','title')
    ->View(array('title'=>"Nom",'image'=>'Image') )
    ->FormControl('text','title','Nom')
    ->FormControl('file','image','Image')
    ->FormControl('check','valid','Valide')
    ->FormControl("rte",'content','Contenu')
    ->Attr('icon','fa fa-book');
#endregion

#region Order
Loader('order','order_date')->View(array('order_date'=>'Date','total'=>"Total",'client_id_inner'=>"Client",'status_inner'=>'Status') )
    ->Relation('country',array('type'=>'InnerSimple','left_key'=>'country_id'))
    ->Relation( 'cart'		,array('type'=>'ManyToOneByKey','by_tbl'=>'cart','left_key'=>'id_guest',"right_key"=>'id_guest','readonly'=>1))
    ->Relation( 'transaction'		,array('type'=>'ManyToOneByKey','by_tbl'=>"transaction",'left_key'=>'order_id',"right_key"=>'id','readonly'=>1))
    ->Relation( 'client'		,array('type'=>'InnerSimple','left_key'=>'client_id'))
    ->Relation( 'order_status'		,array('type'=>'InnerSimple','left_key'=>'status'))
    ->FormControl('text' ,'order_date','Date de commande',array("readonly"=>1))
    ->FormControl('number' ,'id_guest', 'ID Session',array("readonly"=>1))
    ->FormControl('text' ,'total','Total',array("readonly"=>1))
    ->FormControl('html' ,'<a href="'. U_BASE .'_vieworder.php" target="_blank" class="btn btn-danger fac-gen-1"><i class="icon-invoice"></i> Voir la facture</a>'
        ,'Facture',array('panel'=>'controls'))
    ->FormControl('html' ,'<a href="'. U_BASE .'_vieworder.php" target="_blank" class="btn btn-danger fac-gen-2"><i class="icon-invoice"></i> Voir la facture</a>'
        ,'Facture Clean',array('panel'=>'controls'))
    ->Attr('sort_name','id')
    ->Attr('sort_order','DESC')
    ->Attr('icon','fa fa-flag-checkered')

    ->Panel('controls','Controls Print','icon ion-compose')
 ;
#endregion

Loader('product_type','type')   ->View(array('type'=>"Type") )    ->Attr('Hide',1);
Loader('images','path')->View(array('id'=>"id",'path'=>"Image") )  ->FormControl('file','path','Image')->Attr('Hide',1)->Attr('icon','fa fa-camera');
Loader('file','path')->View(array('id'=>"id",'path'=>"Fichier") )->FormControl('file','path','Fichier')->FormControl('text','title','Titre')->Attr('Hide',1)->Attr('icon','fa fa-file');
Loader('video','path')->View(array('id'=>"id",'path'=>"Url Video") )->FormControl('text','path','Url Video')->Attr('Hide',1)->Attr('icon','fa fa-video-camera');

#region Client
Loader('client','email')->View(array('id'=>"id",'email'=>"Email",'last_name'=>'Nom','first_name'=>'prénom') )
    ->Relation( 'country',	array( 'type'=>'InnerSimple','left_key'=>'country_id'))
    ->Relation( 'country',	array( 'type'=>'InnerSimple','left_key'=>'fac_country_id'))
    ->FormControl('email','email','Email')
    ->FormControl('password','pass','Mot de passe',array('readonly'=>1))
    ->FormControl('check','valid','Valid')
    ->FormControl('text','gender','Civilité')
    ->FormControl('text','last_name','Nom')
    ->FormControl('text','first_name','Prénom')
    ->FormControl('text','company','Société')
    ->FormControl('text','address','Adresse')
    ->FormControl('zipcode','zipcode','CP')
    ->FormControl('text','ville','Ville')
    ->FormControl('text','country','Pays')
    ->FormControl('phone','phone','Tél')
    ->FormControl('phone','cell','Port.')
    ->FormControl('phone','fax','Fax')
    ->FormControl('text','fac_gender','Civilité',array('panel'=>'facture'))
    ->FormControl('text','fac_first_name','Prénom',array('panel'=>'facture'))
    ->FormControl('text','fac_last_name','Nom',array('panel'=>'facture'))
    ->FormControl('text','fac_company','Société',array('panel'=>'facture'))
    ->FormControl('text','fac_address','Adresse',array('panel'=>'facture'))
    ->FormControl('zipcode','fac_zipcode','CP',array('panel'=>'facture'))
    ->FormControl('text','fac_ville','Ville',array('panel'=>'facture'))
    ->FormControl('text','fac_country','Pays',array('panel'=>'facture'))
    ->FormControl('phone','fac_phone','Tél',array('panel'=>'facture'))
    ->FormControl('phone','fac_cell','Port.',array('panel'=>'facture'))
    ->FormControl('phone','fac_fax','Fax',array('panel'=>'facture'))
    ->Panel('facture','Facturation','')
->Attr('icon','fa fa-user');
#endregion

#region Country
Loader('country','name_fr')
    ->View(array('id'=>"id",'name_fr'=>"Nom",'tva'=>'TVA') )
    ->Relation( 'product',array('type'=>'ManyToMany','by_tbl'=>'shipping_country','left_key'=>'Shipping_id',"right_key"=>'Country_id'))
    ->FormControl('text','name_fr','Nom')
    ->FormControl('text','tva','TVA')
    ->FormControl('text','sort','Ordre')
->Attr('icon','fa fa-globe');
#endregion

#region Cart
Loader('cart','concat_ws(\' \',\'product\',id_product,\'x\',quantity)')
    ->Relation('product', 	array( 'type'=>'InnerSimple','left_key'=>'id_product'))
    ->View(array('id'=>"id",'id_product'=>"ID Produit",'id_product_inner'=>"Produit",'quantity'=>'Quantité','date_time'=>'Date') )
    ->FormControl('number','quantity','Qty.')
    ->Attr("Hide",1)
    ->Attr('icon','shopping-cart')
    ;
#endregion

#region Transaction
Loader('transaction','concat_ws(\' \',response_code,capture_mode)')
    ->View(array('id'=>"id",'response_code'=>'response_code','transaction_id'=>"transaction n",'amount'=>'amount') )
    ->FormControl('text','datetime','datetime',array("readonly"=>1))
    ->FormControl('text','order_id','order_id',array("readonly"=>1))
    ->FormControl('text','return_type','return_type',array("readonly"=>1))
    ->FormControl('text','merchant_id','merchant_id',array("readonly"=>1))
    ->FormControl('text','merchant_country','merchant_country',array("readonly"=>1))
    ->FormControl('text','amount','amount',array("readonly"=>1))
    ->FormControl('text','transaction_id','transaction_id',array("readonly"=>1))
    ->FormControl('text','payment_means','payment_means',array("readonly"=>1))
    ->FormControl('text','transmission_date','transmission_date',array("readonly"=>1))
    ->FormControl('text','payment_time','payment_time',array("readonly"=>1))
    ->FormControl('text','payment_date','payment_date',array("readonly"=>1))
    ->FormControl('text','response_code','response_code',array("readonly"=>1))
    ->FormControl('text','payment_certificate','payment_certificate',array("readonly"=>1))
    ->FormControl('text','authorisation_id','authorisation_id',array("readonly"=>1))
    ->FormControl('text','currency_code','currency_code',array("readonly"=>1))
    ->FormControl('text','card_number','card_number',array("readonly"=>1))
    ->FormControl('text','cvv_flag','cvv_flag',array("readonly"=>1))
    ->FormControl('text','cvv_response_code','cvv_response_code',array("readonly"=>1))
    ->FormControl('text','bank_response_code','bank_response_code',array("readonly"=>1))
    ->FormControl('text','complementary_code','complementary_code',array("readonly"=>1))
    ->FormControl('text','return_context','return_context',array("readonly"=>1))
    ->FormControl('text','unk','unk',array("readonly"=>1))
    ->FormControl('text','caddie','caddie',array("readonly"=>1))
    ->FormControl('text','receipt_complement','receipt_complement',array("readonly"=>1))
    ->FormControl('text','merchant_language','merchant_language',array("readonly"=>1))
    ->FormControl('text','language','language',array("readonly"=>1))
    ->FormControl('text','customer_id','customer_id',array("readonly"=>1))
    ->FormControl('text','customer_email','customer_email',array("readonly"=>1))
    ->FormControl('text','customer_ip_address','customer_ip_address',array("readonly"=>1))
    ->FormControl('text','capture_day','capture_day',array("readonly"=>1))
    ->FormControl('text','capture_mode','capture_mode',array("readonly"=>1))
    ->FormControl('textarea','last_line','last_line',array("readonly"=>1))
    ->FormControl('textarea','data','data',array("readonly"=>1))
    ->Attr('Hide',1)
->Attr('icon','fa fa-credit-card')
;
#endregion

Loader('order_status','title') ->View(array('id'=>"id",'title'=>"Status")) ->FormControl('text','title','Status') ->Attr('title','Status') ->Attr('Hide',1);


Hook::Add('js','<script src="hook.js"></script>') ;
?>