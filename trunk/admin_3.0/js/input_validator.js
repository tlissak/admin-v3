function input_validation(){
	this._link = {
		pattern :/([-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?)|^$/gi
		,fix:function(v){ return $.trim(v); }
		,msg: 'Url mal formé'
	}
	this._url = {
		pattern : /(^[-a-z\.]+$)|^$/
		,fix:function(v){ return $.trim(v).toLowerCase().replace(/\s/g, '-') ;}
		,msg: 'Lien mal formé'
	}
	this._int = {
		pattern : /^[0-9]{0,11}$/
		,fix:function(v){ return $.trim(v).replace(/[^0-9]/g, '') ;}
		,msg: 'Valeur int éroné '
	}
	this._price = {
		pattern : /(^\d*(\.\d*)$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^0-9.]/g, '') ;}
		,msg: 'Valeur float éroné '
	}	
	this._text = {
		pattern : /^(.*)?$/
		,fix:function(v){ return $.trim(v).replace(/^./g, '') ;}
		,msg: 'Valeur text éroné '
	}	
	this._zipcode = {
		pattern : /([0-9]{4,5}$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^0-9]/g, '') ;}
		,msg: 'Valeur float éroné '
	}
	this._email = {
		pattern : /(^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^a-zA-Z0-9-@.]/g, '') ;}
		,msg: 'Valeur email éroné '
	}	
	this._phone = {
		pattern :/(^[0-9\-\(\)\.]{9,15}$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^0-9\-\(\)\.]/g, '') ;}
		,msg: 'Valeur float éroné '
	}
	this._password = {
		pattern :/(^[A-Za-z]\w{7,15}$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^A-Za-z0-9_]/g, '') ;}
		,msg: '7 to 16 ( characters, digits, underscore ) and the 1 character must be a letter ' 
	}
	
	this._date = {
		pattern :/(^\d+[-\/]\d+[-\/]\d+$)|^$/
		,fix:function(v){ return $.trim(v).replace(/[^0-9\/-]/g, '') ;}
		,msg: ' date non valide 00-00-0000 ou 00/00/0000 ' 
	}
	this._color = {
		pattern :/(^#[0-9a-f]{3}([0-9a-f]{3})?$)|^$/i
		,fix:function(v){ return $.trim(v).replace(/[^A-Fa-f0-9#]/g, '') ;}
		,msg: 'Couleur format #AAA ou #A1C1F9 ' 
	}
}