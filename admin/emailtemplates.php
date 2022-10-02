<?php
defined('admin') or exit;
// Capture post data
if (isset($_POST['emailtemplate'], $_POST['emailtemplate2'])) {
    // Save templates
    file_put_contents('../order-details-template.php', $_POST['emailtemplate']);
    file_put_contents('../order-notification-template.php', $_POST['emailtemplate2']);
    header('Location: index.php?page=emailtemplates&success_msg=1');
    exit;
}
// Read the order details template PHP file
$contents = file_get_contents('../order-details-template.php');
// Read the order notification template PHP file
$contents2 = file_get_contents('../order-notification-template.php');
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Settings updated successfully!';
    }
}
?>
<?=template_admin_header('Email Templates', 'emailtemplates')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100">Email Templates</h2>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <?php if (isset($success_msg)): ?>
    <div class="msg success">
        <i class="fas fa-check-circle"></i>
        <p><?=$success_msg?></p>
        <i class="fas fa-times"></i>
    </div>
    <?php endif; ?>

    <div class="content-block">
        <div class="form responsive-width-100">
            <label for="emailtemplate">Order Details Template:</label>
            <textarea name="emailtemplate" id="emailtemplate"><?=$contents?></textarea>

            <label for="emailtemplate2">Order Notification Template:</label>
            <textarea name="emailtemplate2" id="emailtemplate2"><?=$contents2?></textarea>
        </div>
    </div>

</form>

<?=template_admin_footer()?>