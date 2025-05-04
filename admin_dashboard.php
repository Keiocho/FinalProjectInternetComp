<?php
session_start();
include('database.php');

// Only allows admin users
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>admin dashboard | campuscart</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #fdf6ec;
            margin: 0;
            padding: 0;
            text-transform: lowercase;
        }

        .nav-bar {
            background-color: #000;
            padding: 15px 20px;
            display: flex;
            gap: 20px;
        }

        .nav-bar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-bar a:hover {
            color: #ccc;
        }

        .dashboard-container {
            padding: 40px;
            text-align: center;
        }

        .dashboard-container h1 {
            margin-bottom: 40px;
        }

        .dashboard-links a {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 15px 30px;
            margin: 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
        }

        .dashboard-links a:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <!-- Admin navbar -->
    <div class="nav-bar">
        <a href="admin_dashboard.php">dashboard</a>
        <a href="admin_orders.php">orders</a>
        <a href="admin_books.php">manage books</a>
        <a href="admin_users.php">manage users</a>
        <a href="logout.php">logout</a>
    </div>

    <div class="dashboard-container">
        <h1>welcome, <?= htmlspecialchars($_SESSION['first_name']) ?> 👋</h1>
        <div class="dashboard-links">
            <a href="admin_orders.php">view orders</a>
            <a href="admin_books.php">manage books</a>
            <a href="admin_users.php">manage users</a>
        </div>
    </div>
</body>
</html>
