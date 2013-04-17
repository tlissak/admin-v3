(function(ui){
	ui.inputValidation = function(){
		this._link = {
			pattern :/([-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?)|^$/gi
			,fix:function(v){ return $.trim(v); }
			,msg: UI.lang.UNVALID_URL
		}
		this._url = {
			pattern : /(^[-a-z0-9\.]+$)|^$/
			,fix:function(v){ return $.trim(v).toLowerCase().replace(/\s/g, '-') ;}
			,msg: UI.lang.UNVALID_LINK
		}
		this._int = {
			pattern : /^[0-9]{0,11}$/
			,fix:function(v){ return $.trim(v).replace(/[^0-9]/g, '') ;}
			,msg: UI.lang.UNVALID_INTEGER
		}
		this._price = {
			pattern : /(^\d*(\.\d*)$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^0-9.]/g, '') ;}
			,msg: UI.lang.UNVALID_FLOAT
		}	
		this._text = {
			pattern : /^(.*)?$/
			,fix:function(v){ return $.trim(v).replace(/^./g, '') ;}
			,msg: UI.lang.UNVALID_TEXT
		}	
		this._zipcode = {
			pattern : /([0-9]{4,5}$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^0-9]/g, '') ;}
			,msg: UI.lang.UNVALID_ZIPCODE
		}
		this._email = {
			pattern : /(^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^a-zA-Z0-9-@.]/g, '') ;}
			,msg: UI.lang.UNVALID_EMAIL
		}	
		this._phone = {
			pattern :/(^[0-9\-\(\)\.]{9,15}$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^0-9\-\(\)\.]/g, '') ;}
			,msg: UI.lang.UNVALID_PHONE
		}
		this._password = {
			pattern :/(^[A-Za-z]\w{7,15}$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^A-Za-z0-9_]/g, '') ;}
			,msg: UI.lang.UNVALID_PASSWORD_PATTERN 
		}
		
		this._date = {
			pattern :/(^\d+[-\/]\d+[-\/]\d+$)|^$/
			,fix:function(v){ return $.trim(v).replace(/[^0-9\/-]/g, '') ;}
			,msg:UI.lang.UNVALID_DATE_FORMAT 
		}
		this._color = {
			pattern :/(^#[0-9a-f]{3}([0-9a-f]{3})?$)|^$/i
			,fix:function(v){ return $.trim(v).replace(/[^A-Fa-f0-9#]/g, '') ;}
			,msg:UI.lang.UNVALID_COLOR_FORMAT 
		}
	}
}(UI)) ;