<?php
defined('admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','email','first_name','role','registered'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (a.email LIKE :search OR a.first_name LIKE :search OR a.last_name LIKE :search) ' : '';
// Retrieve the total number of products
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM accounts a ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$accounts_total = $stmt->fetchColumn();
// SQL query to get all products from the "products" table
$stmt = $pdo->prepare('SELECT a.*, count(t.id) AS orders FROM accounts a LEFT JOIN transactions t ON t.account_id = a.id ' . $where . ' GROUP BY a.id, a.email, a.password, a.role, a.first_name, a.last_name, a.address_street, a.address_city, a.address_state, a.address_zip, a.address_country, a.registered ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Account created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Account updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Account deleted successfully!';
    }
}
// Determine the URL
$url = 'index.php?page=accounts&search=' . $search;
?>
<?=template_admin_header('Accounts', 'accounts', 'view')?>

<div class="content-title">
    <h2>Accounts</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>


<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=account" class="btn">Create Account</a>
    <form action="" method="get">
        <input type="hidden" name="page" value="accounts">
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search account..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
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
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=email'?>">Email<?php if ($order_by=='email'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=first_name'?>">Name<?php if ($order_by=='first_name'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden">Address</td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=role'?>">Role<?php if ($order_by=='role'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden">Orders Placed</td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=registered'?>">Registered Date<?php if ($order_by=='registered'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no accounts</td>
                </tr>
                <?php else: ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td class="responsive-hidden"><?=$account['id']?></td>
                    <td><?=htmlspecialchars($account['email'], ENT_QUOTES)?></td>
                    <td><?=htmlspecialchars($account['first_name'], ENT_QUOTES)?> <?=htmlspecialchars($account['last_name'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden">
                        <?=htmlspecialchars($account['address_street'], ENT_QUOTES)?><?=$account['address_street']?', ':''?>
                        <?=htmlspecialchars($account['address_city'], ENT_QUOTES)?><?=$account['address_city']?', ':''?>
                        <?=htmlspecialchars($account['address_state'], ENT_QUOTES)?><?=$account['address_state']?', ':''?>
                        <?=htmlspecialchars($account['address_zip'], ENT_QUOTES)?><?=$account['address_zip']?', ':''?>
                        <?=htmlspecialchars($account['address_country'], ENT_QUOTES)?>
                    </td>
                    <td class="responsive-hidden"><?=$account['role']?></td>
                    <td class="responsive-hidden"><a href="index.php?page=orders&account_id=<?=$account['id']?>" class="link1"><?=number_format($account['orders'])?></a></td>
                    <td class="responsive-hidden"><?=date('F j, Y', strtotime($account['registered']))?></td>
                    <td><a href="index.php?page=account&id=<?=$account['id']?>" class="link1">Edit</a></td>
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
    <span>Page <?=$pagination_page?> of <?=ceil($accounts_total / $results_per_page) == 0 ? 1 : ceil($accounts_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $accounts_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>