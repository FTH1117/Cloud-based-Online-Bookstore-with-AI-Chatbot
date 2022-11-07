<?php
// Database info
/*
define('db_host','database-1.cq9szh8qchb5.us-west-2.rds.amazonaws.com');
define('db_user','admin');
define('db_pass','');
define('db_name','fth_fyp');
*/
define('db_host','localhost');
define('db_user','root');
define('db_pass','');
define('db_name','fth_fyp');

// Website details
define('site_name','<i class="fa fa-book" style="font-size:25px"></i> FTH BookStore ');
define('currency_code','RM');

define('featured_image','uploads/books_background.jpg');
define('about_image','uploads/reading.jpg');
define('contact_image','uploads/reading2.jpg');

define('default_payment_status','Completed');
define('account_required',true);


// Rewrite URL?
define('rewrite_url',false);

/* Pay on Delivery */
define('pay_on_delivery_enabled',true);

/* PayPal */
// Accept payments with PayPal?
define('paypal_enabled',true);
// Your business email account, this is where you'll receive the money
define('paypal_email','foongtzehing@gmail.com');
// If the test mode is set to true it will use the PayPal sandbox website, which is used for testing purposes.
// Read more about PayPal sandbox here: https://developer.paypal.com/developer/accounts/
// Set this to false when you're ready to start accepting payments on your business account
define('paypal_testmode',false);
// Currency to use with PayPal, default is USD
define('paypal_currency','MYR');
// PayPal IPN url, this should point to the IPN file located in the "ipn" directory
define('paypal_ipn_url','http://fth-fyp.me/fyp/ipn/paypal.php');
// PayPal cancel URl, the page the customer returns to when they cancel the payment
define('paypal_cancel_url','http://fth-fyp.me/fyp/index.php?page=cart');
// PayPal return URL, the page the customer returns to after the payment has been made:
define('paypal_return_url','http://fth-fyp.me/fyp/index.php?page=placeorder');


?>