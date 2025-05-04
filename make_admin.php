<?php
include('database.php');

$email = "admin@montclair.edu";
$rawPassword = "admin123";
$hashed = password_hash($rawPassword, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, is_admin)
                            VALUES ('Admin', 'User', :email, :password, 1)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed);
    $stmt->execute();

    echo "✅ Admin created. Email: $email | Password: $rawPassword";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
