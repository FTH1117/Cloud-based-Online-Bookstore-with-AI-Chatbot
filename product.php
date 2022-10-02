<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM products WHERE status = 1 AND (id = ? OR url_slug = ?)');
    $stmt->execute([ $_GET['id'], $_GET['id'] ]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Output simple error if the id for the product doesn't exists (array is empty)
        http_response_code(404);
        exit('Product does not exist!');
    }
    // Select the product images (if any) from the products_images table
    $stmt = $pdo->prepare('SELECT m.*, pm.position FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = ? ORDER BY pm.position ASC');
    $stmt->execute([ $product['id'] ]);
    // Fetch the product images from the database and return the result as an Array
    $product_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Select the product options (if any) from the products_options table
    $stmt = $pdo->prepare('SELECT CONCAT(title, "::", type, "::", required) AS k, name, quantity, price, price_modifier, weight, weight_modifier, type, required FROM products_options WHERE product_id = ? ORDER BY position ASC');
    $stmt->execute([ $product['id'] ]);
    // Fetch the product options from the database and return the result as an Array
    $product_options = $stmt->fetchAll(PDO::FETCH_GROUP);
    // Add the HTML meta data (for SEO purposes)
    $meta = '
        <meta property="og:url" content="' . url('index.php?page=product&id=' . ($product['url_slug'] ? $product['url_slug']  : $product['id'])) . '">
        <meta property="og:title" content="' . $product['name'] . '">
    ';
    if (isset($product_media[0]) && file_exists($product_media[0]['full_path'])) {
        $meta .= '<meta property="og:image" content="' . base_url . $product_media[0]['full_path'] . '">';
    }
    // If the user clicked the add to cart button
    if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        // abs() function will prevent minus quantity and (int) will ensure the value is an integer (number)
        $quantity = abs((int)$_POST['quantity']);
        // Get product options
        $options = '';
        $options_price = (float)$product['price'];
        $options_weight = (float)$product['weight'];
        // Iterate post data
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'option-') !== false) {
                if (is_array($v)) {
                    // Option is checkbox or radio element
                    foreach ($v as $vv) {
                        if (empty($vv)) continue;
                        $options .= str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $vv . ',';
                        $stmt = $pdo->prepare('SELECT * FROM products_options WHERE title = ? AND name = ? AND product_id = ?');
                        $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $vv, $product['id'] ]);
                        $option = $stmt->fetch(PDO::FETCH_ASSOC);
                        $options_price = $option['price_modifier'] == 'add' ? $options_price + $option['price'] : $options_price - $option['price'];
                        $options_weight = $option['weight_modifier'] == 'add' ? $options_weight + $option['weight'] : $options_weight - $option['weight'];
                    }
                } else {
                    if (empty($v)) continue;
                    $options .= str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $v . ',';
                    $stmt = $pdo->prepare('SELECT * FROM products_options WHERE title = ? AND name = ? AND product_id = ?');
                    $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $v, $product['id'] ]);
                    $option = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$option) {
                        // Option is text or datetime element
                        $stmt = $pdo->prepare('SELECT * FROM products_options WHERE title = ? AND product_id = ?');
                        $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $product['id'] ]);
                        $option = $stmt->fetch(PDO::FETCH_ASSOC);                              
                    }
                    $options_price = $option['price_modifier'] == 'add' ? $options_price + $option['price'] : $options_price - $option['price'];
                    $options_weight = $option['weight_modifier'] == 'add' ? $options_weight + $option['weight'] : $options_weight - $option['weight'];
                }
            }
        }
        $options_price = $options_price < 0 ? 0 : $options_price;
        $options = rtrim($options, ',');
        // Check if the product exists (array is not empty)
        if ($quantity > 0) {
            // Product exists in database, now we can create/update the session variable for the cart
            if (!isset($_SESSION['cart'])) {
                // Shopping cart session variable doesnt exist, create it
                $_SESSION['cart'] = [];
            }
            $cart_product = &get_cart_product($product['id'], $options);
            if ($cart_product) {
                // Product exists in cart, update the quanity
                $cart_product['quantity'] += $quantity;
            } else {
                // Product is not in cart, add it
                $_SESSION['cart'][] = [
                    'id' => $product['id'],
                    'quantity' => $quantity,
                    'options' => $options,
                    'options_price' => $options_price,
                    'options_weight' => $options_weight,
                    'shipping_price' => 0.00
                ];
            }
        }
        // Prevent form resubmission...
        header('Location: ' . url('index.php?page=cart'));
        exit;
    }
} else {
    // Output simple error if the id wasn't specified
    http_response_code(404);
    exit('Product does not exist!');
}
?>
<?=template_header($product['name'], $meta)?>

<?php if ($error): ?>

<p class="content-wrapper error"><?=$error?></p>

<?php else: ?>

