<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = (int)$_POST['book_id'];

    try {
        // Check if this book is already in the cart
        $check = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND book_id = :book_id");
        $check->execute([':user_id' => $user_id, ':book_id' => $book_id]);

        if ($check->rowCount() > 0) {
            // If exists, update quantity by 1
            $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND book_id = :book_id");
            $update->execute([':user_id' => $user_id, ':book_id' => $book_id]);
        } else {
            // Else, insert new cart entry
            $insert = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (:user_id, :book_id, 1)");
            $insert->execute([':user_id' => $user_id, ':book_id' => $book_id]);
        }

        header("Location: view_cart.php");
        exit();

    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
