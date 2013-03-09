<?php



add_shortcode('process-payment','process_payment');

function process_payment() {
	session_start();

  	if(is_array(get_option('paypal'))) 
  	{
  		extract(get_option('paypal'));
  		//print_r(get_option('paypal'));
  		//die();
  	}

	$PayPalMode 			= $paypal_mode;
	$PayPalApiUsername 		= $paypal_username;
	$PayPalApiPassword 		= $paypal_pwd;
	$PayPalApiSignature 	= $paypal_signature;
	$PayPalCurrencyCode 	= $paypal_currency_code;
	$PayPalReturnURL 		= $paypal_return_url;
	$PayPalCancelURL 		= $paypal_cancel_url;
	$api_version 			= '85.0';

	
	//include_once(PLUGIN_PATH. "class/config.php");
	include_once(PLUGIN_PATH. "class/paypal.class.php");


	if($_POST) //Post Data received from product list page.
	{

		include(PLUGIN_PATH.'class/CurrencyConverter.php');
	   	$currncy_convrtr = new CurrencyConverter();

	   	$post_id = 87; //$wpdb->escape($_GET['post_id']);
	   	//Mainly we need 4 variables from an item, Item Name, Item Price, Item Number and Item Quantity.

		$package 		= get_post($post_id,OBJECT);
		$ItemName 		= $package->post_title; //Item Name
		$ItemPrice 		= get_post_meta($package->ID,'package_price',TRUE); //Item Price
		$ItemNumber 	= $package->ID; //Item Number
		$ItemQty 		= 1; // Item Quantity
		$ItemTotalPrice = $ItemPrice;//($ItemPrice*$ItemQty); //(Item Price x Quantity = Total) Get total amount of product;

	   	$ItemTotalPrice = $currncy_convrtr->convert($ItemTotalPrice,'PHP','USD');
	   	$data = $_POST;
	   	$pymnt_method = $data['payment_method'];

	   	switch($pymnt_method)
	   	{
	   		case 'paypal':
				 

				//Data to be sent to paypal
				$padata = 	'&CURRENCYCODE='.urlencode($PayPalCurrencyCode).
							'&PAYMENTACTION=Sale'.
							'&ALLOWNOTE=1'.
							'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
							'&PAYMENTREQUEST_0_AMT='.urlencode($ItemTotalPrice).
							'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice). 
							'&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
							'&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemTotalPrice).
							'&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
							'&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
							'&AMT='.urlencode($ItemTotalPrice).				
							'&RETURNURL='.urlencode($PayPalReturnURL ).
							'&CANCELURL='.urlencode($PayPalCancelURL);	
					
				//We need to execute the "SetExpressCheckOut" method to obtain paypal token
				$paypal= new MyPayPal();
				$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
				
				//Respond according to message we receive from Paypal
				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
				{
							
					// If successful set some session variable we need later when user is redirected back to page from paypal. 
					

					$data['itemprice'] 	 = $ItemPrice;
					$data['totalamount'] = $ItemTotalPrice;
					$data['itemName'] 	 = $ItemName;
					$data['itemNo'] 	 = $ItemNumber;
					$data['itemQTY'] 	 = $ItemQty;					

					$_SESSION['accomodation'] = $data;
					
					if($PayPalMode=='sandbox')
					{
						$paypalmode 	=	'.sandbox';
					}
					else
					{
						$paypalmode 	=	'';
					}
					//Redirect user to PayPal store with Token received.
				 	$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
					header('Location: '.$paypalurl);
					 
				}else{
					//Show error message
					echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					echo '<pre>';
					print_r($httpParsedResponseAr);
					echo '</pre>';
				}
				break;
			case 'credit_card':
					$nvp_string = 
								'METHOD=DoDirectPayment'.
								'&VERSION='. urlencode('3.0').
								'&PWD='. urlencode($PayPalApiPassword).
								'&USER='. urlencode($PayPalApiUsername).
								'&SIGNATURE='. urlencode($PayPalApiSignature).
								'&PAYMENTACTION=Sale'.
								'&IPADDRESS='. urlencode($_SERVER["REMOTE_ADDR"]).
								'&AMT='. urlencode($ItemTotalPrice). 
								'&CREDITCARDTYPE='. urlencode($data['cc_type']) . 
								'&ACCT='. urlencode($data['cc_number']) .
								'&EXPDATE='.  urlencode($data['cc_month']) . urlencode($data['cc_year']) .
								'&FIRSTNAME='. urlencode($data['firstname']) .
								'&LASTNAME='. urlencode($data['lastname']).
								'&STREET='. urlencode($data['cus_address']).
								'&STREET2='.
								'&CITY='. urlencode($data['city']) .
								'&STATE='. urlencode($data['state']) .
								'&COUNTRYCODE='.  urlencode($data['country']) .
								'&ZIP='. urlencode($data['zip_code']). 
								'&CURRENCYCODE=USD'.
								'&SHIPPINGAMT=0'.
								'&CVV2='. urlencode($data['cc_cvv_number']). 
								'&EMAIL='. urlencode($data['email']) . 
								'&PHONENUM='. urlencode($data['mobile']) . 
								'&SHIPTONAME='.
								'&SHIPTOSTREET='.
								'&SHIPTOSTREET2='.
								'&SHIPTOCITY='.
								'&SHIPTOSTATE='.
								'&SHIPTOZIP='.
								'&SHIPTOCOUNTRYCODE='.
								'&SHIPTOPHONENUM='.
								'&L_NAME0='. urlencode($ItemName).
								'&L_NUMBER0='. urlencode($ItemNumber) .
								'&L_QTY0=1'.
								'&L_TAXAMT0=0'.
								'&L_AMT0='. urlencode($ItemTotalPrice) .
								'&ITEMAMT='. urlencode($ItemTotalPrice);

							
					if($PayPalMode=='sandbox')
					{
						$paypalmode 	=	'.sandbox';
					}
					else
					{
						$paypalmode 	=	'';
					}				
					//	LIVE
					// private $API_ENDPOINT = 'https://api-3t.paypal.com/nvp';
					//	SANDBOX
					$api_endpoint = 'https://api-3t'. $paypalmode .'.paypal.com/nvp';			
					// Send NVP string to PayPal and store response
					$curl = curl_init();
							curl_setopt($curl, CURLOPT_VERBOSE, 1);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
							curl_setopt($curl, CURLOPT_TIMEOUT, 30);
							curl_setopt($curl, CURLOPT_URL, $api_endpoint);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);

					$result = curl_exec($curl);
					curl_close($curl);

					// Parse the API response
					$result_array = NVPToArray($result);
					$data = $_POST;

					if($result_array['TRANSACTIONID'] == 'Success') {
						
    					$data['accomodation_status'] = 'COMPLETED';

    				} else {

    					$data['accomodation_status'] = 'PENDING';
    				}
					echo '<pre>';
    				print_r($result_array);
    				echo '</pre>';
					$data['itemprice'] 	 = $ItemPrice;
					$data['totalamount'] = $ItemTotalPrice;
					$data['itemName'] 	 = $ItemName;
					$data['itemNo'] 	 = $ItemNumber;
					$data['itemQTY'] 	 = $ItemQty;	
    				$data['transID'] 	= $result_array['TRANSACTIONID'];
    				$data['accomodation_status'] = 'COMPLETED';
    				save_accomodation_information($data); 

    				
					
				break;

		}

	}



	//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
	if(isset($_GET["token"]) && isset($_GET["PayerID"]))
	{
		//we will be using these two variables to execute the "DoExpressCheckoutPayment"
		//Note: we haven't received any payment yet.
		
		$token 		= $_GET["token"];
		$playerid 	= $_GET["PayerID"];

		//get session variables
		//$ItemPrice 		= $_SESSION['accomodation']['itemprice'];
		$ItemTotalPrice = $_SESSION['accomodation']['totalamount'];
		//$ItemName 		= $_SESSION['accomodation']['itemName'];
		//$ItemNumber 	= $_SESSION['accomodation']['itemNo'];
		//$ItemQTY 		= $_SESSION['accomodation']['itemQTY'];
		
		$padata = 	'&TOKEN='.urlencode($token).
					'&PAYERID='.urlencode($playerid).
					'&PAYMENTACTION='.urlencode("SALE").
					'&AMT='.urlencode($ItemTotalPrice).
					'&CURRENCYCODE='.urlencode($PayPalCurrencyCode);
		
		//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
		//Check if everything went ok..
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		{
				echo '<h2>Success</h2>';
				echo 'Your Transaction ID :'.urldecode($httpParsedResponseAr["TRANSACTIONID"]);
				
					/*
					//Sometimes Payment are kept pending even when transaction is complete. 
					//May be because of Currency change, or user choose to review each payment etc.
					//hence we need to notify user about it and ask him manually approve the transiction
					*/
					
					if('Completed' == $httpParsedResponseAr["PAYMENTSTATUS"])
					{
						echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
					}
					elseif('Pending' == $httpParsedResponseAr["PAYMENTSTATUS"])
					{
						echo '<div style="color:red">Transaction Complete, but payment is still pending! You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
					}
				

				echo '<br /><b>Stuff to store in database :</b><br /><pre>';
	
					$transactionID = urlencode($httpParsedResponseAr["TRANSACTIONID"]);
					$nvpStr = "&TRANSACTIONID=".$transactionID;
					$paypal= new MyPayPal();
					$httpParsedResponseAr = $paypal->PPHttpPost('GetTransactionDetails', $nvpStr, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
					
					$data = $_SESSION['accomodation'];
    				$data['transID'] = $transactionID;

					if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
						echo '<pre>';
						print_r($httpParsedResponseAr);
						echo '</pre>';
						$data['accomodation_status'] = 'COMPLETED';
					} else  {
						echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
						echo '<pre>';
						print_r($httpParsedResponseAr);
						echo '</pre>';
						$data['accomodation_status'] = 'PENDING';

					}

					
    				save_accomodation_information($data); 
		
		}
		else
		{
				echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				echo '<pre>';
				print_r($httpParsedResponseAr);
				echo '</pre>';
		}
	}	

}



