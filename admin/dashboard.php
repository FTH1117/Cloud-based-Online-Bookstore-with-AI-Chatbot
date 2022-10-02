<?php
defined('admin') or exit;
// SQL query that will get all orders and sort by the date created
$stmt = $pdo->prepare('SELECT t.*, COUNT(ti.id) AS total_products FROM transactions t JOIN transactions_items ti ON ti.txn_id = t.txn_id WHERE cast(t.created as DATE) = cast(now() as DATE) GROUP BY t.id, t.txn_id, t.payment_amount, t.payment_status, t.created, t.payer_email, t.first_name, t.last_name, t.address_street, t.address_city, t.address_state, t.address_zip, t.address_country, t.account_id, t.payment_method, t.discount_code, t.shipping_method, t.shipping_amount ORDER BY t.created DESC');
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the orders statistics
$stmt = $pdo->prepare('SELECT SUM(payment_amount) AS earnings FROM transactions WHERE cast(created as DATE) = cast(now() as DATE)');
$stmt->execute();
$order_stats = $stmt->fetch(PDO::FETCH_ASSOC);;
// Get the total number of accounts
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM accounts');
$stmt->execute();
$accounts = $stmt->fetch(PDO::FETCH_ASSOC);
// Get the total number of products
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM products');
$stmt->execute();
$products = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?=template_admin_header('Dashboard', 'dashboard')?>

<div class="content-title">
    <h2>Dashboard</h2>
</div>

<div class="dashboard">
    <div class="content-block stat">
        <div class="data">
            <h3>New Orders</h3>
            <p><?=number_format(count($orders))?></p>
        </div>
        <i class="fas fa-shopping-cart"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total orders for today
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>New Sales</h3>
            <p><?=currency_code?><?=number_format($order_stats['earnings'], 2)?></p>
        </div>
        <i class="fas fa-coins"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total earnings for today
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Total Accounts</h3>
            <p><?=number_format($accounts['total'])?></p>
        </div>
        <i class="fas fa-users"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total accounts
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Total Products</h3>
            <p><?=number_format($products['total'])?></p>
        </div>
        <i class="fas fa-boxes"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total products
        </div>
    </div>
</div>

<div class="content-title">
    <h2>Today's Transactions</h2>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Customer</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Products</td>
                    <td>Total</td>
                    <td class="responsive-hidden">Method</td>
                    <td class="responsive-hidden">Status</td>
                    <td class="responsive-hidden">Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="9" style="text-align:center;">There are no recent orders</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?=$order['id']?></td>
                    <td><?=htmlspecialchars($order['first_name'], ENT_QUOTES)?> <?=htmlspecialchars($order['last_name'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><?=htmlspecialchars($order['payer_email'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden"><?=$order['total_products']?></td>
                    <td><?=currency_code?><?=number_format($order['payment_amount'], 2)?></td>
                    <td class="responsive-hidden"><?=$order['payment_method']?></td>
                    <td class="responsive-hidden"><?=$order['payment_status']?></td>
                    <td class="responsive-hidden"><?=date('F j, Y', strtotime($order['created']))?></td>
                    <td><a href="index.php?page=order&id=<?=$order['id']?>" class="link1">View</a> <a href="index.php?page=order_manage&id=<?=$order['id']?>" class="link1">Edit</a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>