<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch cart items and book details
    $stmt = $conn->prepare("
        SELECT c.quantity, b.title, b.author, b.price
        FROM cart c
        JOIN book_database b ON c.book_id = b.book_id
        WHERE c.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
} catch (PDOException $e) {
    die("❌ Error fetching cart: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>your cart | campuscart</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cinzel', serif;
            background-color: #fdf6ec;
            padding: 30px;
        }

        h1 {
            text-align: center;
            text-transform: lowercase;
        }

        table {
            width: 80%;
            margin: 0 auto 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #eee;
        }

        .total {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        .back-link, .checkout-btn {
            display: block;
            width: fit-content;
            margin: 20px auto;
            font-family: 'Cinzel', serif;
            text-transform: lowercase;
            text-decoration: none;
            color: #fff;
            background-color: #000;
            padding: 10px 16px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .back-link:hover, .checkout-btn:hover {
            background-color: #333;
        }

    </style>
</head>
<body>

<h1>your cart</h1>

<?php if (count($items) === 0): ?>
    <p style="text-align:center;">your cart is empty.</p>
<?php else: ?>
    <table>
        <tr>
            <th>title</th>
            <th>author</th>
            <th>price</th>
            <th>quantity</th>
            <th>subtotal</th>
        </tr>
        <?php foreach ($items as $item): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= htmlspecialchars($item['author']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($subtotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p class="total">total: $<?= number_format($total, 2) ?></p>

    <a href="index.php" class="back-link">← continue shopping</a>
    <a href="checkout.php" class="checkout-btn">place order</a>
<?php endif; ?>

</body>
</html>
