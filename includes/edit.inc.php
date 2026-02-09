<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
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

    if ($controller->is_empty_inputs([$id, $name, $email, $role, $position])) {
        $errors[] = 'Please fill in all required fields';
    }
    
   if ($controller->is_only_char([$name])) {
        $errors[] = 'Name must contain only letters and spaces';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
   if (!empty($email)) {
        $existingUsers = $controller->check_record('users', ['email' => $email]);
        foreach ($existingUsers as $user) {
            if ($user['id'] != $id) {
                $errors[] = 'Email already exists';
                break;
            }
        }
    }

    if (!empty($pwd) && strlen($pwd) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    if ($errors) {
        $_SESSION['errors_edit'] = $errors;
        header('Location: ../user-listing');
        exit;
    }
    $updateData = [
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'Position' => $position
    ];
    if (!empty($pwd)) {
        $updateData['pwd'] = $pwd;
    }

    if ($controller->update('users', $updateData, $id)) {
        $_SESSION['success'] = 'User updated successfully';
        header("Location: ../user-listing");
        exit;
    } else {
        $_SESSION['errors_edit'] = ['Failed to update user'];
        header('Location: ../user-listing');
        exit;
    }

} else {
    header("Location: ../user-listing");
    exit;
}
?>