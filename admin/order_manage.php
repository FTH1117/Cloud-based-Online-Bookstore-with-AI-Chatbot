<?php
defined('admin') or exit;
// Default transaction values
$transaction = [
    'txn_id' => '',
    'payment_amount' => '',
    'payment_status' => '',
    'payer_email' => '',
    'first_name' => '',
    'last_name' => '',
    'account_id' => '',
    'payment_method' => '',
    'discount_code' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => '',
    'shipping_method' => '',
    'shipping_amount' => '',
    'created' => date('Y-m-d\TH:i')
];
// Retrieve the products from the database
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY id');
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retrieve the accounts from the database
$stmt = $pdo->prepare('SELECT * FROM accounts ORDER BY id');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Add transactions items to the database
function addOrderItems($pdo, $txn_id) {
    if (isset($_POST['item_id']) && is_array($_POST['item_id']) && count($_POST['item_id']) > 0) {
        // Iterate items
        $delete_list = [];
        for ($i = 0; $i < count($_POST['item_id']); $i++) {
            // If the item doesnt exist in the database
            if (!intval($_POST['item_id'][$i])) {
                // Insert new item
                $stmt = $pdo->prepare('INSERT INTO transactions_items (txn_id,item_id,item_price,item_quantity,item_options) VALUES (?,?,?,?,?)');
                $stmt->execute([ $txn_id, $_POST['item_product'][$i], $_POST['item_price'][$i], $_POST['item_quantity'][$i], $_POST['item_options'][$i] ]);
                $delete_list[] = $pdo->lastInsertId();
            } else {
                // Update existing item
                $stmt = $pdo->prepare('UPDATE transactions_items SET txn_id = ?, item_id = ?, item_price = ?, item_quantity = ?, item_options = ? WHERE id = ?');
                $stmt->execute([ $txn_id, $_POST['item_product'][$i], $_POST['item_price'][$i], $_POST['item_quantity'][$i], $_POST['item_options'][$i], $_POST['item_id'][$i] ]);    
                $delete_list[] = $_POST['item_id'][$i];          
            }
        }
        // Delete item
        $in  = str_repeat('?,', count($delete_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM transactions_items WHERE txn_id = ? AND id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $txn_id ], $delete_list));
    } else {
        // No item exists, delete all
        $stmt = $pdo->prepare('DELETE FROM transactions_items WHERE txn_id = ?');
        $stmt->execute([ $txn_id ]);       
    }
}
// Save captured data
if (isset($_GET['id'])) {
    // Retrieve the transaction from the database
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    // Retrieve the transaction items from the database
    $stmt = $pdo->prepare('SELECT * FROM transactions_items WHERE txn_id = ?');
    $stmt->execute([ $transaction['txn_id'] ]);
    $transactions_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing transaction
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the transaction
        $stmt = $pdo->prepare('UPDATE transactions SET txn_id = ?, payment_amount = ?, payment_status = ?, created = ?, payer_email = ?, first_name = ?, last_name = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ?, account_id = ?, payment_method = ?, discount_code = ?, shipping_method = ?, shipping_amount = ? WHERE id = ?');
        $stmt->execute([ $_POST['txn_id'], $_POST['amount'], $_POST['status'], date('Y-m-d H:i:s', strtotime($_POST['created'])), $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], empty($_POST['account']) ? NULL : $_POST['account'], $_POST['method'], $_POST['discount_code'], $_POST['shipping_method'], $_POST['shipping_amount'], $_GET['id'] ]);
        addOrderItems($pdo, $_POST['txn_id']);
        header('Location: index.php?page=orders&success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the transaction
        $stmt = $pdo->prepare('DELETE t, ti FROM transactions t LEFT JOIN transactions_items ti ON ti.txn_id = t.txn_id WHERE t.id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: index.php?page=orders&success_msg=3');
        exit;
    }
} else {
    // Create a new transaction
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO transactions (txn_id,payment_amount,payment_status,created,payer_email,first_name,last_name,address_street,address_city,address_state,address_zip,address_country,account_id,payment_method,discount_code,shipping_method,shipping_amount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['txn_id'], $_POST['amount'], $_POST['status'], date('Y-m-d H:i:s', strtotime($_POST['created'])), $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], empty($_POST['account']) ? NULL : $_POST['account'], $_POST['method'], $_POST['discount_code'], $_POST['shipping_method'], $_POST['shipping_amount'] ]);
        addOrderItems($pdo, $_POST['txn_id']);
        header('Location: index.php?page=orders&success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Order', 'orders', 'manage')?>