<div class="product content-wrapper">

    <div class="product-imgs">

        <?php if (isset($product_media[0]) && file_exists($product_media[0]['full_path'])): ?>
        <div class="product-img-large">
            <img src="<?=base_url . $product_media[0]['full_path']?>" alt="<?=$product_media[0]['caption']?>">
        </div>
        <?php endif; ?>

        <div class="product-small-imgs">
            <?php foreach ($product_media as $media): ?>
            <div class="product-img-small<?=$media['position']==1?' selected':''?>">
                <img src="<?=base_url . $media['full_path'] ?>" width="150" height="150" alt="<?=$media['caption']?>">
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <div class="product-wrapper">

        <h1 class="name"><?=$product['name']?></h1>

        <div class="prices">
            <span class="price" data-price="<?=$product['price']?>"><?=currency_code?><?=number_format($product['price'],2)?></span>
            <?php if ($product['rrp'] > 0): ?>
            <span class="rrp"><?=currency_code?><?=number_format($product['rrp'],2)?></span>
            <?php endif; ?>
        </div>
        <br>
        <div class="type">
            <?php if($product['type']==1):?>
            <label for="type">Physical book</label>
            <?php endif; ?>
            <?php if($product['type']==0):?>
            <label for="type">E-book</label>
            <?php endif; ?>
        </div>
        
        <div class="author">
            <br>
            <label for="author">Author: </label>
            <?=$product['author']?>
        </div>

        <div class="isbn">
            <br>
            <label for="isbn">ISBN: </label>
            <?=$product['isbn']?>
        </div>

        <form id="product-form" action="" method="post">
            <?php foreach ($product_options as $id => $option): ?>
            <?php $id = explode('::', $id); ?>
            <?php if ($id[1] == 'select'): ?>
            <label for="<?=$id[0]?>"><?=$id[0]?></label>
            <select id="<?=$id[0]?>" class="option select" name="option-<?=$id[0]?>"<?=$id[2] ? ' required' : ''?>>
                <option value="" selected disabled style="display:none"><?=$id[0]?></option>
                <?php foreach ($option as $option_value): ?>
                <option value="<?=$option_value['name']?>" data-price="<?=$option_value['price']?>" data-modifier="<?=$option_value['price_modifier']?>"<?=$option_value['quantity']==0?' disabled':''?>><?=$option_value['name']?></option>
                <?php endforeach; ?>
            </select>
            <?php elseif ($id[1] == 'radio'): ?>
            <label for="<?=$id[0]?>"><?=$id[0]?></label>
            <div class="radio-checkbox">
                <?php foreach ($option as $n => $option_value): ?>
                <label>
                    <input class="option radio" value="<?=$option_value['name']?>" name="option-<?=$id[0]?>" type="radio" data-price="<?=$option_value['price']?>" data-modifier="<?=$option_value['price_modifier']?>"<?=$id[2] && $n == 0 ? ' required' : ''?><?=$option_value['quantity']==0?' disabled':''?>><?=$option_value['name']?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php elseif ($id[1] == 'checkbox'): ?>
            <label for="<?=$id[0]?>"><?=$id[0]?></label>
            <div class="radio-checkbox">
                <?php foreach ($option as $n => $option_value): ?>
                <label>
                    <input class="option checkbox" value="<?=$option_value['name']?>" name="option-<?=$id[0]?>[]" type="checkbox" data-price="<?=$option_value['price']?>" data-modifier="<?=$option_value['price_modifier']?>"<?=$id[2] && $n == 0 ? ' required' : ''?><?=$option_value['quantity']==0?' disabled':''?>><?=$option_value['name']?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php elseif ($id[1] == 'text'): ?>
            <?php foreach ($option as $option_value): ?>
            <label for="<?=$id[0]?>"><?=$id[0]?></label>
            <input id="<?=$id[0]?>" class="option text" name="option-<?=$id[0]?>" type="text" placeholder="<?=$option_value['name']?>" data-price="<?=$option_value['price']?>" data-modifier="<?=$option_value['price_modifier']?>"<?=$id[2] ? ' required' : ''?><?=$option_value['quantity']==0?' disabled':''?>>
            <?php endforeach; ?>
            <?php elseif ($id[1] == 'datetime'): ?>
            <?php foreach ($option as $option_value): ?>
            <label for="<?=$id[0]?>"><?=$id[0]?></label>
            <input id="<?=$id[0]?>" class="option datetime" name="option-<?=$id[0]?>" type="datetime-local"<?=$option_value['name'] ? 'value="' . date('Y-m-d\TH:i', strtotime($product['date_added'])) . '" ' : ''?> data-price="<?=$option_value['price']?>" data-modifier="<?=$option_value['price_modifier']?>"<?=$id[2] ? ' required' : ''?><?=$option_value['quantity']==0?' disabled':''?>>
            <?php endforeach; ?>          
            <?php endif; ?>
            <?php endforeach; ?>
            <label for="quantity">Quantity</label>
            <input id="quantity" type="number" name="quantity" value="1" min="1"<?php if ($product['quantity'] != -1): ?> max="<?=$product['quantity']?>"<?php endif; ?> placeholder="Quantity" required>
            <?php if ($product['quantity'] == 0): ?>
            <input type="submit" value="Out of Stock" class="btn" disabled>
            <?php else: ?>
            <input type="submit" value="Add To Cart" class="btn">
            <?php endif; ?>
        </form>

        

        <div class="description">
            <?=$product['description']?>
        </div>

    </div>

</div>

<?php endif; ?>

<?=template_footer()?>