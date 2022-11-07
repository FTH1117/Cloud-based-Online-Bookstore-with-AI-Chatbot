<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
// Default values for the input form elements
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$account = [
    'first_name' => '',
    'last_name' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => 'United States',
    'role' => 'Member'
];
// Error array, output errors on the form
$errors = [];
// Redirect the user if the shopping cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: ' . url('index.php?page=cart'));
    exit;
}
// Check if user is logged in
if (isset($_SESSION['account_loggedin'])) {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_SESSION['account_id'] ]);
    // Fetch the account from the database and return the result as an Array
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Update discount code
if (isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
    $_SESSION['discount'] = $_POST['discount_code'];
} else if (isset($_POST['discount_code']) && empty($_POST['discount_code']) && isset($_SESSION['discount'])) {
    unset($_SESSION['discount']);
}
// Variables
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0.00;
$shippingtotal = 0.00;
$discounttotal = 0.00;
$taxtotal = 0.00;
$selected_country = isset($_POST['address_country']) ? $_POST['address_country'] : $account['address_country'];
$selected_shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : null;
$selected_shipping_method_name = '';
$shipping_methods_available = [];
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include: IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img, (SELECT GROUP_CONCAT(pc.category_id) FROM products_categories pc WHERE pc.product_id = p.id) AS categories FROM products p WHERE p.id IN (' . $array_to_question_marks . ')');
    // We use the array_column to retrieve only the id's of the products
    $stmt->execute(array_column($products_in_cart, 'id'));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Retrieve the discount code
    if (isset($_SESSION['discount'])) {
        $stmt = $pdo->prepare('SELECT * FROM discounts WHERE discount_code = ?');
        $stmt->execute([ $_SESSION['discount'] ]);
        $discount = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Get tax
    $stmt = $pdo->prepare('SELECT * FROM taxes WHERE country = ?');
    $stmt->execute([ isset($_POST['address_country']) ? $_POST['address_country'] : $account['address_country'] ]);
    $tax = $stmt->fetch(PDO::FETCH_ASSOC);
    $tax_rate = $tax ? $tax['rate'] : 0.00;
    // Get the current date
    $current_date = strtotime((new DateTime())->format('Y-m-d H:i:s'));
    // Retrieve shipping methods
    $stmt = $pdo->query('SELECT * FROM shipping');
    $shipping_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Iterate the products in cart and add the meta data (product name, desc, etc)
    foreach ($products_in_cart as &$cart_product) {
        foreach ($products as $product) {
            if ($cart_product['id'] == $product['id']) {
                $cart_product['meta'] = $product;
                $product_weight = $cart_product['options_weight'];
                // Calculate the subtotal
                $product_price = (float)$cart_product['options_price'];
                $subtotal += $product_price * (int)$cart_product['quantity'];
                // Calculate the final price, which includes tax
                $cart_product['final_price'] = $product_price + (($tax_rate / 100) * $product_price);
                $taxtotal += (($tax_rate / 100) * $product_price) * (int)$cart_product['quantity'];
                // Calculate the shipping
                foreach ($shipping_methods as $shipping_method) {
                    if (empty($shipping_method['countries']) || in_array($selected_country, explode(',', $shipping_method['countries']))) {
                        if ($shipping_method['id'] == $selected_shipping_method && $product_price >= $shipping_method['price_from'] && $product_price <= $shipping_method['price_to'] && $product_weight >= $shipping_method['weight_from'] && $product_weight <= $shipping_method['weight_to']) {
                            if ($shipping_method['type'] == 'Single Product') {
                                $cart_product['shipping_price'] += (float)$shipping_method['price'] * (int)$cart_product['quantity'];
                                $shippingtotal += $cart_product['shipping_price'];
                            } else {
                                $cart_product['shipping_price'] = (float)$shipping_method['price'] / count($products_in_cart);
                                $shippingtotal = (float)$shipping_method['price'];
                            }
                            $shipping_methods_available[] = $shipping_method['id'];
                        } else if ($product_price >= $shipping_method['price_from'] && $product_price <= $shipping_method['price_to'] && $product_weight >= $shipping_method['weight_from'] && $product_weight <= $shipping_method['weight_to']) {
                            $shipping_methods_available[] = $shipping_method['id'];
                        }
                    }
                    if ($shipping_method['id'] == $selected_shipping_method) {
                        $selected_shipping_method_name = $shipping_method['name'];
                    }
                }
                // Check which products are eligible for a discount
                if (isset($discount) && $discount && $current_date >= strtotime($discount['start_date']) && $current_date <= strtotime($discount['end_date'])) {
                    // Check whether product list is empty or if product id is whitelisted
                    if (empty($discount['product_ids']) || in_array($product['id'], explode(',', $discount['product_ids']))) {
                        // Check whether category list is empty or if category id is whitelisted
                        if (empty($discount['category_ids']) || array_intersect(explode(',', $product['categories']), explode(',', $discount['category_ids']))) {
                            $cart_product['discounted'] = true;
                        }
                    }
                }
            }
        }
    }
    // Number of discounted products
    $num_discounted_products = count(array_column($products_in_cart, 'discounted'));
    // Iterate the products and update the price for the discounted products
    foreach ($products_in_cart as &$cart_product) {
        if (isset($cart_product['discounted']) && $cart_product['discounted']) {
            $price = &$cart_product['final_price'];
            if ($discount['discount_type'] == 'Percentage') {
                $d = (float)$price * ((float)$discount['discount_value'] / 100);
                $price -= $d;
                $discounttotal += $d * (int)$cart_product['quantity'];
            }
            if ($discount['discount_type'] == 'Fixed') {
                $d = (float)$discount['discount_value'] / $num_discounted_products;
                $price -= $d / (int)$cart_product['quantity'];
                $discounttotal += $d;
            }
        }
    }
}
// Make sure when the user submits the form all data was submitted and shopping cart is not empty
if (isset($_POST['method'], $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_SESSION['cart']) && !isset($_POST['update'])) {
    $account_id = null;
    // If the user is already logged in
    if (isset($_SESSION['account_loggedin'])) {
        // Account logged-in, update the user's details
        $stmt = $pdo->prepare('UPDATE accounts SET first_name = ?, last_name = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ? WHERE id = ?');
        $stmt->execute([ $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_SESSION['account_id'] ]);
        $account_id = $_SESSION['account_id'];
    } else if (isset($_POST['email'], $_POST['password'], $_POST['cpassword']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['password']) && !empty($_POST['cpassword'])) {
        // User is not logged in, check if the account already exists with the email they submitted
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ?');
        $stmt->execute([ $_POST['email'] ]);
    	if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            // Email exists, user should login instead...
    		$errors[] = 'Account already exists with that email!';
        }
        if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
            // Password must be between 5 and 20 characters long.
            $errors[] = 'Password must be between 5 and 20 characters long!';
    	}
        if ($_POST['password'] != $_POST['cpassword']) {
            // Password and confirm password fields do not match...
            $errors[] = 'Passwords do not match!';
        }
        if (!$errors) {
            // Hash the password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // Email doesnt exist, create new account
            $stmt = $pdo->prepare('INSERT INTO accounts (email, password, first_name, last_name, address_street, address_city, address_state, address_zip, address_country) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([ $_POST['email'], $password, $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'] ]);
            $account_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
            $stmt->execute([ $account_id ]);
            // Fetch the account from the database and return the result as an Array
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } else if (account_required) {
        $errors[] = 'Account creation required!';
    }
    if (!$errors && $products_in_cart) {
        // No errors, process the order
        
        // Process PayPal Payment
        if (paypal_enabled && $_POST['method'] == 'paypal') {
            // Process PayPal Checkout
            // Variable that will stored all details for all products in the shopping cart
            $data = [];
            // Add all the products that are in the shopping cart to the data array variable
            for ($i = 0; $i < count($products_in_cart); $i++) {
                $data['item_number_' . ($i+1)] = $products_in_cart[$i]['id'];
                $data['item_name_' . ($i+1)] = $products_in_cart[$i]['meta']['name'];
                $data['quantity_' . ($i+1)] = $products_in_cart[$i]['quantity'];
                $data['amount_' . ($i+1)] = $products_in_cart[$i]['final_price'];
                $data['on0_' . ($i+1)] = 'Options';
                $data['os0_' . ($i+1)] = $products_in_cart[$i]['options'];
            }
            // Metadata
            $metadata = [
                'account_id' => $account_id,
                'discount_code' => isset($_SESSION['discount']) ? $_SESSION['discount'] : '',
                'shipping_method' => $selected_shipping_method_name
            ];
            // Variables we need to pass to paypal
            $data = $data + [
                'cmd'			=> '_cart',
                'charset'		=> 'UTF-8',
                'upload'        => '1',
                'custom'        => json_encode($metadata),
                'business' 		=> paypal_email,
                'cancel_return'	=> paypal_cancel_url,
                'notify_url'	=> paypal_ipn_url,
                'currency_code'	=> paypal_currency,
                'return'        => paypal_return_url,
                'shipping_1'    => $shippingtotal,
                'address1'      => $_POST['address_street'],
                'city'          => $_POST['address_city'],
                'country'       => $_POST['address_country'],
                'state'         => $_POST['address_state'],
                'zip'           => $_POST['address_zip'],
                'first_name'    => $_POST['first_name'],
                'last_name'     => $_POST['last_name'],
                'email'         => isset($account['email']) && !empty($account['email']) ? $account['email'] : $_POST['email']
            ];
            if ($account_id != null) {
                // Log the user in with the details provided
                session_regenerate_id();
                $_SESSION['account_loggedin'] = TRUE;
                $_SESSION['account_id'] = $account_id;
                $_SESSION['account_role'] = $account ? $account['role'] : 'Member';
            }
            // Redirect the user to the PayPal checkout screen
            header('location:' . (paypal_testmode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr') . '?' . http_build_query($data));
            // End the script, don't need to execute anything else
            exit;
        }
        
        if (pay_on_delivery_enabled && $_POST['method'] == 'payondelivery') {
            // Process Normal Checkout
            // Generate unique transaction ID
            $transaction_id = strtoupper(uniqid('SC') . substr(md5(mt_rand()), 0, 5));
            // Insert transaction into database
            $stmt = $pdo->prepare('INSERT INTO transactions (txn_id, payment_amount, payment_status, created, payer_email, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, account_id, payment_method, shipping_method, shipping_amount, discount_code) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $transaction_id,
                ($subtotal-$discounttotal)+$shippingtotal+$taxtotal,
                default_payment_status,
                date('Y-m-d H:i:s'),
                isset($account['email']) && !empty($account['email']) ? $account['email'] : $_POST['email'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['address_street'],
                $_POST['address_city'],
                $_POST['address_state'],
                $_POST['address_zip'],
                $_POST['address_country'],
                $account_id,
                'website',
                $selected_shipping_method_name,
                $shippingtotal,
                isset($_SESSION['discount']) ? $_SESSION['discount'] : ''
            ]);
            // Get order ID
            $order_id = $pdo->lastInsertId();
            // Iterate products and deduct quantities
            foreach ($products_in_cart as $product) {
                // For every product in the shopping cart insert a new transaction into our database
                $stmt = $pdo->prepare('INSERT INTO transactions_items (txn_id, item_id, item_price, item_quantity, item_options) VALUES (?,?,?,?,?)');
                $stmt->execute([ $transaction_id, $product['id'], $product['final_price'], $product['quantity'], $product['options'] ]);
                // Update product quantity in the products table
                $stmt = $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE quantity > 0 AND id = ?');
                $stmt->execute([ $product['quantity'], $product['id'] ]);
                // Deduct option quantities
                if ($product['options']) {
                    $options = explode(',', $product['options']);
                    foreach ($options as $opt) {
                        $option_name = explode('-', $opt)[0];
                        $option_value = explode('-', $opt)[1];
                        $stmt = $pdo->prepare('UPDATE products_options SET quantity = quantity - ? WHERE quantity > 0 AND title = ? AND (name = ? OR name = "")');
                        $stmt->execute([ $product['quantity'], $option_name, $option_value ]);                
                    }
                }
            }
            // Authenticate the user
            if ($account_id != null) {
                // Log the user in with the details provided
                session_regenerate_id();
                $_SESSION['account_loggedin'] = TRUE;
                $_SESSION['account_id'] = $account_id;
                $_SESSION['account_role'] = $account ? $account['role'] : 'Member';
            }

            header('Location: ' . url('index.php?page=placeorder'));
            exit;
        }
    }
    // Preserve form details if the user encounters an error
    $account = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'address_street' => $_POST['address_street'],
        'address_city' => $_POST['address_city'],
        'address_state' => $_POST['address_state'],
        'address_zip' => $_POST['address_zip'],
        'address_country' => $_POST['address_country']
    ];
}
?>
<?=template_header('Checkout')?>

