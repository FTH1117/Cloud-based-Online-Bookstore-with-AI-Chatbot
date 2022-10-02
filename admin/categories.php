<?php
defined('admin') or exit;
// SQL query to get all categories from the "categories" table
$stmt = $pdo->prepare('SELECT * FROM categories');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Category created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Category updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Category deleted successfully!';
    }
}
// Populate categories function
function admin_populate_categories($categories, $parent_id = 0, $n = 0) {
    $html = '';
    foreach ($categories as $category) {
        if ($parent_id == $category['parent_id']) {
            $html .= '
            <tr>
                <td><span style="padding-right:8px;color:#bbbec0;border-left:1px solid #bbbec0;padding-bottom:2px;">-' . str_repeat('----', $n) . '</span>' . $category['name'] . '</td>
                <td><a href="index.php?page=category&id=' . $category['id'] . '" class="link1">Edit</a></td>
            </tr>           
            ';
            $html .= admin_populate_categories($categories, $category['id'], $n+1);
        }
    }
    return $html;
}
?>
<?=template_admin_header('Categories', 'categories')?>

<div class="content-title">
    <h2>Categories</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>


<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=category" class="btn">Create Category</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>Name</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no categories</td>
                </tr>
                <?php else: ?>
                <?=admin_populate_categories($categories)?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>