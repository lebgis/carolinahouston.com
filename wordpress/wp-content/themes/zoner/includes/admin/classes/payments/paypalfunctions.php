<?php
class zoner_PayPal {
	/********************************************
	PayPal API Module
	 
	Defines all the global variables and the wrapper functions 
	********************************************/
	private $PROXY_HOST;
	private $PROXY_PORT;
	private $USE_PROXY;
	
	private $SandboxFlag;

	//'------------------------------------
	//' PayPal API Credentials
	//' Replace <API_USERNAME> with your API Username
	//' Replace <API_PASSWORD> with your API Password
	//' Replace <API_SIGNATURE> with your Signature
	//'------------------------------------
	private $API_UserName;
	private $API_Password;
	private $API_Signature;

	// BN Code 	is only applicable for partners
	private $sBNCode;
	private $API_Endpoint;
	private $PAYPAL_URL;
	
	private $version = "94.0";
	
	
	/*	
	' Define the PayPal Redirect URLs.  
	' 	This is the URL that the buyer is first sent to do authorize payment with their paypal account
	' 	change the URL depending if you are testing on the sandbox or the live PayPal site
	'
	' For the sandbox, the URL is       https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=
	' For the live site, the URL is     https://www.paypal.com/webscr&cmd=_express-checkout&token=
	*/
	
	public function __construct($SandboxFlag, $sBNCode, $API_UserName, $API_Password, $API_Signature, $useProxy  = false, $proxyHost = '', $proxyPort = '') {
		
		$this->SandboxFlag   = $SandboxFlag;
		$this->sBNCode 	     = $sBNCode;
		$this->API_UserName  = $API_UserName;
		$this->API_Password  = $API_Password;
		$this->API_Signature = $API_Signature;
		
		$this->USE_PROXY 	= $useProxy;
		$this->PROXY_HOST	= $proxyHost;
		$this->PROXY_PORT	= $proxyPort;
		
		if ($this->SandboxFlag  == 'sandbox') {
			$this->API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
			$this->PAYPAL_URL   = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
		} else {
			$this->API_Endpoint = "https://api-3t.paypal.com/nvp";
			$this->PAYPAL_URL   = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
		}
	}	

	public function request($method,$params = array()) {
		if( empty($method) ) {
			return false;
		}

		$requestParams = 
		array(
			'METHOD'  => $method,
			'VERSION' => $this->version
		) + array(
			'USER' 		=> $this->API_UserName,
			'PWD'  		=> $this->API_Password,
			'SIGNATURE' => $this->API_Signature,
		);

		$request = http_build_query($requestParams + $params);

		// Настраиваем cURL
		$curlOptions = array (
			 CURLOPT_URL 			=> $this->API_Endpoint,
			 CURLOPT_VERBOSE 		=> 1,
			 CURLOPT_SSL_VERIFYPEER => false,
			 CURLOPT_SSL_VERIFYHOST => 2,
			 CURLOPT_RETURNTRANSFER => 1,
			 CURLOPT_POST 			=> 1,
			 CURLOPT_POSTFIELDS 	=> $request
		);

		$ch = curl_init();
		curl_setopt_array($ch, $curlOptions);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			 curl_close($ch);
			 return false;
		} else  {
			 curl_close($ch);
			 $responseArray = array();
			 parse_str($response,$responseArray);
			 return $responseArray;
		}
	}	
	
	/*'----------------------------------------------------------------------------------
	 Purpose: Redirects to PayPal.com site.
	 Inputs:  NVP string.
	 Returns: 
	----------------------------------------------------------------------------------
	*/
	public function RedirectToPayPal ( $token )
	{
		// Redirect to paypal.com here
		$payPalURL = $this->PAYPAL_URL . $token;
		header("Location: ".$payPalURL);
		exit;
	}
	
	
	public function RedirectToPayPalUrl ( $token )
	{
		// Redirect to paypal.com here
		$payPalURL = $this->PAYPAL_URL . $token;
		return $payPalURL;
	}

	
	/*'----------------------------------------------------------------------------------
	 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  * It is usefull to search for a particular key and displaying arrays.
	  * @nvpstr is NVPString.
	  * @nvpArray is Associative Array.
	   ----------------------------------------------------------------------------------
	  */
	public function deformatNVP($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();

		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }
		return $nvpArray;
	}
	
} //end class zoner_PayPal