function init_instant_payment_notification() {
	global $wpdb;

   //Change these with your information
	$mode       = 'sandbox'; //Sandbox for testing or empty ''

	if($_POST)
	{
	        if($mode=='sandbox')
	        {
	            $paypalmode     =   '.sandbox';
	        }
	        $req = 'cmd=' . urlencode('_notify-validate');
	        foreach ($_POST as $key => $value) {
	            $value = urlencode(stripslashes($value));
	            $req .= "&$key=$value";
	        }
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, 'https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr');
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www'.$paypalmode.'.sandbox.paypal.com'));
	        $res = curl_exec($ch);
	        curl_close($ch);

	        if (strcmp ($res, "VERIFIED") == 0)
	        {
	            $transaction_id = $_POST['txn_id'];
	            $payerid 		= $_POST['payer_id'];
	            $firstname 		= $_POST['first_name'];
	            $lastname 		= $_POST['last_name'];
	            $payeremail 	= $_POST['payer_email'];
	            $paymentdate 	= $_POST['payment_date'];
	            $paymentstatus 	= $_POST['payment_status'];
	            $mdate			= date('Y-m-d h:i:s',strtotime($paymentdate));
	            $otherstuff 	= json_encode($_POST);

	           

	            // insert in our IPN record table, or you can update existing record

	            $wpdb->insert($wpdb->prefix .'transaction_log',array(
	                          'itransaction_id'	=> $transaction_id,
	                          'ipayerid'		=> $payerid,
	                          'iname'			=> $firstname .' '. $lastname,
	                          'iemail'			=> $payeremail,
	                          'itransaction_date' => $mdate,
	                          'ipaymentstatus'	=> $paymentstatus,
	                          'ieverything_else'	=> $otherstuff
	                          ), array('%s','%s','%s','%s','%s','%s','%s'));

	        }
	}

}

add_shortcode('instant-payment-nofication','init_instant_payment_notification');

// Function to convert NTP string to an array
function NVPToArray($NVPString)
{
	$proArray = array();
	while(strlen($NVPString))
	{
		// name
		$keypos= strpos($NVPString,'=');
		$keyval = substr($NVPString,0,$keypos);
		// value
		$valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
		$valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
		// decoding the respose
		$proArray[$keyval] = urldecode($valval);
		$NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
	}
	return $proArray;
}

