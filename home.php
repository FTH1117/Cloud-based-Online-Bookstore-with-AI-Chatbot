<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get the 4 most recent added products
$stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM products p WHERE p.status = 1 ORDER BY p.date_added DESC LIMIT 8');
$stmt->execute();
$recently_added_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM products p  JOIN products_categories pc ON pc.product_id = p.id AND pc.category_id = 1 WHERE p.status = 1 ORDER BY p.date_added DESC LIMIT 4');
$stmt->execute();
$sales_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT 
    p.*, 
    (SELECT m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img 
FROM products p JOIN products_categories pcc ON pcc.category_id = (SELECT c.id FROM transactions_items ti JOIN products_categories pc ON pc.product_id = ti.item_id JOIN categories c ON c.id = pc.category_id ORDER BY ti.id DESC LIMIT 1) AND pcc.product_id = p.id
WHERE p.status = 1 
ORDER BY p.date_added DESC 
LIMIT 4');
$stmt->execute();
$recommend_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $search=$_POST['search'];
    header("Location: index.php?page=search&query=".$search);
    exit;
    
}


?>
<?=template_header('Home')?>

<div class="featured" style="background-image:url(<?=featured_image?>)">

    <h2>FTH BookStore</h2>

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
	
	<form method="POST" action="index.php?page=products">
<div class="buttons">
	<input type="submit" value="View More Products >>">
</div>
</form>
	
	<h2>Sales</h2>

    <div class="products">
        <?php foreach ($sales_products as $product): ?>
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
	
	<h2>Recommend to you</h2>

    <div class="products">
        <?php foreach ($recommend_products as $product): ?>
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


<div class="content content-wrapper">
	<br/>
	<div class="who">
	<h2>Who are Us?<br/>
	<p>
		AI Bookstore provides a platform for our customers to purchase any book they want. We will provide users with physical books and eBooks on this platform. 
		Users can search for their favorite data through the search function, or they can find the books they want based on their favorite category. 
		The system is a graduation project of Foong Tze Hing, a student of INTI International University. 
		The system was built using AWS as a cloud provider. Hopefully users will enjoy the system
	</p>
	</h2>
	</div>
	<div class="vision">
	<h2>Enjoy reading now!<br/>
	<p>
		So what are you waiting for? Add all the books you like to your cart and place your order. 
		Your physical books will be delivered to you within a week. 
		If you are purchasing an e-book, you can download the book resources after you place your order. 
		Enjoy the fast delivery and great customer service of AI Bookstore.
	</p>
	</h2>
	</div>
	</div>



<?=template_footer()?>