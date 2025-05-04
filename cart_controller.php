<?php
session_start();
include_once('database.php');

function addToCart($userId, $bookId) {
    global $conn;

    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND book_id = :book_id");
    $check->execute([':user_id' => $userId, ':book_id' => $bookId]);

    if ($check->rowCount() > 0) {
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND book_id = :book_id");
        return $update->execute([':user_id' => $userId, ':book_id' => $bookId]);
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (:user_id, :book_id, 1)");
        return $insert->execute([':user_id' => $userId, ':book_id' => $bookId]);
    }
}

function getCartItems($userId) {
    global $conn;

    $stmt = $conn->prepare("SELECT c.quantity, b.title, b.author, b.price, c.book_id
                            FROM cart c
                            JOIN book_database b ON c.book_id = b.book_id
                            WHERE c.user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeFromCart($userId, $bookId) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id AND book_id = :book_id");
    return $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
}

function clearCart($userId) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    return $stmt->execute([':user_id' => $userId]);
}">]}
