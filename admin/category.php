<?php
defined('admin') or exit;
// Default input category values
$category = [
    'name' => '',
    'parent_id' => 0
];
if (isset($_GET['id'])) {
    // Retrieve all the categories
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id != ?');
    $stmt->execute([ $_GET['id'] ]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing category
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the category
        $stmt = $pdo->prepare('UPDATE categories SET name = ?, parent_id = ? WHERE id = ?');
        $stmt->execute([ $_POST['name'], $_POST['parent_id'], $_GET['id'] ]);
        header('Location: index.php?page=categories&success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the category
        $stmt = $pdo->prepare('DELETE c, pc FROM categories c LEFT JOIN products_categories pc ON pc.category_id = c.id WHERE c.id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: index.php?page=categories&success_msg=3');
        exit;
    }
    // Get the category from the database
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Retrieve all the categories
    $stmt = $pdo->prepare('SELECT * FROM categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Create a new category
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO categories (name,parent_id) VALUES (?,?)');
        $stmt->execute([ $_POST['name'], $_POST['parent_id'] ]);
        header('Location: index.php?page=categories&success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Category', 'categories', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Category</h2>
        <a href="index.php?page=categories" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this category?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="content-block">
        <div class="form responsive-width-100">

            <label for="name"><i class="required">*</i> Name</label>
            <input id="name" type="text" name="name" placeholder="Name" value="<?=$category['name']?>" required>


        </div>
    </div>

</form>

<?=template_admin_footer()?>