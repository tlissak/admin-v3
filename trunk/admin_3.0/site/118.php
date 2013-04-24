<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{	
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica" ,"jlc"=>"15lmd56") ;
	public static $_LNG 		= array(					
					'pub'=>'Publicité'
					,'guide'=>'Annuaire' ,
					'pubguide'=>'Publicité'
				) ;
}
Loader('pubguide')->FieldTitle('title')->View(array('id'=>"id",'title'=>'Titre','ville'=>'Ville','codepostal'=>"CP") )
->Image('image_horiz' )
->Image('image_vert' )
->Image('image_inner' )
->_Text('title','Nom',array('required'=>1))
->_Textarea('activity','Activité',array('required'=>1))
->_Textarea('activity_kwd','Activité (mot-clès)',array('required'=>1))
->_Textarea('address','Adresse',array('required'=>1))
->_Text('ville','Ville',array('required'=>1))
->_Zipcode('codepostal','CP',array('required'=>1))
->_Phone('tel','Tél',array('required'=>1))
->_Phone('tel2','Tél 2')
->_Phone('fax','Fax')
->_File('image_horiz','Image horiz (999px/60px)')
->_File('image_vert','Image vertical (120px/400px)')
->_File('image_inner','Image inner (400px/80px)')
->_Check('in_homepage','Dans la page daccueil')
->_Check('in_rightside','Dans lespace publicitaire')
->_Check('in_top','Dans la top resultat')
->Load() ;

?>