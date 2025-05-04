<?php
session_start();
include_once('database.php');

function getAllOrdersWithUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT o.order_id, o.order_date, o.total_amount, u.first_name, u.last_name
                             FROM orders o
                             JOIN users u ON o.user_id = u.user_id
                             ORDER BY o.order_date DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function placeOrder($user_id, $items) {
    global $conn;

    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    try {

        $conn->beginTransaction();

        $insertOrder = $conn->prepare("INSERT INTO orders (user_id, order_date, total_amount) VALUES (:user_id, NOW(), :total)");
        $insertOrder->bindParam(':user_id', $user_id);
        $insertOrder->bindParam(':total', $total);
        $insertOrder->execute();

        $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $deleteCart->bindParam(':user_id', $user_id);
        $deleteCart->execute();

        $conn->commit();
        return true;
    } catch (PDOException $e) {
        $conn->rollBack();
        return false;
    }
}
