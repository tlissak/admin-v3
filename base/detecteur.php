<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{	
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica",'2roues'=>'etcie123' ) ;
	public static $_LNG 		= array(	
					'image'	=> '<i class="icon-picture"></i> Image',	'marque'=> 'Marque','product'=> 'Produit','store'=>'Magasin','category'=>'Categorie',
					'category_category'=>'Categorie racine','category_slider'=>'Slider','product_image'=>'<i class="icon-picture"></i> Image',
					"cubage"=>"Cubage","image_color"=>"Couleur - Image","product_image_color"=>"Couleur - Image",
					'category_image'=>'<i class="icon-picture"></i> Image','selection'=>'Selections'
					 ,'color'=>'Couleurs','slider'=>'Slider'
					 ,'occassion'=>'Occasion'
					 ,'department_image'=>'Image'
					 ,'department'=>'Categorie'
					 ,'product_video'=>'Video'
					 ,'promos'=>'Promotions'
				) ;
}

Loader('page')->FieldTitle('title')->View(array('id'=>"id",'title'=>'Titre') )
->_Text('tpl','Template',array('required'=>1))
->_Text('title','Titre',array('required'=>1))
->_Rte('content','Desc')
->_Sort('sort','Position'	)
->_Check('valid','valid'	)
->_Html('<fieldset><legend>SEO</legend>')
->_Text('url_alias','Reecriture URL',array('required'=>1))
->_Html('<p>à ne pas modifier ! (sauf de cas de changment $page_name :)</p>')
->_Textarea('meta_title','Meta titre') 
->_Textarea('meta_desc','Meta desc') 
->_Textarea('meta_key','Meta keys')  
->_Html('</fieldset>')
->Load();


