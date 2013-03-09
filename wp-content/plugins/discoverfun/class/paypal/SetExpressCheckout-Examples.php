<?php 
// this is the Request file

/*
*Response
*$paypal->Response;
*Display the response
*print_r($paypal->Response);
*/

include('phpPaypal.0.5.2.php');
// Create instance of the phpPayPal class
$paypal = new phpPayPal();
 
// Set the amount total for this order.
$paypal->amount_total = '50.49';
 
// You can manually set the return and cancel URLs, or keep the one's pre-set in the class definition
$paypal->return_url = 'http://www.example.com/successful_return.php';
$paypal->cancel_url = 'http://www.example.com/failed.php';
 
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
?>