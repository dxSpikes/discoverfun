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
 
// (required)
$paypal->ip_address = $_SERVER['REMOTE_ADDR'];
 
// Order Totals (amount_total is required)
$paypal->amount_total = '56.48';
$paypal->amount_shipping = '5.99';
 
// Credit Card Information (required)
$paypal->credit_card_number = '4138592804463340';
$paypal->credit_card_type = 'Visa';
$paypal->cvv2_code = '123';
$paypal->expire_date = '122018';
 
// Billing Details (required)
$paypal->first_name = 'John';
$paypal->last_name = 'Doe';
$paypal->address1 = '123 Maple Street';
$paypal->address2 = 'Apt 3';
$paypal->city = 'Middletown';
$paypal->state = 'TX';
$paypal->postal_code = '77570';
$paypal->phone_number = '8315555555';
$paypal->country_code = 'US';
 
// Shipping Details (NOT required)
$paypal->email = 'johndoe@example.com';
$paypal->shipping_name = 'John Doe';
$paypal->shipping_address1 = '123 Maple Street';
$paypal->shipping_address2 = 'Apt 3';
$paypal->shipping_city = 'Middletown';
$paypal->shipping_state = 'TX';
$paypal->shipping_postal_code = '77570';
$paypal->shipping_phone_number = '8315555555';
$paypal->shipping_country_code = 'US';
 
// Add Order Items (NOT required) - Name, Number, Qty, Tax, Amt
//    Repeat for each item needing to be added
$paypal->addItem('Item Name', 'Item Number 012', 1, 0, '50.49');
 
// Perform the payment
$r = $paypal->DoDirectPayment();
echo $r;
?>