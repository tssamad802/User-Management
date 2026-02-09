<?php
$request_url = $_SERVER['REQUEST_URI'];

if (strpos($request_url, '/users') !== false) {
    include 'user-listing.php';
} else {
    include 'user-listing.php';
    exit;
}
?>