/*
Loader('slider')->FieldTitle('title')->View(array('id'=>"id",'title'=>'Titre' , "sort"=>'Position') )
->Show(1)
->_Text('title','Titre',array('required'=>1))
->_Textarea('link','Lien')
->_File("image",'Image')
->_Sort('sort','Position')
->_Check('valid','valide')
->Load();

Loader('promos')->FieldTitle('title')->View(array('id'=>"id",'title'=>'Titre', "sort"=>'Position') )
->_Text('title','Titre',array('required'=>1))
->_Textarea('link','Lien')
->_File("image",'Image')
->_Sort('sort','Position')
->_Check('valid','valide')
->Load();



Loader('product')->FieldTitle('model')->View(array('id'=>"id",'id_department_inner'=>'Category','id_marque_inner'=>'Marque','model'=>'Model','version'=>"Version") )
->Relation( 	array( 'name'=>'marque','tbl'=>'marque'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_marque','fld'=>'title'))
->Relation( 	array( 'name'=>'department','tbl'=>'department'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_department','fld'=>'title'))
//->Relation( 	array( 'name'=>'category','tbl'=>'category'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_category','fld'=>'title'))
->Relation( 	array( 'name'=>'cubage','tbl'=>'cubage'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_cubage','fld'=>'title'))
->Relation(		array( 'name'=>'product_image_color','tbl'=>'image_color','type'=>RelationType::ManyToMany,'by_tbl'=>'product_image_color','left_key'=>'id_image_color','right_key'=>'id_product') ) 
->Relation(		array( 'name'=>'product_image','tbl'=>'image','type'=>RelationType::ManyToMany,'by_tbl'=>'product_image','left_key'=>'id_image','right_key'=>'id_product') ) 
->Relation(		array( 'name'=>'product_video','tbl'=>'video','type'=>RelationType::ManyToMany,'by_tbl'=>'product_video','left_key'=>'id_video','right_key'=>'id_product') ) 
->_Text('model','Model',array('required'=>1))
->_Text('version','Version')
->_Text('url_alias','Recriture URL') //,array('required'=>1)
->_Textarea('meta_desc','Meta desc') 
->_Textarea('meta_key','Meta keys')  
->_Price('price','Prix')
->_Price('price_promo','Prix promo/m')
->_Textarea('small_description','Petite description')
->_Rte('description','Description')
->_Rte('info','Fiche technique')
->_Check('is_new','Afficher comme nouveau ?','checkbox'	)
->_Check('is_test','a l\'essai ?','checkbox'	)
->_Check('is_promo','En promo ?','checkbox'	) 
->_Check('is_permisb','Permis b ?','checkbox'	) 
->_Check('valid','Valide')
->_Sort('sort','Position'	) 
->Load() ;


Loader('occassion')->FieldTitle('title')->View(array('id'=>"id",'id_marque_inner'=>'Marque','model'=>'Model','version'=>"Version") )

->Relation( 	array( 'name'=>'marque','tbl'=>'marque'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_marque','fld'=>'title'))
//->Relation( 	array( 'name'=>'store','tbl'=>'store'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_store','fld'=>'title'))
->Relation( 	array( 'name'=>'cubage','tbl'=>'cubage'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_cubage','fld'=>'title'))
->Relation( 	array( 'name'=>'color','tbl'=>'color'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_color','fld'=>'title'))
->Image('image')
->_Text('title','Titre',array('required'=>1))
->_Text('model','Modèle',array('required'=>1))
->_Text('version','Version',array('required'=>1))
->_Price('km','Kilométrage')
->_Price('price','Prix')
->_Price('year','Année')
->_File('image','Image')
->_Textarea('accessories','Accessoires') 
->_Textarea('garanties','Garanties')  
->_Check('valid','Valide')
->_Sort('sort','Position'	) 
->Load() ;



Loader('marque')->FieldTitle('title')->View(array("id"=>'#' ,'title'=>'Titre','icon'=>'Icon','sort'=>'Position'))
->_Text('title','Titre')->_File('icon','Icon')->_Sort('sort','Position'	)
->Image('icon')
->Load() ;


Loader('category')->FieldTitle('title')->View(array('id'=>'#',"title"=>'Titre'))
//->Relation(	array( 'name'=>'category_slider','tbl'=>'slider','type'=>RelationType::ManyToMany,'by_tbl'=>'category_slider','left_key'=>'id_slider','right_key'=>'id_category') )
->_Text('title','Ttire')
->_Sort('sort','Position'	) 
->_Text('url_alias','Reecriture URL',array('required'=>1))
->_Textarea('meta_desc','Meta desc') 
->_Textarea('meta_key','Meta keys')  
->Show(0)
->Load();

Loader('department')->FieldTitle('title')->View(array('id'=>'#',"title"=>'Titre' , 'id_parent_inner'=>'categorie','sort'=>'Ordre'))
->Relation(		array( 'name'=>'department_image','tbl'=>'image','type'=>RelationType::ManyToMany,'by_tbl'=>'department_image','left_key'=>'id_image','right_key'=>'id_department') ) 

->Relation( 	array( 'name'=>'department','tbl'=>'department'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_parent','fld'=>'title'))
//->_File('cover','Cover')
->_Text('title','Ttire')
->_Rte('description','Description')
->_Sort('sort','Position'	) 
->_Html('<fieldset><legend>SEO</legend>')
->_Text('url_alias','Reecriture URL',array('required'=>1))
->_Textarea('meta_title','Meta titre') 
->_Textarea('meta_desc','Meta desc') 
->_Textarea('meta_key','Meta keys')  
->_Html('</fieldset>')
->Load();

Loader('cubage')->Show(0)->FieldTitle('title')->View(array('title'=>"Cubage"))->_Text('title','Titre')->_Sort('sort','Position'	)->Load() ;	

Loader('image')->Show(0)->FieldTitle('path')->View(array('path'=>"Image"))
->Image('path')
->_File('path','Image (multiple)',array('extends'=>' multiple ')) 
//->_Text('url','') 
->_Sort('sort','Position')->Load() ;	

Loader('video')->Show(0)->FieldTitle('path')->View(array('path'=>"Titre"))
->_File('path','Titre') 
->_Text('url','Url') 
->_Sort('sort','Position')->Load() ;	

Loader('image_color')->Show(0)->FieldTitle('path')
->Relation( 	array( 'name'=>'color','tbl'=>'color'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_color','fld'=>'title'))
->View(array('id_color_inner'=>'Couleur','path'=>"Image"))
->_File('path','Image') 
->_Sort('sort','Position'	)->Load();
		
Loader('color')->Show(0)->FieldTitle('title')->View(array('title'=>'Couleur'))
->_Text('title','Titre')->_Color('code','Couleur')->_Sort('sort','Position'	)->Load();
*/
?>