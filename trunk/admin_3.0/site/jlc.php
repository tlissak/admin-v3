<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{	
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica" ,"jlc"=>"15lmd56") ;
	public static $_LNG 		= array('image'	=> '<i class="icon-picture"></i> Image / Video',	'marque'=> 'Marque','product'=> 'Produit','store'=>'Magasin','category'=>'Categorie',
						'category_category'=>'Categorie parent','product_image'=>'<i class="icon-picture"></i> Image',
						'category_image'=>'<i class="icon-picture"></i> Image','selection'=>'Selection','category_m2o'=>'Test Many 2 One') ;
}

Loader('product')->FieldTitle('model')->View(array('id'=>"id",'id_category_inner'=>"Category",'id_marque_inner'=>'Marque','model'=>'Modele') )
->Relation( 	array( 'name'=>'marque','tbl'=>'marque'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_marque','fld'=>'title'))
->Relation( 	array( 'name'=>'category','tbl'=>'category'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_category','fld'=>'title'))
->Relation(		array( 'name'=>'product_image','tbl'=>'image','type'=>RelationType::ManyToMany,'by_tbl'=>'product_image','left_key'=>'id_image','right_key'=>'id_product') ) 
->_Text('model','Modèle',array('required'=>1))->_Text('version','Version')->_Price('price','Prix')->_Price('price_promo','Prix promotion')
->_Url('url_alias','Url simplifié',array('required'=>1,'pattern'=>'[^a-zA-Z0-9]+')) ->_Textarea('meta_desc','Meta Descriptif') ->_Textarea('meta_key','Meta Keywords')  
->_Textarea('description_fr','Description (Français)')->_Textarea('description_en','Description (Anglais)')
->_Rte('info_en','Informations (Anglais)')->_Textarea('info_fr','Informations (Français)')
->_Text('sort','Ordre d\'affichage'	) ->_Check('valid','En-ligne')
->_Textarea('small_description_en','Petite description (Anglais)')->_Textarea('small_description_fr','Petite description (Français)') 
->_Check('is_new','Nouveau ?','checkbox'	)->_Check('is_promo','En Promo ?','checkbox'	) 
->Load() ;

Loader('marque')->FieldTitle('title_fr')->View(array("id"=>'#' ,'title_fr'=>'Titre','icon'=>'Image','sort'=>'Ordre'))
->Image(array('field'=>'icon','path'=>'photos/marque/' ))
->_Text('title_fr','Titre (Français)')->_Text('title_en','Titre (Anglais)'	) 
->_Textarea('description_fr','Description (Français)')->_Textarea('description_en','Description (Anglais)')
->_File('icon','Icon')->_Sort('sort','Ordre d\'affichage')
->_Url('url_alias','Url simplifié') ->_Textarea('meta_desc','Meta Descriptif') ->_Textarea('meta_key','Meta Keywords') 
->Load() ;

Loader('store')->FieldTitle('title')->View(array("title"=>'Titre'))
->Image(array('field'=>'image','path'=>'photos/store/' ) ) 
->_Text('title','Titre')->_Text('lat','GPS (Latitude)')->_Text('lng','GPS (Longitude)'	)->_Textarea('address','Adresse')
->_Zipcode('zipcode','Code postal'	)->_Text('city','Ville')->_Sort('sort','Ordre d\'affichage'	)->_Textarea('opening_houres','Horaires'	) 
->_Phone('phone','Téléphone')->_Phone('fax','Fax')->_Email('email','Email')->_File('image','Image')
->_Textarea('description_fr','Description (Français)') ->_Textarea('description_en','Description (Anglais)'	) 
->_Url('url_alias','Url simplifié')->_Textarea('meta_desc','Meta Descriptif')->_Textarea('meta_key','Meta Keywords') 
->Load();

Loader('category')->FieldTitle('title_fr')->View(array('id'=>'#',"title_fr"=>'Titre','id_parent_inner'=>'Parent'))
->Relation(	array( 'name'=>'category_category','tbl'=>'category','type'=>RelationType::InnerSimple,'by_tbl'=>'category','left_key'=>'id_parent') )
->Relation(	array( 'name'=>'category_image','tbl'=>'image'		,'type'=>RelationType::ManyToMany,'by_tbl'=>'category_image','left_key'=>'id_image','right_key'=>'id_category') )
->Relation(	array( 'name'=>'category_m2o','tbl'=>'category_m2o'		,'type'=>RelationType::ManyToOne,'by_tbl'=>'category_m2o','left_key'=>'id_category','right_key'=>'id_category') )
->_Text('title_fr','Titre (Français)')->_Text('title_en','Titre (Anglais)')->_Sort('sort','Ordre d\'affichage') 
->_Textarea('description_fr','Description (Français)'	)->_Textarea('description_en','Description (Anglais)'	) 
->_Url('url_alias','Url simplifié') ->_Textarea('meta_desc','Meta Descriptif') ->_Textarea('meta_key','Meta Keywords')
->Load();

Loader("image")->FieldTitle('path')->View(array('path'=>'Image'))
->Show(0)
->Image(array('field'=>'path','path'=>'photos/product/' ) )
->_File('path','Fichier')->_Url('url','(Ou) Liens URL de la vidéo')
->Load();

Loader('category_m2o')->FieldTitle('title')->Show(0)->View(array("id"=>'#' ,'title'=>'Titre','test'=>'TesT'))
->_File('title','Titre')->_Text('test','Test')
->Load() ;

Loader("selection")->FieldTitle('title_fr')->View(array('title_fr'=>'Titre'))
->Relation(	array( 'name'=>'product','tbl'=>'product'	,'type'=>RelationType::ManyToManySelect,'by_tbl'=>'selection_product','left_key'=>'id_product','right_key'=>'id_selection') )
->_Text('title_fr','Titre (Français)'	)->_Text('title_en','Titre (Anglais)') 
->_Textarea('description_fr','Description (Français)')->_Textarea('description_en','Description (Anglais)') 
->_Sort('sort','Ordre d\'affichage'	)->_Check('valid','En-ligne')
->Load();

/*
Loader('page')->Text('name','Nom'	)->Text('title_fr','Titre (Français)')->Text('title_en','Titre (Anglais)') 
->Text('url_alias','Url simplifié') ->Textarea('meta_desc','Meta Descriptif') ->Textarea('meta_key','Meta Keywords') 
->Rte('content_fr','Contenu (Français)')->Rte('content_en','Contenu (Anglais)')
Loader('news')->Text('title_fr','Titre (Français)')->Text('title_en','Titre (Anglais)')->Date('sdate','Date')->File('image','Image')
					->Textarea('description_fr','Description (Français)')->Textarea('description_en','Description (Anglais)')
					->Text('sort','Ordre d\'affichage'	) 
*/

?>