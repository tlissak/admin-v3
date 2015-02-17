<?php
class AtosApi{	
	public $data = array(); 
	public $n_payments = 3;	
	public $params = array();
	
	function __construct($n_payments = 1 ){  
		$this->n_payments = $n_payments ; 
	}
	
	function init_response_params( array $Params ){		
		$this->params['pathfile'] = ATOS_PATHFILE ;
		$this->params['message'] = $Params['DATA'] ;
	}
	
	function init_request_params( array $OrderInfo){
				
		$AMOUNT = number_format($OrderInfo['amount'], 2, '', '')  ;		
		$params = array();		
		$params['pathfile'] = ATOS_PATHFILE ;
		$params['merchant_id'] = ATOS_MERCHANT_ID ;
		$params['amount'] = $AMOUNT ;
		
		$params['merchant_country']	= 'fr' ;//MERCHANT_COUNTRY!fr!
		$params['merchant_language']	= 'fr' ;//MERCHANT_LANGUAGE!fr!
		$params['language'] = 'fr' ;//LANGUAGE!fr!
		$params['currency_code'] = 978 ;
		$params['customer_id'] 	= $OrderInfo['client_id'];
		$params['caddie'] = $OrderInfo['order_id'] ;
		$params['order_id'] =  $OrderInfo['order_id'] ;
		$params['normal_return_url'] 	= ATOS_RETURN . "?type=normal&order_id=". $OrderInfo['order_id'] ;//RETURN_URL!http://glasman.fr/payment/return.asp!
		$params['cancel_return_url'] 	=ATOS_RETURN . "?type=cancel&order_id=". $OrderInfo['order_id'] ;//CANCEL_URL!http://glasman.fr/payment/cancel.asp!
		$params['automatic_response_url'] = ATOS_RETURN . "?type=auto&order_id=". $OrderInfo['order_id'] ; // AUTO_RESPONSE_URL!http://glasman.fr/payment/auto.asp!
		$params['header_flag'] = "yes" ; //HEADER_FLAG!yes!			
		$params['payment_means'] 	= 'CB,2,VISA,2,MASTERCARD,2' ;//PAYMENT_MEANS!CB,2,VISA,2,MASTERCARD,2!   
		$params['capture_mode'] = 'PAYMENT_N';
		$params['capture_day'] =1;
		$params['block_align'] 			= "center"; //BLOCK_ALIGN!center! left
		$params['customer_email'] = $OrderInfo['email'] && strlen($OrderInfo['email'] ) > 128 ? substr($OrderInfo['email'],0,128) : $OrderInfo['email'] ;
		$params['customer_ip_address'] = (IP!="") ? substr(IP, 0, 19) : '' ;
		$params['capture_mode'] = "";
		$params['block_order'] = "1,2,3,4,5,6,7,8"; //BLOCK_ORDER!1,2,3,4,5,6,7,8!
		$params['textcolor'] = "000000"; //TEXTCOLOR!000000! black
		$params['receipt_complement'] = "receipt_complement<i>receipt_complement</i>" ;
		$params['bgcolor'] = "FFFFFF" ;
		$params['white'] = "";
		$params['return_context'] = "";
		$params['target'] = "_top" ;//TARGET!_top!
		$params['normal_return_logo'] = "";
		$params['submit_logo'] = "";
		$params['background_id'] = "bg.gif" ;
		$params['templatefile'] = "";
		$params['advert'] = "logo.gif"; //ADVERT!http://glasman.fr/logo.png!
		$params['cancel_return_logo'] = "" ;
		$params['logo_id'] = "left.gif"; //LOGO!cyber.gif!
		$params['logo_id2'] =  "cp.gif";//LOGO2!bp.gif!
		$params['condition'] = '';#CONDITION!SSL!
		
		$this->data['date_time']= date('m/d/Y h:m:s') ;
		$this->data[] = 'NO_RESPONSE_PAGE' ; //$this->FORCE_RETURN
		
		$this->params = $params;
		$this->n_payment($AMOUNT);
		$this->params['data'] = implode(';', $this->data);
	}
		