<div class="checkout content-wrapper">

    <h1>Checkout</h1>

    <p class="error"><?=implode('<br>', $errors)?></p>

    <?php if (!isset($_SESSION['account_loggedin'])): ?>
    <p>Already have an account? <a href="<?=url('index.php?page=myaccount')?>">Log In</a></p>
    <?php endif; ?>

    <form action="" method="post">

        <div class="container">

            <div class="shipping-details">

                <h2>Payment Method</h2>

                <div class="payment-methods">
                    <?php if (pay_on_delivery_enabled): ?>
                    <input id="payondelivery" type="radio" name="method" value="payondelivery" checked>
                    <label for="payondelivery">Pay on Delivery</label>
                    <?php endif; ?>

                    <?php if (paypal_enabled): ?>
                    <input id="paypal" type="radio" name="method" value="paypal">
                    <label for="paypal"><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal Logo"></label>
                    <?php endif; ?>

                    
                </div>

                <?php if (!isset($_SESSION['account_loggedin'])): ?>
                <h2>Create Account<?php if (!account_required): ?> (optional)<?php endif; ?></h2>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="john@example.com" class="form-field" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" class="form-field" autocomplete="new-password">

                <label for="cpassword">Confirm Password</label>
                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password" class="form-field" autocomplete="new-password">
                <?php endif; ?>

                <h2>Shipping Details</h2>

                <div class="row1">
                    <label for="first_name">First Name</label>
                    <input type="text" value="<?=htmlspecialchars($account['first_name'], ENT_QUOTES)?>" name="first_name" id="first_name" placeholder="John" class="form-field" required>
                </div>

                <div class="row2">
                    <label for="last_name">Last Name</label>
                    <input type="text" value="<?=htmlspecialchars($account['last_name'], ENT_QUOTES)?>" name="last_name" id="last_name" placeholder="Doe" class="form-field" required>
                </div>

                <label for="address_street">Address</label>
                <input type="text" value="<?=htmlspecialchars($account['address_street'], ENT_QUOTES)?>" name="address_street" id="address_street" placeholder="24 High Street" class="form-field" required>

                <label for="address_city">City</label>
                <input type="text" value="<?=htmlspecialchars($account['address_city'], ENT_QUOTES)?>" name="address_city" id="address_city" placeholder="New York" class="form-field" required>

                <div class="row1">
                    <label for="address_state">State</label>
                    <input type="text" value="<?=htmlspecialchars($account['address_state'], ENT_QUOTES)?>" name="address_state" id="address_state" placeholder="NY" class="form-field" required>
                </div>

                <div class="row2">
                    <label for="address_zip">Zip</label>
                    <input type="text" value="<?=htmlspecialchars($account['address_zip'], ENT_QUOTES)?>" name="address_zip" id="address_zip" placeholder="10001" class="form-field" required>
                </div>

                <label for="address_country">Country</label>
                <select name="address_country" class="ajax-update form-field" required>
                    <?php foreach(get_countries() as $country): ?>
                    <option value="<?=$country?>"<?=$country==$account['address_country']?' selected':''?>><?=$country?></option>
                    <?php endforeach; ?>
                </select>

            </div>

            <div class="cart-details">
                    
                <h2>Shopping Cart</h2>

                <table>
                    <?php foreach($products_in_cart as $product): ?>
                    <tr>
                        <td><img src="<?=$product['meta']['img']?>" width="35" height="35" alt="<?=$product['meta']['name']?>"></td>
                        <td><?=$product['quantity']?> x <?=$product['meta']['name']?></td>
                        <td class="price"><?=currency_code?><?=number_format($product['options_price'] * $product['quantity'],2)?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <div class="discount-code">
                    <input type="text" class="ajax-update form-field" name="discount_code" placeholder="Discount Code" value="<?=isset($_SESSION['discount']) ? $_SESSION['discount'] : ''?>">
                    <span class="result">
                        <?php if (isset($_SESSION['discount'], $discount) && !$discount): ?>
                        Incorrect discount code!
                        <?php elseif (isset($_SESSION['discount'], $discount) && $current_date < strtotime($discount['start_date'])): ?>
                        Incorrect discount code!  
                        <?php elseif (isset($_SESSION['discount'], $discount) && $current_date > strtotime($discount['end_date'])): ?>
                        Discount code expired!
                        <?php elseif (isset($_SESSION['discount'], $discount)): ?>
                        Discount code applied!
                        <?php endif; ?>
                    </span>
                </div>

                <div class="shipping-methods-container">
                    <?php if ($shipping_methods_available): ?>
                    <div class="shipping-methods">
                        <h3>Shipping Method</h3>
                        <?php foreach($shipping_methods as $k => $method): ?>
                        <?php if (!in_array($method['id'], $shipping_methods_available)) continue; ?>
                        <div class="shipping-method">
                            <input type="radio" class="ajax-update" id="sm<?=$k?>" name="shipping_method" value="<?=$method['id']?>" required<?=$selected_shipping_method==$method['id']?' checked':''?>>
                            <label for="sm<?=$k?>"><?=$method['name']?> (<?=currency_code?><?=number_format($method['price'], 2)?><?=$method['type']=='Single Product'?' per item':''?>)</label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="summary">
                    <div class="subtotal">
                        <span>Subtotal</span>
                        <span><?=currency_code?><?=number_format($subtotal,2)?></span>
                    </div>

                    <?php if ($tax): ?>
                    <div class="vat">
                        <span>VAT <span class="alt">(<?=$tax['rate']?>%)</span></span>
                        <span><?=currency_code?><?=number_format($taxtotal,2)?></span>
                    </div>
                    <?php endif; ?>

                    <div class="shipping">
                        <span>Shipping</span>
                        <span><?=currency_code?><?=number_format($shippingtotal,2)?></span>
                    </div>

                    <?php if ($discounttotal > 0): ?>
                    <div class="discount">
                        <span>Discount</span>
                        <span>-<?=currency_code?><?=number_format(round($discounttotal, 1),2)?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="total">
                    <span>Total <span class="alt">(VAT included)</span></span><span><?=currency_code?><?=number_format($subtotal-round($discounttotal,1)+$shippingtotal+$taxtotal,2)?></span>
                </div>

                <div class="buttons">
                    <button type="submit" name="checkout" class="btn">Place Order</button>
                </div>

            </div>

        </div>

    </form>

</div>

<?=template_footer()?>