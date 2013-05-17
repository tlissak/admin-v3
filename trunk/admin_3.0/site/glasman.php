<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{
	public static $_SITE = "glasman" ;
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,"yomtov" => "75010" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica" ,"jlc"=>"15lmd56") ;
	public static $_LNG 		= array(	'product'=>'Articles','category'=>'Categorie' 
		, 'cart'=>'Panier','client'=>'Client','country'=>'Zones','marque'=>'Marque'
		,'order'=>'Commande','order_status'=>'Status des commandes','status'=>'Status'
		,'category_category'=>'Categorie parent'
		,'fac_country'=>'Pays de facturation','product_type'=>'XT' , 'language'=>'Traduction' ,'shipping_country'=>'Transporteurs (XT:3)'	) ;
	public static $_PREF = array('ui_js'=>array('site/UI.glasman.js'));
}

Loader('product')->FieldTitle('name_fr')->View(array('id'=>"id",'name_fr'=>"nom",'model'=>'Model','image'=>'Couver.','product_type'=>'XT') )
->Relation( 	array( 'name'=>'category','tbl'=>'category'		,'type'=>RelationType::InnerSimple,'left_key'=>'category_id'))
->Relation(array( 'name'=>'product_type','tbl'=>'product_type'		,'type'=>RelationType::InnerSimple,'left_key'=>'product_type'))
->Image('image')->Image('image2')->Image('image3')
->_Text('name_fr','Nom',array('required'=>1))
->_Text('model','Model' ,array('required'=>1))
->_Text('marque','Marque',array('required'=>1))
->_Check('occasion','Ocassion')
->_Price('price','Prix')
->_Text('status','Etat')
->_Range('delay','delay (j)',array('min'=>1,'max'=>100))
->_File('image','image')->_File('image2','image 2')->_File('image3','image 3')
->_Price('price_orig','Prix d\'achat')
->_Price('weight_kg','Poids')
->_Check('show_price','Afficher le prix')
->_Check('promotion','En promo')
->_Check('occasion','Occasion')
->_Rte('description_fr','Description')
->_Html('<fieldset><legend>SEO</legend>')
->_Textarea('keywords','Meta keywords')
->_Html('</fieldset>')
->Load() ;

Loader('product_type')->FieldTitle('type')->View(array('id'=>"id",'type'=>"Type") )->Show(0)->Load();

Loader('category')->FieldTitle('title_fr')->View(array('id'=>"id",'title_fr'=>'Nom','parent_id_inner'=>"parent",'level'=>'niveau') )
->Relation(	array( 'name'=>'category_category','tbl'=>'category','type'=>RelationType::InnerSimple,'by_tbl'=>'category','left_key'=>'parent_id') )
->_Text('title_fr','Nom',array('required'=>1))
->_Text('sort','Sort')
->_Text('level','Niveau',array('required'=>1))
->Load();

Loader('cart')->FieldTitle('date_time')->View(array('id'=>"id",'Article_id'=>"ID Produit",'Article_id_inner'=>"Produit",'quantity'=>'Quantité','date_time'=>'Date') )
->Show(0)
->Relation( 	array( 'name'=>'product','tbl'=>'product'		,'type'=>RelationType::InnerSimple,'left_key'=>'Article_id'))
->Load() ;

Loader('client')->FieldTitle('email')->View(array('id'=>"id",'email'=>"Email",'last_name'=>'Nom','first_name'=>'prénom') )
->Relation( 	array( 'name'=>'country','tbl'=>'country'		,'type'=>RelationType::InnerSimple,'left_key'=>'country_id'))
->Relation( 	array( 'name'=>'fac_country','tbl'=>'country'		,'type'=>RelationType::InnerSimple,'left_key'=>'fac_country_id'))
->_Email('email','Email')
->_Text('pass','Mot de passe',array('readonly'=>1))
->_Check('valid','Valid')
->_Text('gender','Civilité')
->_Text('last_name','Nom')
->_Text('first_name','Prénom')
->_Text('company','Société')
->_Text('address','Adresse')
->_Zipcode('zipcode','CP')
->_Text('ville','Ville')
->_Text('country','Pays')
->_Text('phone','Tél')
->_Text('cell','Port.')
->_Text('fax','Fax')
->_Html('<fieldset><legend>Info Facturation</legend>')
->_Text('fac_gender','Civilité')
->_Text('fac_first_name','Prénom')
->_Text('fac_last_name','Nom')
->_Text('fac_company','Société')
->_Text('fac_address','Adresse')
->_Zipcode('fac_zipcode','CP')
->_Text('fac_ville','Ville')
->_Text('fac_country','Pays')
->_Text('fac_phone','Tél')
->_Text('fac_cell','Port.')
->_Text('fac_fax','Fax')
->_Html('</fieldset>')
->Load() ;

