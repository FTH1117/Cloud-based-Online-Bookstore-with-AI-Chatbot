<?php
defined('admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$method = isset($_GET['method']) ? $_GET['method'] : '';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','first_name','total_products','payment_amount','payment_method','payment_status','created','payer_email'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'created';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (t.first_name LIKE :search OR t.last_name LIKE :search OR t.id LIKE :search OR t.txn_id LIKE :search OR t.payer_email LIKE :search) ' : '';
// Add filters

// Account ID filter
if ($account_id) $where .= $where ? 'AND account_id = :account_id ' : 'WHERE account_id = :account_id ';
// Retrieve the total number of products
$stmt = $pdo->prepare('SELECT COUNT(DISTINCT t.id) AS total FROM transactions t LEFT JOIN transactions_items ti ON ti.txn_id = t.txn_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
$orders_total = $stmt->fetchColumn();
// SQL query to get all products from the "products" table
$stmt = $pdo->prepare('SELECT t.*, COUNT(ti.id) AS total_products FROM transactions t LEFT JOIN transactions_items ti ON ti.txn_id = t.txn_id ' . $where . ' GROUP BY t.id, t.txn_id, t.payment_amount, t.payment_status, t.created, t.payer_email, t.first_name, t.last_name, t.address_street, t.address_city, t.address_state, t.address_zip, t.address_country, t.account_id, t.payment_method, t.discount_code, t.shipping_method, t.shipping_amount ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($account_id) $stmt->bindParam('account_id', $account_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Determine the URL
$url = 'index.php?page=orders&search=' . $search . '&status=' . $status . '&method=' . $method . '&account_id=' . $account_id;
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Order created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Order updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Order deleted successfully!';
    }
}
?>
<?=template_admin_header('Orders', 'orders')?>

<div class="content-title">
    <h2>Orders</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=order_manage" class="btn">Create Order</a>
    <form action="" method="get">
        <input type="hidden" name="page" value="orders">
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search order..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
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
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?php if ($order_by=='id'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=first_name'?>">Customer<?php if ($order_by=='first_name'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=payer_email'?>">Email<?php if ($order_by=='payer_email'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_products'?>">Products<?php if ($order_by=='total_products'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=payment_amount'?>">Total<?php if ($order_by=='payment_amount'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=payment_method'?>">Method<?php if ($order_by=='payment_method'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=payment_status'?>">Status<?php if ($order_by=='payment_status'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=created'?>">Date<?php if ($order_by=='created'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="9" style="text-align:center;">There are no orders</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $i): ?>
                <tr>
                    <td><?=$i['id']?></td>
                    <td><?=htmlspecialchars($i['first_name'], ENT_QUOTES)?> <?=htmlspecialchars($i['last_name'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><?=htmlspecialchars($i['payer_email'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><?=$i['total_products']?></td>
                    <td><?=currency_code?><?=number_format($i['payment_amount'], 2)?></td>
                    <td class="responsive-hidden"><?=$i['payment_method']?></td>
                    <td class="responsive-hidden"><?=$i['payment_status']?></td>
                    <td class="responsive-hidden"><?=date('F j, Y', strtotime($i['created']))?></td>
                    <td><a href="index.php?page=order&id=<?=$i['id']?>" class="link1">View</a> <a href="index.php?page=order_manage&id=<?=$i['id']?>" class="link1">Edit</a></td>
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
    <span>Page <?=$pagination_page?> of <?=ceil($orders_total / $results_per_page) == 0 ? 1 : ceil($orders_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $orders_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>