<?php

#region INIT

Loader('page','title_fr')
    ->View(array('title_fr'=>"nom") )
    ->AddFormField('text','meta_title','Meta titre',array('required'=>1))
    ->AddFormField('textarea','meta_desc','Meta desc',array('required'=>1))
    ->AddFormField('text','title_fr','Nom FR',array('required'=>1))
    ->AddFormField('text','title_en','Nom EN')
    ->AddFormField('rte','content_fr','Contenu FR')
    ->AddFormField('rte','content_en','Contenu EN') ;


Loader('product','name_fr')
    ->View(array('name_fr'=>"nom",'model'=>'Model','category_id_inner'=>'categorie','image'=>'Couver.','sort'=>'Ordre') )
    ->Relation( 'marque' , array( 'type'=>'InnerSimple','left_key'=>'marque_id') )
    ->Relation( 'category',	array( 'type'=>'InnerSimple','left_key'=>'category_id'))
    ->AddFormField('rte','description_fr','Résumé')
    ->AddFormField('rte','description_long','Description')
    ->AddFormField('rte','features','Caracteristiques')
    ->AddFormField('text','url_alias','Url simplifier' ,array('required'=>1)) //->_Html('<span style="color:red;font-weight:bold"> url fini par .html ! ATTENTION au changement!!!!</span>')
    ->AddFormField('textarea','keywords','Meta keywords') ;


Loader('category','title_fr')
    ->View(array('title_fr'=>'Nom','parent_id_inner'=>"parent",'level'=>'niveau') )
    ->Relation(	'category',array('type'=>'InnerSimple','by_tbl'=>'category','left_key'=>'parent_id') )
    ->AddFormField('text','title_fr','Nom',array('required'=>1))
    ->AddFormField('sort','sort','Sort')
    ->AddFormField('number','level','Niveau',array('required'=>1)) ;


Loader('marque','title')
    ->View(array('title'=>"Nom",'image'=>'Image') )
    ->AddFormField('text','title','Nom')
    ->AddFormField('file','image','Image')
    ->AddFormField('check','valid','Valide')
    ->AddFormField("rte",'content','Contenu') ;

#endregion
?>