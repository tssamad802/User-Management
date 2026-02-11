<?php
$request_url = $_SERVER['REQUEST_URI'];

// Normalize URL (remove query string)
$request_path = parse_url($request_url, PHP_URL_PATH);

switch (true) {
    case str_contains($request_path, '/api'):
        include './includes/api.php';
        break;

    case str_contains($request_path, '/users'):
        include './user-listing.php';
        break;

    case str_contains($request_path, '/add'):
        include './includes/add.inc.php';
        break;

    case str_contains($request_path, '/edit'):
        include './includes/edit.inc.php';
        break;

    default:
        include './user-listing.php';
        break;
}
?>