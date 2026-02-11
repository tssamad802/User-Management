<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $role = $_POST['role'];
    $position = $_POST['position'];

    require_once 'config.session.inc.php';
    require_once 'dbh.inc.php';
    require_once 'model.php';
    require_once 'control.php';

    $db = new database();
    $conn = $db->connection();
    $controller = new controller($conn);

    $errors = [];

    if ($controller->is_empty_inputs([$name, $email, $pwd, $role, $position])) {
        $errors[] = 'Please fill in all fields';
    }
    if ($controller->is_only_char([$name])) {
        $errors[] = 'Name must contain only letters and spaces';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    if (!empty($email) && $controller->check_record('users', ['email' => $email])) {
        $errors[] = 'Email already exists';
    }
    if (!empty($pwd) && strlen($pwd) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if ($errors) {
        $_SESSION['errors_add'] = $errors;
        header('Location: ../user-listing');
        exit;
    }
    $insertData = [
        'name' => $name,
        'email' => $email,
        'pwd' => $pwd,
        'role' => $role,
        'Position' => $position,
        'is_deleted' => null
    ];
    try {
        $result = $controller->insert_record('users', $insertData);

        if ($result) {
            //$_SESSION['success'] = 'User added successfully';
            print_r($result);
            //header("Location: ./add");
            // exit;
            // echo '<pre>';
            // print_r($result);
            // echo '</pre>';
        } else {
            $_SESSION['errors_add'] = ['Failed to add user - Insert returned false'];
            header('Location: ../user-listing');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['errors_add'] = ['Database Error: ' . $e->getMessage()];
        header('Location: ../user-listing');
        exit;
    }
} else {
    header("Location: ../user-listing");
    exit;
}
?>