<form action="" method="post">

    <div class="content-title">
        <h2><?=$page?> Order</h2>
        <a href="index.php?page=orders" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this order?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="tabs">
        <a href="#" class="active">Details</a>
        <a href="#">Address</a>
        <a href="#">Items</a>
    </div>

    <div class="content-block tab-content active">

        <div class="form responsive-width-100">

            <label for="txn_id"><i class="required">*</i> Transaction ID</label>
            <input id="txn_id" type="text" name="txn_id" placeholder="Transaction ID" value="<?=$transaction['txn_id']?>" required>

            <label for="status"><i class="required">*</i> Status</label>
            <select id="status" name="status" required>
                <option value="Completed"<?=$transaction['payment_status']=='Completed'?' selected':''?>>Completed</option>
                <option value="Pending"<?=$transaction['payment_status']=='Pending'?' selected':''?>>Pending</option>
                <option value="Failed"<?=$transaction['payment_status']=='Failed'?' selected':''?>>Failed</option>
                <option value="Cancelled"<?=$transaction['payment_status']=='Cancelled'?' selected':''?>>Cancelled</option>
                <option value="Reversed"<?=$transaction['payment_status']=='Reversed'?' selected':''?>>Reversed</option>
                <option value="Refunded"<?=$transaction['payment_status']=='Refunded'?' selected':''?>>Refunded</option>
                <option value="Shipped"<?=$transaction['payment_status']=='Shipped'?' selected':''?>>Shipped</option>
            </select>

            <label for="amount"><i class="required">*</i> Payment Amount</label>
            <input id="amount" type="number" name="amount" placeholder="0.00" value="<?=$transaction['payment_amount']?>" step=".01" required>

            <label for="email"><i class="required">*</i> Customer Email</label>
            <input id="email" type="email" name="email" placeholder="joebloggs@example.com" value="<?=htmlspecialchars($transaction['payer_email'], ENT_QUOTES)?>" required>

            <label for="account">Account</label>
            <select id="account" name="account">
                <option value=""<?=$transaction['account_id']==NULL?' selected':''?>>(none)</option>
                <?php foreach ($accounts as $account): ?>
                <option value="<?=$account['id']?>"<?=$account['id']==$transaction['account_id']?' selected':''?>><?=$account['id']?> - <?=htmlspecialchars($account['email'], ENT_QUOTES)?></option>
                <?php endforeach; ?>
            </select>

            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" placeholder="Joe" value="<?=htmlspecialchars($transaction['first_name'], ENT_QUOTES)?>">

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" placeholder="Bloggs" value="<?=htmlspecialchars($transaction['last_name'], ENT_QUOTES)?>">

            <label for="method">Payment Method</label>
            <input id="method" type="text" name="method" placeholder="website" value="<?=$transaction['payment_method']?>">

            <label for="shipping_method">Shipping Method</label>
            <input id="shipping_method" type="text" name="shipping_method" placeholder="Standard" value="<?=$transaction['shipping_method']?>">

            <label for="shipping_amount"><i class="required">*</i> Shipping Amount</label>
            <input id="shipping_amount" type="number" name="shipping_amount" placeholder="0.00" value="<?=$transaction['shipping_amount']?>" step=".01" required>

            <label for="discount_code">Discount Code</label>
            <input id="discount_code" type="text" name="discount_code" placeholder="Discount Code" value="<?=htmlspecialchars($transaction['discount_code'], ENT_QUOTES)?>">

            <label for="created"><i class="required">*</i> Date</label>
            <input id="created" type="datetime-local" name="created" value="<?=date('Y-m-d\TH:i', strtotime($transaction['created']))?>" required>

        </div>

    </div>

    <div class="content-block tab-content">

        <div class="form responsive-width-100">

            <label for="address_street">Address Street</label>
            <input id="address_street" type="text" name="address_street" placeholder="24 High Street" value="<?=htmlspecialchars($transaction['address_street'], ENT_QUOTES)?>">

            <label for="address_city">Address City</label>
            <input id="address_city" type="text" name="address_city" placeholder="New York" value="<?=htmlspecialchars($transaction['address_city'], ENT_QUOTES)?>">

            <label for="address_state">Address State</label>
            <input id="address_state" type="text" name="address_state" placeholder="NY" value="<?=htmlspecialchars($transaction['address_state'], ENT_QUOTES)?>">

            <label for="address_zip">Address Zip</label>
            <input id="address_zip" type="text" name="address_zip" placeholder="10001" value="<?=htmlspecialchars($transaction['address_zip'], ENT_QUOTES)?>">

            <label for="address_country">Country</label>
            <select id="address_country" name="address_country" required>
                <?php foreach(get_countries() as $country): ?>
                <option value="<?=$country?>"<?=$country==$transaction['address_country']?' selected':''?>><?=$country?></option>
                <?php endforeach; ?>
            </select>

        </div>

    </div>

    <div class="content-block tab-content">
        <div class="table manage-order-table">
            <table>
                <thead>
                    <tr>
                        <td>Product</td>
                        <td>Price</td>
                        <td>Quantity</td>
                        <td>Options</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions_items)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;" class="no-order-items-msg">There are no order items</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($transactions_items as $item): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="item_id[]" value="<?=$item['id']?>">
                            <select name="item_product[]">
                                <?php foreach ($products as $product): ?>
                                <option value="<?=$product['id']?>"<?=$item['item_id']==$product['id']?' selected':''?>><?=$product['id']?> - <?=htmlspecialchars($product['name'], ENT_QUOTES)?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input name="item_price[]" type="number" placeholder="Price" value="<?=$item['item_price']?>" step=".01"></td>
                        <td><input name="item_quantity[]" type="number" placeholder="Quantity" value="<?=$item['item_quantity']?>"></td>
                        <td><input name="item_options[]" type="text" placeholder="Options" value="<?=htmlspecialchars($item['item_options'], ENT_QUOTES)?>"></td>
                        <td><i class="fa-solid fa-xmark delete-item"></i></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="#" class="add-item"><i class="fa-solid fa-plus"></i>Add Item</a>
        </div>
    </div>

</form>

<?=template_admin_footer('initManageOrder(' . json_encode($products) . ')')?>