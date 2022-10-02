<?php
defined('admin') or exit;
// SQL query to get all taxes from the "taxes" table
$stmt = $pdo->prepare('SELECT * FROM taxes ORDER BY country ASC');
$stmt->execute();
$taxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Tax created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Tax updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Tax deleted successfully!';
    }
}
?>
<?=template_admin_header('Taxes', 'taxes')?>

<div class="content-title">
    <h2>Taxes</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=tax" class="btn">Create Tax</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden">#</td>
                    <td>Country</td>
                    <td>Tax Rate</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($taxes)): ?>
                <tr>
                    <td colspan="4" style="text-align:center;">There are no taxes</td>
                </tr>
                <?php else: ?>
                <?php foreach ($taxes as $tax): ?>
                <tr>
                    <td class="responsive-hidden"><?=$tax['id']?></td>
                    <td><?=$tax['country']?></td>
                    <td><?=$tax['rate']?>%</td>
                    <td><a href="index.php?page=tax&id=<?=$tax['id']?>" class="link1">Edit</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>