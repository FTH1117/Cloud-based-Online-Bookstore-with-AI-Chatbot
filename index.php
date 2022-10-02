<?php
define('shoppingcart', true);
// Determine the base URL
$base_url = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http';
$base_url .= '://' . rtrim($_SERVER['HTTP_HOST'], '/');
$base_url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 || strpos($_SERVER['HTTP_HOST'], ':') !== false ? '' : ':' . $_SERVER['SERVER_PORT'];
$base_url .= '/' . ltrim(substr(str_replace('\\', '/', realpath(__DIR__)), strlen($_SERVER['DOCUMENT_ROOT'])), '/');
define('base_url', rtrim($base_url, '/') . '/');
// If somehow the above URL fails to resolve the correct URL, you can simply comment out the below line and manually specifiy the URL to the system.
// define('base_url', 'http://yourdomain.com/shoppingcart/');
// Initialize a new session
session_start();
// Include the configuration file, this contains settings you can change.
include 'config.php';
// Include functions and connect to the database using PDO MySQL
include 'functions.php';
// Connect to MySQL database
$pdo = pdo_connect_mysql();
// Output error variable
$error = '';
// Define all the routes for all pages
$url = routes([
    '/' => 'home.php',
    '/home' => 'home.php',
    '/product/{id}' => 'product.php',
    '/products' => 'products.php',
    '/products/{category}/{sort}' => 'products.php',
    '/products/{p}/{category}/{sort}' => 'products.php',
    '/myaccount' => 'myaccount.php',
    '/myaccount/{tab}' => 'myaccount.php',
    '/download/{id}' => 'download.php',
    '/cart' => 'cart.php',
    '/cart/{remove}' => 'cart.php',
    '/checkout' => 'checkout.php',
    '/placeorder' => 'placeorder.php',
    '/search/{query}' => 'search.php',
    '/logout' => 'logout.php'
]);
// Check if route exists
if ($url) {
    include $url;
} else {
    // Page is set to home (home.php) by default, so when the visitor visits that will be the page they see.
    $page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';
    // Include the requested page
    include $page . '.php';
}
?>