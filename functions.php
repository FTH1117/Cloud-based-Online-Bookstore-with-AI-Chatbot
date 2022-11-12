<?php
// Function that will connect to the MySQL database
function pdo_connect_mysql() {
    try {
        // Connect to the MySQL database using the PDO interface
    	$pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=utf8', db_user, db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $exception) {

    	exit('Failed to connect to database!');
    }
}
// Function to retrieve a product from cart by the ID and options string
function &get_cart_product($id, $options) {
    $p = null;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$product) {
            if ($product['id'] == $id && $product['options'] == $options) {
                $p = &$product;
                return $p;
            }
        }
    }
    return $p;
}
// Populate categories function
function populate_categories($categories, $selected = 0, $parent_id = 0, $n = 0) {
    $html = '';
    foreach ($categories as $category) {
        if ($parent_id == $category['parent_id']) {
            $html .= '<option value="' . $category['id'] . '"' . ($selected == $category['id']  ? ' selected' : '') . '>' . str_repeat('--', $n) . ' ' . $category['name'] . '</option>';
            $html .= populate_categories($categories, $selected, $category['id'], $n+1);
        }
    }
    return $html;
}
// Get country list
function get_countries() {
    return ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"];
}

// Template header, feel free to customize this
function template_header($title, $head = '') {
// Get the amount of items in the shopping cart, this will be displayed in the header.
$num_items_in_cart = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$home_link = url('index.php');
$products_link = url('index.php?page=products');
$myaccount_link = url('index.php?page=myaccount');
$contact_link = url('index.php?page=contact');
$about_link = url('index.php?page=about');
$cart_link = url('index.php?page=cart');
$admin_link = isset($_SESSION['account_loggedin'], $_SESSION['account_role']) && $_SESSION['account_role'] == 'Admin' ? '<a href="' . base_url . 'admin/index.php" target="_blank">Admin</a>' : '';
$logout_link = isset($_SESSION['account_loggedin']) ? '<a title="Logout" href="' . url('index.php?page=logout') . '"><i class="fas fa-sign-out-alt"></i></a>' : '';
$site_name = site_name;
$base_url = base_url;
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
        <link rel="icon" type="image/png" href="{$base_url}favicon.png">
		<link href="{$base_url}style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.0.0/css/all.css">
        $head
	</head>
	<body>
	<script type="text/javascript">
    (function(d, m){
        var kommunicateSettings = 
            {"appId":"34a66e6ea6f128a69da1ed9ba4e7a3521","popupWidget":true,"automaticChatOpenOnNavigation":true};
        var s = document.createElement("script"); s.type = "text/javascript"; s.async = true;
        s.src = "https://widget.kommunicate.io/v2/kommunicate.app";
        var h = document.getElementsByTagName("head")[0]; h.appendChild(s);
        window.kommunicate = m; m._globals = kommunicateSettings;
    })(document, window.kommunicate || {});
/* NOTE : Use web server to view HTML files as real-time update will not work if you directly open the HTML file in the browser. */
</script>


        <header>
            <div class="content-wrapper">
                <h1><a href="index.php" style="color:black;font-size:20px;">$site_name</a></h1>
                <nav>
                    <a href="$home_link">Home</a>
                    <a href="$products_link">Products</a>
					<a href="$about_link">About Us</a>
					<a href="$contact_link">Contact Us</a>
					<a href="$myaccount_link">Profile</a>
                    $admin_link
                </nav>
                <div class="link-icons">
                    <div class="search">
						<i class="fas fa-search" title="Search"></i>
						<input type="text" placeholder="Search...">
					</div>
                    <a href="$cart_link" title="Shopping Cart">
						<i class="fas fa-shopping-cart"></i>
						<span>$num_items_in_cart</span>
					</a>
                    $logout_link
					<a class="responsive-toggle" href="#">
						<i class="fas fa-bars"></i>
					</a>
                </div>
            </div>
        </header>
        <main>
EOT;
}
// Template footer
function template_footer() {
$base_url = base_url;
$rewrite_url = rewrite_url ? 'true' : 'false';
$year = date('Y');
$currency_code = currency_code;
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
        </main>
        <footer>
            <div class="content-wrapper">
                <p>&copy; $year, <a href="https://www.linkedin.com/in/foong-tze-hing-390a1924b" target="_blank">Foong Tze Hing</a></p>
            </div>
        </footer>
        <script>
        const currency_code = "$currency_code", base_url = "$base_url", rewrite_url = $rewrite_url;
        </script>
        <script src="{$base_url}script.js"></script>
    </body>
</html>
EOT;
}
// Template admin header
function template_admin_header($title, $selected = 'orders', $selected_child = 'view') {
    $admin_links = '
        <a href="index.php?page=dashboard"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="index.php?page=orders"' . ($selected == 'orders' ? ' class="selected"' : '') . '><i class="fas fa-shopping-cart"></i>Orders</a>
        <div class="sub">
            <a href="index.php?page=orders"' . ($selected == 'orders' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Orders</a>
            <a href="index.php?page=order_manage"' . ($selected == 'orders' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Order</a>
        </div>
        <a href="index.php?page=products"' . ($selected == 'products' ? ' class="selected"' : '') . '><i class="fas fa-box-open"></i>Products</a>
        <div class="sub">
            <a href="index.php?page=products"' . ($selected == 'products' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Products</a>
            <a href="index.php?page=product"' . ($selected == 'products' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Product</a>
        </div>
        <a href="index.php?page=categories"' . ($selected == 'categories' ? ' class="selected"' : '') . '><i class="fas fa-list"></i>Categories</a>
        <div class="sub">
            <a href="index.php?page=categories"' . ($selected == 'categories' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Categories</a>
            <a href="index.php?page=category"' . ($selected == 'categories' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Category</a>
        </div>
        <a href="index.php?page=accounts"' . ($selected == 'accounts' ? ' class="selected"' : '') . '><i class="fas fa-users"></i>Accounts</a>
        <div class="sub">
            <a href="index.php?page=accounts"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Accounts</a>
            <a href="index.php?page=account"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Account</a>
        </div>
        <a href="index.php?page=shipping"' . ($selected == 'shipping' ? ' class="selected"' : '') . '><i class="fas fa-shipping-fast"></i>Shipping</a>
        <div class="sub">
            <a href="index.php?page=shipping"' . ($selected == 'shipping' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Shipping Methods</a>
            <a href="index.php?page=shipping_process"' . ($selected == 'shipping' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Shipping Method</a>
        </div>
        <a href="index.php?page=discounts"' . ($selected == 'discounts' ? ' class="selected"' : '') . '><i class="fas fa-tag"></i>Discounts</a>
        <div class="sub">
            <a href="index.php?page=discounts"' . ($selected == 'discounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Discounts</a>
            <a href="index.php?page=discount"' . ($selected == 'discounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Discount</a>
        </div>
        <a href="index.php?page=taxes"' . ($selected == 'taxes' ? ' class="selected"' : '') . '><i class="fa-solid fa-percent"></i>Taxes</a>
        <div class="sub">
            <a href="index.php?page=taxes"' . ($selected == 'taxes' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Taxes</a>
            <a href="index.php?page=tax"' . ($selected == 'taxes' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Tax</a>
        </div>
        <a href="index.php?page=media"' . ($selected == 'media' ? ' class="selected"' : '') . '><i class="fas fa-images"></i>Media</a>
    ';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
        <link rel="icon" type="image/png" href="../favicon.png">
		<link href="admin.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.0.0/css/all.css">
	</head>
	<body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>FTH Bookstore</h1>
            $admin_links
            <div class="footer">
                <a href="https://www.linkedin.com/in/foong-tze-hing-390a1924b" target="_blank">FTH Bookstore</a>
                Author: Foong Tze Hing
            </div>
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <div class="dropdown right">
                    <i class="fas fa-user-circle"></i>
                    <div class="list">
                        <a href="index.php?page=account&id={$_SESSION['account_id']}">Edit Profile</a>
                        <a href="index.php?page=logout">Logout</a>
                    </div>
                </div>
            </header>
EOT;
}
// Template admin footer
function template_admin_footer($js_script = '') {
        $js_script = $js_script ? '<script>' . $js_script . '</script>' : '';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
        </main>
        <script src="admin.js"></script>
        {$js_script}
    </body>
</html>
EOT;
}
// Determine URL function
function url($url) {
    if (rewrite_url) {
        $url = preg_replace('/\&(.*?)\=/', '/', str_replace(['index.php?page=', 'index.php'], '', $url));
    }
    return base_url . $url;
}
// Routeing function
function routes($urls) {
    foreach ($urls as $url => $file_path) {
        $url = '/' . ltrim($url, '/');
        $prefix = dirname($_SERVER['PHP_SELF']);
        $uri = $_SERVER['REQUEST_URI'];
        if (substr($uri, 0, strlen($prefix)) == $prefix) {
            $uri = substr($uri, strlen($prefix));
        }
        $uri = '/' . ltrim($uri, '/');
        $path = explode('/', parse_url($uri)['path']);
        $routes = explode('/', $url);
        $values = [];
        foreach ($path as $pk => $pv) {
            if (isset($routes[$pk]) && preg_match('/{(.*?)}/', $routes[$pk])) {
                $var = str_replace(['{','}'], '', $routes[$pk]);
                $routes[$pk] = preg_replace('/{(.*?)}/', $pv, $routes[$pk]);
                $values[$var] = $pv;
            }
        }
        if ($routes === $path && rewrite_url) {
            foreach ($values as $k => $v) {
                $_GET[$k] = $v;
            }
            return file_exists($file_path) ? $file_path : 'home.php';
        }
    }
    if (rewrite_url) {
        header('Location: ' . url('index.php'));
        exit;
    }
    return null;
}
// Format bytes to human-readable format
function format_bytes($bytes) {
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','KB','MB','GB','TB'][$i];
}
?>