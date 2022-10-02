<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
// Get the 4 most recent added products
$stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM products p WHERE p.status = 1 ORDER BY p.date_added DESC LIMIT 8');
$stmt->execute();
$recently_added_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $search=$_POST['search'];
    header("Location: index.php?page=search&query=".$search);
    exit;
    
}


?>
<?=template_header('Home')?>

<div class="featured" style="background-image:url(<?=featured_image?>)">

    <h2>AI BookStore</h2>

    <p>A book holds a house of gold</p><br>

    <div class="search">
    <form action="" method="post" >
    <input id="search" type="text" name="search" placeholder="Input keyword" required>
    <input type="submit" name="submit" value="Search" class="btn">
    </form>
    </div>

</div>

<div class="recentlyadded content-wrapper">

    <h2>New Arrivals Books</h2>

    <div class="products">
        <?php foreach ($recently_added_products as $product): ?>
        <a href="<?=url('index.php?page=product&id=' . ($product['url_slug'] ? $product['url_slug']  : $product['id']))?>" class="product">
            <?php if (!empty($product['img']) && file_exists($product['img'])): ?>
            <img src="<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
            <?php endif; ?>
            <span class="name"><?=$product['name']?></span>
            <span class="price">
                <?=currency_code?><?=number_format($product['price'],2)?>
                <?php if ($product['rrp'] > 0): ?>
                <span class="rrp"><?=currency_code?><?=number_format($product['rrp'],2)?></span>
                <?php endif; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

</div>

<?=template_footer()?>