Loader('marque')->FieldTitle('title')->View(array('id'=>"id",'title'=>"Nom",'image'=>'Image') )
->Image('image')
->_Text('title','Nom')
->_File('image','Image')
->_Check('valid','Valide')
->Load() ;

Loader('order')->FieldTitle('order_date')->View(array('id'=>"id",'client_id_inner'=>"Client",'order_date'=>'Date','total'=>"Total",'status_inner'=>'Status') )
->Relation( 	array( 'name'=>'country','tbl'=>'country'		,'type'=>RelationType::InnerSimple,'left_key'=>'country_id'))
->Relation( 	array( 'name'=>'cart','tbl'=>'cart'		,'type'=>RelationType::ManyToOneByKey,'left_key'=>'visitor_id',"right_key"=>'visitor_id'))
->Relation( 	array( 'name'=>'client','tbl'=>'client'		,'type'=>RelationType::Simple,'left_key'=>'client_id'))
->Relation( 	array( 'name'=>'status','tbl'=>'order_status'		,'type'=>RelationType::InnerSimple,'left_key'=>'status'))
->_Text('order_date','Date de commande',array("readonly"=>1))
->_Text('visitor_id', 'ID Session',array("readonly"=>1)) 
->_Text('total','Total',array("readonly"=>1))
->_Html('<a href="'. U_BASE .'_facture.php" target="_blank" class="btn fac-gen"><i class="icon-invoice"></i> Voir la facture</a>')
->Load() ;

Loader('order_status')->FieldTitle('title')->View(array('id'=>"id",'title'=>"Status") )
->_Text('title','Status')
->Load() ;

Loader('country')->FieldTitle('name_fr')->View(array('id'=>"id",'name_fr'=>"Nom",'tva'=>'TVA') )
->Relation( 	array( 'name'=>'shipping_country','tbl'=>'product'		,'type'=>RelationType::ManyToMany,'by_tbl'=>'shipping_country','left_key'=>'Shipping_id',"right_key"=>'Country_id'))
->_Text('name_fr','Nom')
->_Text('tva','TVA')
->_Text('sort','Ordre')
->Load() ;

Loader('language')->FieldTitle('title')->View(array('id'=>"id",'title'=>"Nom",'en'=>'En','fr'=>'Fr') )
->_Text('title','Nom',array('readonly'=>1))
->_Textarea('fr','Francais',array('required'=>1))
->_Textarea('en','Anglais',array('required'=>1))
->Load();

/*
client
Country_id == country_id country

ALTER TABLE `category`
CHANGE COLUMN `Parent_id` `parent_id`  int(11) NULL DEFAULT NULL AFTER `id`;

ALTER TABLE `product`
CHANGE COLUMN `ID` `id`  int(11) NOT NULL AUTO_INCREMENT FIRST ,
CHANGE COLUMN `Marque` `marque`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `image3`,
CHANGE COLUMN `Delay` `delay`  int(11) NULL DEFAULT NULL AFTER `online`,
CHANGE COLUMN `Category_id` `category_id`  int(11) NULL DEFAULT NULL AFTER `description_cn`;

ALTER TABLE `marque`
CHANGE COLUMN `ID` `id`  int(11) NOT NULL AUTO_INCREMENT FIRST ;


DELETE * FROM cart WHERE visitor_id NOT IN(SELECT visitor_id FROM `order` );

*/
?>