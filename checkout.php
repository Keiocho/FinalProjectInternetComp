<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Step 1: Get cart items
    $stmt = $conn->prepare("
        SELECT c.quantity, b.price, c.book_id
        FROM cart c
        JOIN book_database b ON c.book_id = b.book_id
        WHERE c.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($items) === 0) {
        echo "<p style='text-align:center; font-family:Cinzel, serif;'>your cart is empty. <a href='index.php'>go back</a></p>";
        exit();
    }

    // Step 2: Calculate total
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Step 3: Insert into orders table
    $insertOrder = $conn->prepare("
        INSERT INTO orders (user_id, order_date, total_amount)
        VALUES (:user_id, NOW(), :total)
    ");
    $insertOrder->bindParam(':user_id', $user_id);
    $insertOrder->bindParam(':total', $total);
    $insertOrder->execute();

    // Step 4: Clear cart
    $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $deleteCart->bindParam(':user_id', $user_id);
    $deleteCart->execute();

    $orderSuccess = true;

} catch (PDOException $e) {
    die("❌ Error during checkout: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>order complete | campuscart</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #fdf6ec;
            text-align: center;
            padding: 50px;
            text-transform: lowercase;
        }

        h1 {
            font-size: 28px;
            color: #333;
        }

        p {
            font-size: 18px;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 18px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        a:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<?php if (isset($orderSuccess) && $orderSuccess): ?>
    <h1>your order has been placed!</h1>
    <p>total charged: $<?= number_format($total, 2) ?></p>
    <a href="index.php">return to home</a>
<?php else: ?>
    <h1>something went wrong</h1>
    <p>please try again or contact support.</p>
<?php endif; ?>

</body>
</html>
