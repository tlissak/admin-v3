<?php

#region INIT

Loader('page','title_fr')
    ->View(array('title_fr'=>"nom") )
    ->FormControl('text','meta_title','Meta titre',array('required'=>1))
    ->FormControl('textarea','meta_desc','Meta desc',array('required'=>1))
    ->FormControl('text','title_fr','Nom FR',array('required'=>1))
    ->FormControl('text','title_en','Nom EN')
    ->FormControl('rte','content_fr','Contenu FR')
    ->FormControl('rte','content_en','Contenu EN')
    ->Attr("title",'CMS')
;


Loader('product','name_fr')
    ->View(array('name_fr'=>"nom",'model'=>'Model','category_id_inner'=>'categorie','image'=>'Couver.','sort'=>'Ordre') )
    ->Relation( 'marque' , array( 'type'=>'InnerSimple','left_key'=>'marque_id') )
    ->Relation( 'category',	array( 'type'=>'InnerSimple','left_key'=>'category_id'))
    ->FormControl('rte','description_fr','Résumé')
    ->FormControl('rte','description_long','Description')
    ->FormControl('rte','features','Caracteristiques')
    ->FormControl('text','url_alias','Url simplifier' ,array('required'=>1))
    ->FormControl('textarea','keywords','Meta keywords')
    ->Attr('icon','link')


    ;

//TODO Add gorup with title and id
//TODO Form control option can be relayd to this group
//TODO RTE height
//TODO File upload


Loader('category','title_fr')
    ->View(array('title_fr'=>'Nom','parent_id_inner'=>"parent",'level'=>'niveau') )
    ->Relation(	'category',array('type'=>'InnerSimple','by_tbl'=>'category','left_key'=>'parent_id') )
    ->FormControl('text','title_fr','Nom',array('required'=>1))
    ->FormControl('sort','sort','Sort')
    ->FormControl('number','level','Niveau',array('required'=>1))
    ->Attr('badge','-');


Loader('marque','title')
    ->View(array('title'=>"Nom",'image'=>'Image') )
    ->FormControl('text','title','Nom')
    ->FormControl('file','image','Image')
    ->FormControl('check','valid','Valide')
    ->FormControl("rte",'content','Contenu') ;

#endregion
?>