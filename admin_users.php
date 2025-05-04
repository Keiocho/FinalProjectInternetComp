<?php
session_start();
include('database.php');

// Only allows admin access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Prevents deleting self
$currentUserId = $_SESSION['user_id'];

// Handles deletion
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    if ($deleteId != $currentUserId) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :id");
        $stmt->bindParam(':id', $deleteId);
        $stmt->execute();
        header("Location: admin_users.php");
        exit();
    }
}

// Fetches all users
$stmt = $conn->query("SELECT * FROM users ORDER BY last_name ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>admin users | campuscart</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #fdf6ec;
            padding: 20px;
            text-transform: lowercase;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #000;
            color: #fff;
        }

        .nav-bar {
            background-color: #000;
            padding: 10px 20px;
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-bar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-bar a:hover {
            color: #ccc;
        }

        a.delete-btn {
            color: red;
            text-decoration: underline;
        }

        a.delete-btn:hover {
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Nav -->
<div class="nav-bar">
    <a href="admin_dashboard.php">dashboard</a>
    <a href="admin_orders.php">orders</a>
    <a href="admin_books.php">manage books</a>
    <a href="admin_users.php">manage users</a>
    <a href="logout.php">logout</a>
</div>

<h1>user manager</h1>

<table>
    <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= $user['is_admin'] ? 'admin' : 'user' ?></td>
        <td>
            <?php if ($user['user_id'] != $currentUserId): ?>
                <a href="admin_users.php?delete=<?= $user['user_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">delete</a>
            <?php else: ?>
                (you)
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
