<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Remove the product from the shopping cart
    array_splice($_SESSION['cart'], $_GET['remove'], 1);
    header('Location: ' . url('index.php?page=cart'));
    exit;
}
// Empty the cart
if (isset($_POST['emptycart']) && isset($_SESSION['cart'])) {
    // Remove all products from the shopping cart
    unset($_SESSION['cart']);
    header('Location: ' . url('index.php?page=cart'));
    exit;
}
// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if ((isset($_POST['update']) || isset($_POST['checkout'])) && isset($_SESSION['cart'])) {
    // Iterate the post data and update quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            // abs() function will prevent minus quantity and (int) will ensure the value is an integer (number)
            $quantity = abs((int)$v);
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    }
    // Send the user to the place order page if they click the Place Order button, also the cart should not be empty
    if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
        header('Location: ' . url('index.php?page=checkout'));
        exit;
    }
    header('Location: ' . url('index.php?page=cart'));
    exit;
}
// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include: IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    // Prepare SQL statement
    // $stmt = $pdo->prepare('SELECT p.id, pc.category_id, p.* FROM products p LEFT JOIN products_categories pc ON p.id = pc.product_id LEFT JOIN categories c ON c.id = pc.category_id WHERE p.id IN (' . $array_to_question_marks . ') GROUP BY p.id');
    $stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM products p WHERE p.id IN (' . $array_to_question_marks . ')');
    // Leverage the array_column function to retrieve only the id's of the products
    $stmt->execute(array_column($products_in_cart, 'id'));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Iterate the products in cart and add the meta data (product name, desc, etc)
    foreach ($products_in_cart as &$cart_product) {
        foreach ($products as $product) {
            if ($cart_product['id'] == $product['id']) {
                $cart_product['meta'] = $product;
                // Calculate the subtotal
                $subtotal += (float)$cart_product['options_price'] * (int)$cart_product['quantity'];
            }
        }
    }
}
?>
<?=template_header('Shopping Cart')?>

<div class="cart content-wrapper">

    <h1>Shopping Cart</h1>

    <form action="" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td class="rhide"></td>
                    <td class="rhide">Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products_in_cart)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">You have no products added in your Shopping Cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products_in_cart as $num => $product): ?>
                <tr>
                    <td class="img">
                        <?php if (!empty($product['meta']['img']) && file_exists($product['meta']['img'])): ?>
                        <a href="<?=url('index.php?page=product&id=' . $product['id'])?>">
                            <img src="<?=base_url?><?=$product['meta']['img']?>" width="50" height="50" alt="<?=$product['meta']['name']?>">
                        </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?=url('index.php?page=product&id=' . $product['id'])?>"><?=$product['meta']['name']?></a>
                        <br>
                        <a href="<?=url('index.php?page=cart&remove=' . $num)?>" class="remove">Remove</a>
                    </td>
                    <td class="options rhide">
                        <?=htmlspecialchars(str_replace(',', ', ', $product['options']), ENT_QUOTES)?>
                        <input type="hidden" name="options" value="<?=htmlspecialchars($product['options'], ENT_QUOTES)?>">
                    </td>
                    <td class="price rhide"><?=currency_code?><?=number_format($product['options_price'],2)?></td>
                    <td class="quantity">
                        <input type="number" class="ajax-update" name="quantity-<?=$num?>" value="<?=$product['quantity']?>" min="1" <?php if ($product['meta']['quantity'] != -1): ?>max="<?=$product['meta']['quantity']?>"<?php endif; ?> placeholder="Quantity" required>
                    </td>
                    <td class="price product-total"><?=currency_code?><?=number_format($product['options_price'] * $product['quantity'],2)?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total">
            <span class="text">Subtotal</span>
            <span class="price"><?=currency_code?><?=number_format($subtotal,2)?></span>
            <span class="note">(shipping and tax calculated at checkout)</span>
        </div>

        <div class="buttons">
            <input type="submit" value="Update" name="update" class="btn">
            <input type="submit" value="Empty Cart" name="emptycart" class="btn">
            <input type="submit" value="Checkout" name="checkout" class="btn">
        </div>

    </form>

</div>

<?=template_footer()?>