	function n_payment($amount){		
		if ($this->n_payments == 2){
			$initialAmount = $amount / 2;
			$initialAmount = str_pad((string)intval(round( $initialAmount , 2)), 3, '0', STR_PAD_LEFT);
			$this->params['capture_mode'] = 'PAYMENT_N';
			$this->params['capture_day'] = 0;
			$this->data[] = 'NB_PAYMENT=2';
			$this->data[] = 'PERIOD=30';
			$this->data[] = 'INITIAL_AMOUNT='. $initialAmount; 
		}elseif ($this->n_payments == 3){		
			$initialAmount = $amount / 3;
			$initialAmount = str_pad((string)intval(round( $initialAmount , 2)), 3, '0', STR_PAD_LEFT);
			$this->params['capture_mode'] = 'PAYMENT_N';
			$this->params['capture_day'] = 0 ;
			$this->data[] = 'NB_PAYMENT=3';
			$this->data[] = 'PERIOD=30';
			$this->data[] = 'INITIAL_AMOUNT='. $initialAmount ;
		}
	}
	
	public $command ,$last_line , $output , $exit_code ; 
	public $success,$error,$response;

	function exe($bin = ATOS_BIN_REQUEST){		
		$this->args  = $this->paramsToArgs($this->params);		
		$this->command = escapeshellcmd( $bin  ) .' ' . implode(' ', array_map('escapeshellarg', $this->args) ) ;
		$this->last_line = exec($this->command , $this->output, $this->exit_code);	
		$output = explode('!', substr($this->last_line, 1));
		
		$this->success = array_shift($output) == '0';
		$this->error = array_shift($output);
		if ($this->success)
			$this->response = $output[0];
		else {
			$this->response = $this->error;
		//	p($this->last_line) ;
		}
	}
	public function paramsToArgs(array $params)	{
		$args = array();
		foreach ($params as $name => $value)
			if (!empty($value) || $value === 0 || $value === '0')	$args[] = $name.'='.$value;
		return $args;
	}
	
	public function log_response($return_type = 'unset'){

		
		if ($this->error){
			mail(ATOS_LOG_EMAIL,'error paiemet glasman ['.$return_type.']' , $this->error,_EMAIL_HEADER_)	 ;			
			return ;
		}
		
		$tableau = explode('!', $this->last_line);
		$log = "------------------------ ".date('Y-m-d h:m:s')." -----------------------------" ;		
		$vals = array(
			'merchant_id'=> $tableau[3] ,
			'merchant_country'=> $tableau[4] ,
			"amount" => $tableau[5] ,
			"transaction_id" =>  $tableau[6],
			"payment_means" =>  $tableau[7],
			"transmission_date" =>  $tableau[8],
			"payment_time" => $tableau[9],
			"payment_date" =>  $tableau[10],
			"response_code" =>  $tableau[11],
			"payment_certificate" =>  $tableau[12],
			"authorisation_id" =>  $tableau[13],
			"currency_code" =>  $tableau[14],
			"card_number" =>  $tableau[15],
			"cvv_flag" =>  $tableau[16],
			"cvv_response_code" =>  $tableau[17],
			"bank_response_code" =>  $tableau[18],
			"complementary_code" =>  $tableau[19],
			"return_context" =>  $tableau[20],
			"unk" => $tableau[21],
			"caddie" =>  $tableau[22],
			"receipt_complement" => $tableau[23],
			"merchant_language" =>  $tableau[24],
			"language" =>  $tableau[25],
			"customer_id" =>  $tableau[26],
			"order_id" =>  $tableau[27],
			"customer_email" =>  $tableau[28], 
			"customer_ip_address" =>  $tableau[29],
			"capture_day" => $tableau[30] ,
			"capture_mode" => $tableau[31],
			"data" =>  $tableau[32] ,
			'last_line' => $this->last_line,
			'datetime'=> date('Y-m-d h:m:s'),
			'return_type'=>$return_type
		);			
		foreach ($vars as $k=>$v){
			$log .= "\n\r<br />" .  $k .' : '. $v ;	
		}
		
		try{
			$sql = SQL::build('INSERT','transaction',$vals) ;
			$db->query( $sql );
		}catch(Exception $e){
			mail(ATOS_LOG_EMAIL,'Log paiement glasman ['.$return_type.']',$log . ' ' . $e->getMessage() ,_EMAIL_HEADER_);
			//mail('tlissak@gmail.com','Transaction insert Exception',$e->getMessage(),_EMAIL_HEADER_);		
		}
		
	}
}

?>