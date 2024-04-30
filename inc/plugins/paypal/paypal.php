<?php
/**
 * Class  PayPal
 */
 
class PayPal {

	private $API_USERNAME;
	private $API_PASSWORD;
	private $API_SIGNATURE;
	private $PP_RETURN;
	private $PP_CANCEL;

	private $endpoint;
	private $host;
	private $gate;

	function __construct($config = array() ) {
		$this->endpoint = '/nvp';

		$this->API_USERNAME = $config['username'];
		$this->API_PASSWORD = $config['password'];
		$this->API_SIGNATURE = $config['signature'];
		$this->PP_RETURN = $config['return'];
		$this->PP_CANCEL = $config['cancel'];

		if ( empty($config['debug']) ) {
			$this->host = "api-3t.paypal.com";
			$this->gate = 'https://www.paypal.com/cgi-bin/webscr?';
		} else {
			//sandbox
			$this->host = "api-3t.sandbox.paypal.com";
			$this->gate = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
		}
	}

	/**
	 * @return HTTPRequest
	 */
	private function response($data){
		$r = new HTTPRequest($this->host, $this->endpoint, 'POST', true);
		$result = $r->connect($data);
		if ($result<400) return $r;
		return false;
	}

	private function buildQuery($data = array()){
		$data['USER'] = $this->API_USERNAME;
		$data['PWD'] = $this->API_PASSWORD;
		$data['SIGNATURE'] = $this->API_SIGNATURE;
		$data['VERSION'] = '52.0';
		$query = http_build_query($data);
		return $query;
	}

	
	/**
	 * Main payment function
	 */
	public function doExpressCheckout($amount, $desc, $invoice='', $currency='USD'){

		$data = array(
		'PAYMENTACTION' =>'Sale',
		'AMT' =>$amount,
		'RETURNURL' => $this->PP_RETURN,
		'CANCELURL'  => $this->PP_CANCEL,
		'DESC'=>$desc,
		'NOSHIPPING'=>"1",
		'ALLOWNOTE'=>"1",
		'CURRENCYCODE'=>$currency,
		'METHOD' =>'SetExpressCheckout');
		
		$data['CUSTOM'] = $amount.'|'.$currency.'|'.$invoice;
		if ($invoice) $data['INVNUM'] = $invoice;

		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);

		if ($return['ACK'] == 'Success') {
			return $this->gate.'cmd=_express-checkout&useraction=commit&token='.$return['TOKEN'];
			header('Location: '.$this->gate.'cmd=_express-checkout&useraction=commit&token='.$return['TOKEN'].'');
			die();
		}
		return($return);
	}

	public function getCheckoutDetails($token){
		$data = array(
		'TOKEN' => $token,
		'METHOD' =>'GetExpressCheckoutDetails');
		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);
		return($return);
	}
	public function doPayment(){
		$token = $_GET['token'];
		$payer = $_GET['PayerID'];
		$details = $this->getCheckoutDetails($token);
		if (!$details) return false;
		list($amount,$currency,$invoice) = explode('|',$details['CUSTOM']);
		$data = array(
		'PAYMENTACTION' => 'Sale',
		'PAYERID' => $payer,
		'TOKEN' =>$token,
		'AMT' => $amount,
		'CURRENCYCODE'=>$currency,
		'METHOD' =>'DoExpressCheckoutPayment');
		$query = $this->buildQuery($data);

		$result = $this->response($query);

		if (!$result) return false;
		$response = $result->getContent();
		$return = $this->responseParse($response);

		return($return);
	}

	private function getScheme() {
		$scheme = 'http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
			$scheme .= 's';
		}
		return $scheme;
	}

	private function responseParse($resp){
		$a=explode("&", $resp);
		$out = array();
		foreach ($a as $v){
			$k = strpos($v, '=');
			if ($k) {
				$key = trim(substr($v,0,$k));
				$value = trim(substr($v,$k+1));
				if (!$key) continue;
				$out[$key] = urldecode($value);
			} else {
				$out[] = $v;
			}
		}
		return $out;
	}
}

?>