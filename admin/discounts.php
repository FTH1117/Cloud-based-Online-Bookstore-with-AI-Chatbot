<?php
defined('admin') or exit;
// SQL query to get all discounts from the "discounts" table
$stmt = $pdo->prepare('SELECT d.*, GROUP_CONCAT(DISTINCT p.name) product_names, GROUP_CONCAT(DISTINCT c.name) category_names FROM discounts d LEFT JOIN products p ON FIND_IN_SET(p.id, d.product_ids) LEFT JOIN categories c ON FIND_IN_SET(c.id, d.category_ids) GROUP BY d.id, d.category_ids, d.product_ids, d.discount_code, d.discount_type, d.discount_type, d.discount_value, d.start_date, d.end_date');
$stmt->execute();
$discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the current date
$current_date = strtotime((new DateTime())->format('Y-m-d H:i:s'));
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Discount created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Discount updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Discount deleted successfully!';
    }
}
?>
<?=template_admin_header('Discounts', 'discounts')?>

<div class="content-title">
    <h2>Discounts</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=discount" class="btn">Create Discount</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden">#</td>
                    <td>Code</td>
                    <td>Active</td>
                    <td class="responsive-hidden">Categories</td>
                    <td class="responsive-hidden">Products</td>
                    <td>Type</td>
                    <td>Value</td>
                    <td class="responsive-hidden">Start Date</td>
                    <td class="responsive-hidden">End Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($discounts)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no discounts</td>
                </tr>
                <?php else: ?>
                <?php foreach ($discounts as $discount): ?>
                <tr>
                    <td class="responsive-hidden"><?=$discount['id']?></td>
                    <td><?=$discount['discount_code']?></td>
                    <td><?=$current_date >= strtotime($discount['start_date']) && $current_date <= strtotime($discount['end_date']) ? 'Yes' : 'No'?></td>
                    <td class="responsive-hidden"><?=$discount['category_names'] ? str_replace(',', ', ', $discount['category_names']) : 'all'?></td>
                    <td class="responsive-hidden"><?=$discount['product_names'] ? str_replace(',', ', ', $discount['product_names']) : 'all'?></td>
                    <td><?=$discount['discount_type']?></td>
                    <td><?=$discount['discount_value']?></td>
                    <td class="responsive-hidden"><?=date('Y-m-d h:ia', strtotime($discount['start_date']))?></td>
                    <td class="responsive-hidden"><?=date('Y-m-d h:ia', strtotime($discount['end_date']))?></td>
                    <td><a href="index.php?page=discount&id=<?=$discount['id']?>" class="link1">Edit</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>