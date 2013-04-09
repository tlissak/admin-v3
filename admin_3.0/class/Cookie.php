<?php
/*From @prestashop */
class		Cookie{	
	private $_content;
	private $_name;
	private $_expire;
	private $_path ;
	private $_domain ;
	public $crypte_data ;
	protected $_cipherTool;
	public $newsession = false;
	private $_iv = null;	
	protected $_modified = false;	
	function __construct($name,$path="",$expire=NULL)	{
		$this->_content = array();
		$this->_expire = isset($expire) ? (int)($expire) : (time() + 1728000);
		$this->_name = md5($name);	
		$this->_path = trim(U_ROOT.$path, '/\\').'/';
		if ($this->_path{0} != '/') $this->_path = '/'.$this->_path;
		$this->_path = rawurlencode($this->_path);
		$this->_path = str_replace('%2F', '/', $this->_path);
		$this->_path = str_replace('%7E', '~', $this->_path);		
		$this->_domain = $this->getDomain();
		if (_CIPHER_ALGORITHM_){
			$this->_iv = _RIJNDAEL_IV_ ;
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		}else{
			$this->_iv = _BLOWFISH_IV_ ;
			$this->_cipherTool = new Blowfish(_BLOWFISH_KEY_, _BLOWFISH_IV_);
		}
		$this->update();
		if ( ! $this->__isset('_session_id') ){
			$this->__set('_session_id',  $this->getRandomKey() ) ;
			$this->newsession = true;
		}
	}
	private function getDomain(){
		$r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
	    preg_match ($r, U_HOST, $out);
		if (preg_match('/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)'. 
         '{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)'. 
         '{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/', $out[4]))
			return  U_HOST;
		if (!strstr(U_HOST, '.')) 
			return  U_HOST ;
		$domain = $out[4];
		return $domain;
	}
	public function __destruct(){	$this->write();	}
	function getRandomKey($length=20){
		for ($i=0,$out=''; $i<$length; $i++) {	$d=rand(1,30)%2; $out .= $d ? chr(rand(65,90)) : chr(rand(48,57));	} 
		return $out ;
	}
	function setExpire($expire)	{	$this->_expire = (int)($expire);}
	function __get($key)	{	return isset($this->_content[$key]) ? $this->_content[$key] : false;	}
	function __isset($key){		return isset($this->_content[$key]);	}
	function __set($key, $value){		
		if (is_array($value) || is_array($key)){throw new Exception('Forbidden array as cookie value') ;	return ;}
		if (preg_match('/¤|\|/', $key. $value))	{throw new Exception('Forbidden chars in cookie') ;	return ; };
		if (!$this->_modified && (!isset($this->_content[$key]) || (isset($this->_content[$key]) && $this->_content[$key] != $value))) //Fixed Important bug
			$this->_modified = true;
		$this->_content[$key] = $value;		
	}
	function __unset($key){		
		if (isset($this->_content[$key]))
			$this->_modified = true;
		unset($this->_content[$key]);
	}	
	function update($nullValues = false){		
		if (isset($_COOKIE[$this->_name]))	{
			$this->crypte_data = $_COOKIE ;
			$content = $this->_cipherTool->decrypt($_COOKIE[$this->_name]);
			$checksum = crc32($this->_iv.substr($content, 0, strrpos($content, '¤') + 2));
			$tmpTab = explode('¤', $content);
			foreach ($tmpTab AS $keyAndValue){
				$tmpTab2 = explode('|', $keyAndValue);
				if (count($tmpTab2) == 2)
					 $this->_content[$tmpTab2[0]] = $tmpTab2[1];
			 }
			if (isset($this->_content['checksum']))
				$this->_content['checksum'] = (int)($this->_content['checksum']);
			if (!isset($this->_content['checksum']) OR $this->_content['checksum'] != $checksum)
				$this->logout();
			if (!isset($this->_content['date_add']))
				$this->_content['date_add'] = date('Y-m-d H:i:s');
		}else{
			$this->_content['date_add'] = date('Y-m-d H:i:s');
		}
	}
	public function logout(){
		$this->_content = array();
		$this->_setcookie();
		unset($_COOKIE[$this->_name]);
		$this->_modified = true;
	}
	protected function _setcookie($cookie = null)	{
		if ($cookie){
			$content = $this->_cipherTool->encrypt($cookie);
			$time = $this->_expire;
		}else{
			$content = 0;
			$time = 1;
		}
		if (PHP_VERSION_ID <= 50200) /* PHP version > 5.2.0 */
			return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0);
		else
			return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0, true);
	}	
	function write(){
		if (!$this->_modified || headers_sent()) 	return;
		$cookie = '';
		if (isset($this->_content['checksum'])) unset($this->_content['checksum']);
		foreach ($this->_content AS $key => $value)
			$cookie .= $key.'|'.$value.'¤';
		$cookie .= 'checksum|'.crc32($this->_iv.$cookie);
		$this->_modified = false;
		return $this->_setcookie($cookie);
	}
}

?>