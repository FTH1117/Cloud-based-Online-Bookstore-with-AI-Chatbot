<?php
defined('admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','name','price','quantity','date_added','status'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (p.name LIKE :search) ' : '';
// Add filters
if ($status == 'one') {
    $where .= $where ? 'AND p.status = 1 ' : 'WHERE p.status = 1 ';
}
if ($status == 'zero') {
    $where .= $where ? 'AND p.status = 0 ' : 'WHERE p.status = 0 ';
}
if ($quantity == 'zero') {
    $where .= $where ? 'AND p.quantity = 0 ' : 'WHERE p.quantity = 0 ';
}
// Retrieve the total number of products
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM products p ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$products_total = $stmt->fetchColumn();
// SQL query to get all products from the "products" table
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(m2.full_path) AS imgs FROM products p LEFT JOIN (SELECT pm.id, pm.product_id, m.full_path FROM products_media pm JOIN media m ON m.id = pm.media_id GROUP BY pm.id, pm.product_id, m.full_path) m2 ON m2.product_id = p.id ' . $where . ' GROUP BY p.id, p.name, p.description, p.author,p.type,p.isbn,p.price, p.rrp, p.quantity, p.date_added, p.weight, p.url_slug, p.status ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete account
if (isset($_GET['delete'])) {
    // Delete the product
    $stmt = $pdo->prepare('DELETE p, pm, po, pc FROM products p LEFT JOIN products_media pm ON pm.product_id = p.id LEFT JOIN products_options po ON po.product_id = p.id LEFT JOIN products_categories pc ON pc.product_id = p.id WHERE p.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    // Clear session cart
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    header('Location: index.php?page=products&success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Product created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Product updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Product deleted successfully!';
    }
}
// Determine the URL
$url = 'index.php?page=products&search=' . $search . '&status=' . $status . '&quantity=' . $quantity;
?>
<?=template_admin_header('Products', 'products', 'view')?>

<div class="content-title">
    <h2>Products</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=product" class="btn">Create Product</a>
    <form action="" method="get">
        <input type="hidden" name="page" value="products">
        <div class="filters">
            <a href="#"><i class="fas fa-sliders-h"></i> Filters</a>
            <div class="list">
                <label><input type="checkbox" name="status" value="one"<?=$status=='one'?' checked':''?>>Enabled</label>
                <label><input type="checkbox" name="status" value="zero"<?=$status=='zero'?' checked':''?>>Disabled</label>
                <label><input type="checkbox" name="quantity" value="zero"<?=$quantity=='zero'?' checked':''?>>No Stock</label>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search product name..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?php if ($order_by=='id'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=name'?>">Name<?php if ($order_by=='name'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=price'?>">Price<?php if ($order_by=='price'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=quantity'?>">Quantity<?php if ($order_by=='quantity'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden">Images</td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=date_added'?>">Date Added<?php if ($order_by=='date_added'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=status'?>">Status<?php if ($order_by=='status'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no products</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="responsive-hidden"><?=$product['id']?></td>
                    <td><?=$product['name']?></td>
                    <?php if ($product['rrp'] == 0.00): ?>
                    <td><?=currency_code?><?=number_format($product['price'], 2)?></td>
                    <?php else: ?>
                    <td><span class="rrp"><?=currency_code?><?=number_format($product['price'], 2)?></span> <s><?=currency_code . number_format($product['rrp'], 2)?></s></td>
                    <?php endif; ?>
                    <td><?=$product['quantity']==-1?'--':number_format($product['quantity'])?></td>
                    <td class="responsive-hidden img">
                        <?php foreach (array_reverse(explode(',',$product['imgs'])) as $img): ?>
                        <?php if ($img): ?>
                        <img src="../<?=$img?>" width="32" height="32" alt="<?=$img?>">
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td class="responsive-hidden"><?=date('F j, Y', strtotime($product['date_added']))?></td>
                    <td class="responsive-hidden"><?=$product['status'] ? 'Enabled' : 'Disabled'?></td>
                    <td><a href="index.php?page=product&id=<?=$product['id']?>" class="link1">Edit</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($products_total / $results_per_page) == 0 ? 1 : ceil($products_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $products_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>