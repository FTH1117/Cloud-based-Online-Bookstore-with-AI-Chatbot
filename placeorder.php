<?php
// Prevent direct access to file
defined('shoppingcart') or exit;
// Remove all the products in cart, the variable is no longer needed as the order has been processed
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}
// Remove discount code
if (isset($_SESSION['discount'])) {
    unset($_SESSION['discount']);
}

?>
<?=template_header('Place Order')?>

<?php if ($error): ?>
<p class="content-wrapper error"><?=$error?></p>
<?php else: ?>
<div class="placeorder content-wrapper">
    <h1>Orders have been placed</h1>
    <p>Thank you for ordering with us!</p>
	<p>If you purchase a E-book, please <a href="index.php?page=myaccount&tab=downloads" target="_blank">CLICK HERE</a>.</p>	
</div>
<?php endif; ?>

<?=template_footer()?>