<?php
defined('admin') or exit;
// Default account product values
$account = [
    'email' => '',
    'password' => '',
    'role' => 'Member',
    'first_name' => '',
    'last_name' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => '',
    'registered' => date('Y-m-d\TH:i')
];
if (isset($_GET['id'])) {
    // Retrieve the account from the database
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the account
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
        $stmt = $pdo->prepare('UPDATE accounts SET email = ?, password = ?, first_name = ?, last_name = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ?, role = ?, registered = ? WHERE id = ?');
        $stmt->execute([ $_POST['email'], $password, $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_POST['role'], date('Y-m-d H:i:s', strtotime($_POST['registered'])), $_GET['id'] ]);
        header('Location: index.php?page=accounts&success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the account
        $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: index.php?page=accounts&success_msg=3');
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO accounts (email,password,first_name,last_name,address_street,address_city,address_state,address_zip,address_country,role,registered) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['email'], $password, $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_POST['role'], date('Y-m-d H:i:s', strtotime($_POST['registered'])) ]);
        header('Location: index.php?page=accounts&success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Account', 'accounts', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Account</h2>
        <a href="index.php?page=accounts" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this account?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="tabs">
        <a href="#" class="active">General</a>
        <a href="#">Shipping Address</a>
    </div>

    <div class="content-block tab-content active">

        <div class="form responsive-width-100">

            <label for="email"><i class="required">*</i> Email</label>
            <input id="email" type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($account['email'], ENT_QUOTES)?>" required>

            <label for="password"><?=$page == 'Edit' ? 'New ' : ''?>Password</label>
            <input type="text" id="password" name="password" placeholder="<?=$page == 'Edit' ? 'New ' : ''?>Password" value=""<?=$page == 'Edit' ? '' : ' required'?>>

            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" placeholder="Joe" value="<?=htmlspecialchars($account['first_name'], ENT_QUOTES)?>">

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" placeholder="Bloggs" value="<?=htmlspecialchars($account['last_name'], ENT_QUOTES)?>">

            <label for="role"><i class="required">*</i> Role</label>
            <select id="role" name="role" required>
                <option value="Member"<?=$account['role']=='Member'?' selected':''?>>Member</option>
                <option value="Admin"<?=$account['role']=='Admin'?' selected':''?>>Admin</option>
            </select>

            <label for="registered"><i class="required">*</i> Registered</label>
            <input id="registered" type="datetime-local" name="registered" value="<?=date('Y-m-d\TH:i', strtotime($account['registered']))?>" required>

        </div>

    </div>

    <div class="content-block tab-content">

        <div class="form responsive-width-100">

            <label for="address_street">Address Street</label>
            <input id="address_street" type="text" name="address_street" placeholder="24 High Street" value="<?=htmlspecialchars($account['address_street'], ENT_QUOTES)?>">

            <label for="address_city">Address City</label>
            <input id="address_city" type="text" name="address_city" placeholder="New York" value="<?=htmlspecialchars($account['address_city'], ENT_QUOTES)?>">

            <label for="address_state">Address State</label>
            <input id="address_state" type="text" name="address_state" placeholder="NY" value="<?=htmlspecialchars($account['address_state'], ENT_QUOTES)?>">

            <label for="address_zip">Address Zip</label>
            <input id="address_zip" type="text" name="address_zip" placeholder="10001" value="<?=htmlspecialchars($account['address_zip'], ENT_QUOTES)?>">

            <label for="address_country">Country</label>
            <select id="address_country" name="address_country" required>
                <?php foreach(get_countries() as $country): ?>
                <option value="<?=$country?>"<?=$country==$account['address_country']?' selected':''?>><?=$country?></option>
                <?php endforeach; ?>
            </select>

        </div>

    </div>

</form>

<?=template_admin_footer()?>