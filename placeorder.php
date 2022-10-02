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
    <h1>Your Order Has Been Placed</h1>
    <p>Thank you for ordering with us! We'll contact you by email with your order details.</p>
</div>
<?php endif; ?>

<?=template_footer()?>