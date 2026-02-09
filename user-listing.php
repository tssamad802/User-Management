<?php
require_once './includes/dbh.inc.php';
require_once './includes/config.session.inc.php';
require_once './includes/view.php';
require_once './includes/model.php';
require_once './includes/control.php';

$view = new view();
$db = new database();
$conn = $db->connection();
$controller = new controller($conn);
$errors_add = $_SESSION['errors_add'] ?? [];
$errors_edit = $_SESSION['errors_edit'] ?? [];
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
$users = $controller->fetch_records('users');
$deletedUsers = $controller->fetch_deleted_records('users');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Success Message -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-people-fill"></i> User Management
            </h3>
            <div>
                <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#trashModal">
                    <i class="bi bi-trash"></i> Trash (<?php echo count($deletedUsers); ?>)
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="bi bi-plus-circle"></i> Add User
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">No users found</p>
                    </div>
                <?php else: ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                    <td><span class="badge bg-success"><?php echo htmlspecialchars($row['Position']); ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                            data-position="<?php echo htmlspecialchars($row['Position']); ?>" title="Edit User">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <a href="./includes/delete.inc.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            title="Delete User">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-end">
                            <li class="page-item disabled"><a class="page-link">Previous</a></li>
                            <li class="page-item active"><a class="page-link">1</a></li>
                            <li class="page-item"><a class="page-link">2</a></li>
                            <li class="page-item"><a class="page-link">Next</a></li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus"></i> Add User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form action="./includes/add.inc.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter name" name="name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" placeholder="Enter email" name="email"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" placeholder="********" name="pwd" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select role</option>
                                    <option value="admin">Admin</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="position" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Display errors for Add modal -->
                        <?php if (!empty($errors_add)): ?>
                            <div class="mt-3">
                                <?php foreach ($errors_add as $error): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form action="./includes/edit.inc.php" method="POST">
                        <input type="hidden" name="id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter name" name="name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" placeholder="Enter email" name="email"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password <small class="text-muted">(leave blank to keep
                                        current)</small></label>
                                <input type="password" class="form-control" placeholder="Enter new password" name="pwd">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select role</option>
                                    <option value="admin">Admin</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="position" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Display errors for Edit modal -->
                        <?php if (!empty($errors_edit)): ?>
                            <div class="mt-3">
                                <?php foreach ($errors_edit as $error): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trash Modal (Soft Deleted Users) -->
    <div class="modal fade" id="trashModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-trash"></i> Deleted Users
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <?php if (empty($deletedUsers)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-trash" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Trash is empty</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Deleted At</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deletedUsers as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><span
                                                class="badge bg-secondary"><?php echo htmlspecialchars($row['role']); ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y h:i A', strtotime($row['is_deleted'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="./includes/restore.inc.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Are you sure you want to restore this user?')"
                                                title="Restore User">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($errors_add)): ?>
                var userModal = new bootstrap.Modal(document.getElementById('userModal'));
                userModal.show();
                <?php unset($_SESSION['errors_add']); ?>
            <?php endif; ?>
            <?php if (!empty($errors_edit)): ?>
                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
                <?php unset($_SESSION['errors_edit']); ?>
            <?php endif; ?>
        });
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var role = button.getAttribute('data-role');
            var position = button.getAttribute('data-position');

            editModal.querySelector('input[name="id"]').value = id;
            editModal.querySelector('input[name="name"]').value = name;
            editModal.querySelector('input[name="email"]').value = email;
            editModal.querySelector('select[name="role"]').value = role;
            editModal.querySelector('select[name="position"]').value = position;
            editModal.querySelector('input[name="pwd"]').value = ''; // Clear password field
        });
    </script>
</body>

</html>