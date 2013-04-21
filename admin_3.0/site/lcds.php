<? function Loader($x){ 	return new AdminLoader($x);	}
class Ctrl extends  AdminController{	
	public static $_USERS 	= array("admin" =>"admin123!" ,"dan" => "people26" ,	"tlissak" => "metallica" , 	"foxdanni" => "metallica" ,"jlc"=>"15lmd56") ;
	public static $_LANG_PREF = array(
	'body_dir'=>'rtl','ui_js_lang' => 'he'
	) ;
	public static $_LNG 		= array(					
					'backup'=>'גבוי','logout'=>'יציאה','login'=>'כניסה',
					'save'=>'שמירה','new'=>'חדש',	'duplicate'=>'העתק','delete'=>'מחק',
					'browse or drop file here'=>'חפש או גרור קובץ כאן','user name'=>'משתמש','password'=>'ססמא',
					'sort direction A first'=>'סדר מלמעלה למטה','sort direction Z first'=>'סדר מלמעלה למטה','results'=>'תוצאות','of'=>'מתוך',
					'image'	=> '<i class="icon-picture"></i> מדיה',	'marque'=> 'חברה','product'=> 'מוצר','store'=>'לנות','category'=>'קטגוריה',
					'category_category'=>'קטגוריה ראשית','category_slider'=>'מצגת תמונות','product_image'=>'<i class="icon-picture"></i> תמונה',
					"cubage"=>"נפח מנוע","image_color"=>"תמונה -  צבע","product_image_color"=>"תמונה -  צבע",
					'category_image'=>'<i class="icon-picture"></i> תמונה','selection'=>'בחירה'
				) ;
}

Loader('product')->FieldTitle('model')->View(array('id'=>"id",'id_marque_inner'=>'חברה','model'=>'מודל','version'=>"גרסא") )
->Relation( 	array( 'name'=>'marque','tbl'=>'marque'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_marque','fld'=>'title'))
->Relation( 	array( 'name'=>'category','tbl'=>'category'		,'type'=>RelationType::InnerSimple,'left_key'=>'id_category','fld'=>'title'))
->Relation( 	array( 'name'=>'cubage','tbl'=>'cubage'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_cubage','fld'=>'title'))
->Relation(		array( 'name'=>'product_image_color','tbl'=>'image_color','type'=>RelationType::ManyToMany,'by_tbl'=>'product_image_color','left_key'=>'id_image_color','right_key'=>'id_product') ) 
->Relation(		array( 'name'=>'product_image','tbl'=>'image','type'=>RelationType::ManyToMany,'by_tbl'=>'product_image','left_key'=>'id_image','right_key'=>'id_product') ) 
->_Text('model','מודל',array('required'=>1))
->_Text('version','גרסא',array('required'=>1))
->_Url('url_alias','לינק',array('required'=>1))
->_Textarea('meta_desc','מטא תקציר') 
->_Textarea('meta_key','מטא תגים')  
->_Price('price','מחיר')
->_Price('price_promo','מחיר מבצע')
->_Price('price_promo','מחיר תשלומים /חודשי')
->_Textarea('small_description','תקציר')
->_Textarea('description','תיאור')
->_Rte('info','פרטים מלאים')
->_Check('is_new','הצג כחדש ?','checkbox'	)
->_Check('is_test','הצג כנסיון ?','checkbox'	)
->_Check('is_promo','במבצע ?','checkbox'	) 
->_Check('is_permisb','רשיון ב ?','checkbox'	) 
->_Check('valid','מקוון')
->_Sort('sort','דירוג ברשימה'	) 
->Load() ;

Loader('marque')->FieldTitle('title')->View(array("id"=>'#' ,'title'=>'שם','icon'=>'תמונה','sort'=>'דירוג'))
->Image(array('field'=>'icon','path'=>'photos/marque/' ))
->_Text('title','שם')->_File('icon','תמונה')->_Sort('sort','דירוג ברשימה'	)
->Load() ;


Loader('category')->FieldTitle('title')->View(array('id'=>'#',"title"=>'שם'))
->Relation(	array( 'name'=>'category_slider','tbl'=>'slider','type'=>RelationType::ManyToMany,'by_tbl'=>'category_slider','left_key'=>'id_slider','right_key'=>'id_category') )
->_Text('title','שם')
->_Sort('sort','דירוג ברשימה'	) 
->_Url('url_alias','לינק',array('required'=>1))
->_Textarea('meta_desc','מטא תקציר') 
->_Textarea('meta_key','מטא תגים')  
->Load();

Loader('cubage')->Show(0)->FieldTitle('title')->View(array('title'=>"נפח מנוע"))->_Text('title','שם')->_Sort('sort','דירוג ברשימה'	)->Load() ;	

Loader('image')->Show(0)->FieldTitle('path')->View(array('path'=>"Image"))
->Image(array('field'=>'path','path'=>'photos/file/' ) ) //onstandby
->_File('path','תמונה') 
->_Url('url','(או) URL של וידאו') 
->_Sort('sort','דירוג ברשימה'	)->Load() ;	

Loader('slider')->Show(0)->FieldTitle('image')->View(array('image'=>"תמונה"))
->Image(array('field'=>'image','path'=>'photos/slider/' ) ) 
->_File('image','תמונה') 
->_Text('title','תיאור תמונה') 
->_Sort('sort','דירוג ברשימה'	)->Load();

Loader('image_color')->Show(0)->FieldTitle('path')
->Relation( 	array( 'name'=>'color','tbl'=>'marque'			,'type'=>RelationType::InnerSimple,'left_key'=>'id_marque','fld'=>'title'))
->View(array('path'=>"תמונה"))
->_File('path','תמונה') 
->_Sort('sort','דירוג ברשימה'	)->Load();
		
Loader('color')->Show(0)->FieldTitle('title')->View(array('title'=>'צבע'))
->_Text('title','שם')->_Color('code','צבע')->_Sort('sort','דירוג ברשימה'	)->Load();

?>