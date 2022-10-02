<?php
defined('admin') or exit;
// SQL query to get all shipping methods from the "shipping" table
$stmt = $pdo->prepare('SELECT * FROM shipping');
$stmt->execute();
$shipping = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Shipping method created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Shipping method updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Shipping method deleted successfully!';
    }
}
?>
<?=template_admin_header('Shipping', 'shipping')?>

<div class="content-title">
    <h2>Shipping</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=shipping_process" class="btn">Create Shipping Method</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Name</td>
                    <td>Type</td>
                    <td class="responsive-hidden">Countries</td>
                    <td class="responsive-hidden">Price Range</td>
                    <td class="responsive-hidden">Weight Range</td>
                    <td>Total Shipping Price</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shipping)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no shipping methods</td>
                </tr>
                <?php else: ?>
                <?php foreach ($shipping as $s): ?>
                <tr>
                    <td><?=$s['id']?></td>
                    <td><?=$s['name']?></td>
                    <td><?=$s['type']?></td>
                    <td class="responsive-hidden" style="max-width:300px"><?=$s['countries'] ? str_replace(',', ', ', $s['countries']) : 'all'?></td>
                    <td class="responsive-hidden"><?=currency_code?><?=number_format($s['price_from'], 2)?> - <?=currency_code?><?=number_format($s['price_to'], 2)?></td>
                    <td class="responsive-hidden"><?=number_format($s['weight_from'], 2)?> lbs - <?=number_format($s['weight_to'], 2)?> lbs</td>
                    <td><?=currency_code?><?=number_format($s['price'], 2)?></td>
                    <td><a href="index.php?page=shipping_process&id=<?=$s['id']?>" class="link1">Edit</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>