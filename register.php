<?php
session_start();
include('database.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->fetch()) {
            $error = "that email is already registered.";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, is_admin)
                                    VALUES (:first, :last, :email, :password, 0)");
            $stmt->execute([
                ':first' => $first,
                ':last' => $last,
                ':email' => $email,
                ':password' => $password
            ]);

            $success = "account created! you can now <a href='login.php'>log in</a>.";
        }
    } catch (PDOException $e) {
        $error = "registration error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>register | campuscart</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #fdf6ec;
            text-transform: lowercase;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            font-family: 'Cinzel', serif;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-family: 'Cinzel', serif;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #333;
        }

        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .success {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }

        a {
            display: block;
            margin-top: 12px;
            font-size: 14px;
            color: #000;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>register</h2>
    <form method="post" action="register.php">
        <input type="text" name="first_name" placeholder="first name" required>
        <input type="text" name="last_name" placeholder="last name" required>
        <input type="email" name="email" placeholder="email" required>
        <input type="password" name="password" placeholder="password" required>
        <button type="submit">create account</button>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
    </form>
    <a href="login.php">already have an account?</a>
</div>
</body>
</html>
