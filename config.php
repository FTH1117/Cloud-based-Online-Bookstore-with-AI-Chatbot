<?php
// Database hostname, don't change this unless your hostname is different
define('db_host','localhost');
// Database username
define('db_user','root');
// Database password
define('db_pass','fthing1117');
// Database name
define('db_name','shoppingcart_advanced');
// This will change the title on the website
define('site_name','AI BookStore');
// Currency code, default is USD, you can view the list here: http://cactus.io/resources/toolbox/html-currency-symbol-codes
define('currency_code','RM');
// Featured image URL
define('featured_image','uploads/books_background.jpg');
// Default payment status
define('default_payment_status','Completed');
// Account required for checkout?
define('account_required',true);
// The from email that will appear on the customer's order details email
define('mail_from','noreply@yourwebsite.com');
// Send mail to the customers, etc?
define('mail_enabled',true);
// Your email
define('email','foongtzehing@gmail.com');
// Receive email notifications?
define('email_notifications',true);
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
define('paypal_ipn_url','http://localhost:1234/advanced/ipn/paypal.php');
// PayPal cancel URl, the page the customer returns to when they cancel the payment
define('paypal_cancel_url','http://localhost:1234/advanced/cart');
// PayPal return URL, the page the customer returns to after the payment has been made:
define('paypal_return_url','http://localhost:1234/advanced/placeorder');


?>