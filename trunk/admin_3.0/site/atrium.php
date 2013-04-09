<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{	
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica" ,"jlc"=>"15lmd56") ;
	public static $_LNG 		= 
	array(
		'image'	=> '<i class="icon-picture"></i> Image / Video'
		,'page'=> 'Pages'
		,'category'=>'Categorie'
		,'page_image'=>'Image') ;
}

Loader('page')
->FieldTitle('title')
->View(array('id'=>"id",'title'=>'Titre','id_category_inner'=>"Categorie") )
->Relation( 	array( 'name'=>'category','tbl'=>'category'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_category','fld'=>'title'))
->Relation( 	array( 'name'=>'page_image','tbl'=>'image'	,'type'=>RelationType::ManyToOne,'left_key'=>'id_page','fld'=>'title'))
->_Text('title','Titre',array('required'=>1))
->_Text('link_title','Text liens',array('required'=>1))
->_Sort('sort','Ordre d\'affichage',array('min'=>1,'max'=>100)) 
->_Check('valid','En-ligne')
->_Check('in_footer','En Pied de page ?','checkbox') 
->_Rte('content','Contenu')
->_Url('url_alias','Url simplifié',array('required'=>1)) 
->_Textarea('meta_desc','Meta Descriptif') 
->_Textarea('meta_key','Meta Keywords')  
->Load() ;

Loader('category')
->FieldTitle('title')->View(array('id'=>'#',"title"=>'Titre'))
->_Text('title','Titre',array('required'=>1))
->_Sort('sort','Ordre d\'affichage') 
->_Color('color','Couleur'	)
->_Check('in_nav','En acces rapide','checkbox') 
->_Url('url_alias','Url simplifié')->_Textarea('meta_desc','Meta Descriptif') ->_Textarea('meta_key','Meta Keywords')
->Load();

Loader("image")->FieldTitle('path')->View(array('path'=>'Image'))
->Show(0)
->Image(array('field'=>'path','path'=>'photos/' ) )
->_File('path','Fichier')
->_Url('url','(Ou) Liens URL de la vidéo')
->Load();

?>