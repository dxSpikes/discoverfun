<?php

function process_payment() {
	

	if($_POST) //Post Data received from product list page.
	{
		include(PLUGIN_PATH.'class/phpPaypal.php');
		include(PLUGIN_PATH.'class/CurrencyConverter.php');

	   	$post = $_POST;
	   	$pymnt_method = $post['payment_method'];
	   	switch($pymnt_method)
	   	{
	   		case 'paypal':
	   			doPaypalPayment($post);
				break;
			case 'credit_card':
				doDirectPayment($post);
				break;

		}

	}
	//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
	if(isset($_GET["token"]) && isset($_GET["PayerID"]))
	{	include(PLUGIN_PATH.'class/phpPaypal.php');
		$paypal = new phpPayPal();
		$paypal->token = urlencode($_GET["token"]);
		$paypal->payer_id = urlencode($_GET["PayerID"]);
		$paypal->DoExpressCheckoutPayment();
	}


}
add_shortcode('process-payment','process_payment');

function doPaypalPayment($data) {
	/*
	*Response
	*$paypal->Response;
	*Display the response
	*print_r($paypal->Response);
	*/
	$currncy_convrtr = new CurrencyConverter();

 	$package = get_post(87);//$data['package_id']
   	$total_price = 40.73;
   	$total_price = $currncy_convrtr->convert($total_price,'PHP','USD');

	// Create instance of the phpPayPal class
	$paypal = new phpPayPal();
	 
	// Set the amount total for this order.
	$paypal->amount_total = $total_price;
	 
	// You can manually set the return and cancel URLs, or keep the one's pre-set in the class definition
	$paypal->return_url = get_page_link(10);
	$paypal->cancel_url = get_page_link(110);
	//$paypal->description = $package->post_title;
	$paypal->item_number = $package->ID;
	$paypal->item_name =  $package->post_title;
	$paypal->item_qty = 1;
	$paypal->item_price = $total_price;

	// Make the request
	$paypal->SetExpressCheckout();
	 
	// If successful, we need to store the token, and then redirect the user to PayPal
	if(!$paypal->_error)
	{
	   // Store your token
	   $_SESSION['token'] = $paypal->token;
	 
	   // Now go to PayPal
	   $paypal->SetExpressCheckoutSuccessfulRedirect();
	}
}


function doDirectPayment($data) {
	$paypal = new phpPayPal();
	$currncy_convrtr = new CurrencyConverter();

 	$package = get_post(87);//$data['package_id']
   	$total_price = 40.73;
   	$total_price = $currncy_convrtr->convert($total_price,'PHP','USD');
   	

	// (required)
	$paypal->ip_address 			= $_SERVER['REMOTE_ADDR'];
	 
	// Order Totals (amount_total is required)
	$paypal->amount_total 			= $total_price;
	$paypal->amount_shipping		= '0';
	 
	// Credit Card Information (required)
	$paypal->credit_card_number 	= $data['cc_number'];
	$paypal->credit_card_type 		= $data['cc_type'];
	$paypal->cvv2_code 				= $data['cc_cvv_number'];
	$paypal->expire_date 			= $data['cc_month'].$data['cc_year'];
	 
	// Billing Details (required)
	$paypal->first_name 			= $data['firstname'];
	$paypal->last_name 				= $data['lastname'];
	$paypal->address1 				= $data['cus_address'];
	$paypal->address2 				= '';//$data['a'];
	$paypal->city 					= $data['city'];
	$paypal->state 					= $data['state'];
	$paypal->postal_code 			= $data['zip_code'];
	$paypal->phone_number 			= $data['mobile'];
	$paypal->country_code 			= $data['country'];
	 
	// Shipping Details (NOT required)
	$paypal->email 					= $data['email'];
	$paypal->shipping_name 			= '';
	$paypal->shipping_address1 		= '';
	$paypal->shipping_address2 		= '';
	$paypal->shipping_city 			= '';
	$paypal->shipping_state 		= '';
	$paypal->shipping_postal_code 	= '';
	$paypal->shipping_phone_number 	= '';
	$paypal->shipping_country_code 	= '';
	 
	// Add Order Items (NOT required) - Name, Number, Qty, Tax, Amt
	//    Repeat for each item needing to be added
	$paypal->addItem($package->title, $package->ID, 1, 0, $total_price);
	 
	// Perform the payment
	$r = $paypal->DoDirectPayment();
	echo $r;
}
