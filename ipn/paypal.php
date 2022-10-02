<?php
define('shoppingcart', true);
include '../config.php';
include '../functions.php';
// Get all input variables and convert them all to URL string variables
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = [];
foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
		if ($keyval[0] === 'payment_date') {
			if (substr_count($keyval[1], '+') === 1) {
				$keyval[1] = str_replace('+', '%2B', $keyval[1]);
			}
		}
		$myPost[$keyval[0]] = urldecode($keyval[1]);
	}
}
$req = 'cmd=_notify-validate';
$get_magic_quotes_exists = function_exists('get_magic_quotes_gpc') ? true : false;
foreach ($myPost as $key => $value) {
	if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
    $req .= "&$key=$value";
}
// Below will verify the transaction, it will make sure the input data is correct, we wouldn't want anybody trying to cheat the system..
$ch = curl_init(paypal_testmode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: Close']);
$res = curl_exec($ch);
curl_close($ch);
if (strcmp($res, 'VERIFIED') == 0) {
    // Transaction is verified and successful...
    $pdo = pdo_connect_mysql();
    $products_in_cart = [];
    $subtotal = 0.00;
    $shippingtotal = isset($_POST['mc_shipping1']) ? floatval($_POST['mc_shipping1']) : 0.00;
    // Retrieve custom data (account_id, discount_code)
    $custom = json_decode($_POST['custom'], true);
    // Iterate the cart items and insert the transaction items into the MySQL database
    for ($i = 1; $i < (intval($_POST['num_cart_items'])+1); $i++) {
        // Update product quantity in the products table
        $stmt = $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE quantity > 0 AND id = ?');
        $stmt->execute([ $_POST['quantity' . $i], $_POST['item_number' . $i] ]);
        // Product related variables
        $option = isset($_POST['os0_' . $i]) ? $_POST['os0_' . $i] : '';
		if (empty($option)) {
			$option = isset($_POST['option_selection1_' . $i]) ? $_POST['option_selection1_' . $i] : '';
		}
        $option = $option == 'N/A' ? '' : $option;
        // Deduct option quantities
        if ($option) {
            $options = explode(',', $option);
            foreach ($options as $opt) {
				$v = explode('-', $opt);
				if (isset($v[0], $v[1])) {
					$stmt = $pdo->prepare('UPDATE products_options SET quantity = quantity - ? WHERE quantity > 0 AND title = ? AND (name = ? OR name = "")');
					$stmt->execute([ $_POST['quantity' . $i], $v[0], $v[1] ]);    
				}				
            }
        }
        $item_price = floatval($_POST['mc_gross_' . $i]) / intval($_POST['quantity' . $i]);
        // Insert product into the "transactions_items" table
        $stmt = $pdo->prepare('INSERT INTO transactions_items (txn_id, item_id, item_price, item_quantity, item_options) VALUES (?,?,?,?,?)');
        $stmt->execute([ $_POST['txn_id'], $_POST['item_number' . $i], $item_price, $_POST['quantity' . $i], $option ]);
        // Add product to array
        $products_in_cart[] = [
            'id' => $_POST['item_number' . $i],
            'quantity' => $_POST['quantity' . $i],
            'options' => $option,
            'final_price' => $item_price,
            'meta' => [
                'name' => $_POST['item_name' . $i],
                'price' => $item_price
            ]
        ];
        // Add product price to the subtotal variable
        $subtotal += $item_price * intval($_POST['quantity' . $i]);
    }
    // Insert the transaction into our transactions table, as the payment status changes the query will execute again and update it, make sure the "txn_id" column is unique
    $stmt = $pdo->prepare('INSERT INTO transactions (txn_id, payment_amount, payment_status, created, payer_email, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, account_id, payment_method, shipping_method, shipping_amount, discount_code) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE payment_status = VALUES(payment_status)');
    $stmt->execute([
        $_POST['txn_id'],
        $subtotal,
        $_POST['payment_status'] == 'Completed' ? default_payment_status : $_POST['payment_status'],
        date('Y-m-d H:i:s'),
        $_POST['payer_email'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['address_street'],
        $_POST['address_city'],
        $_POST['address_state'],
        $_POST['address_zip'],
        $_POST['address_country'],
        $custom['account_id'],
        'paypal',
        $custom['shipping_method'],
        $shippingtotal,
        $custom['discount_code']
    ]);
    $order_id = $pdo->lastInsertId();
    // Send order details to the customer's email address
    send_order_details_email(
        $_POST['payer_email'],
        $products_in_cart,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['address_street'],
        $_POST['address_city'],
        $_POST['address_state'],
        $_POST['address_zip'],
        $_POST['address_country'],
        $subtotal,
        $order_id
    );
}
?>