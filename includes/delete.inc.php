<?php
require_once 'config.session.inc.php';
require_once 'dbh.inc.php';
require_once 'model.php';
require_once 'control.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);
    
    if ($controller->soft_delete('users', $id)) {
        $_SESSION['success'] = 'User deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete user';
    }
} else {
    $_SESSION['error'] = 'Invalid user ID';
}

header("Location: ../user-listing");
exit;
?>