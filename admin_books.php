<?php
session_start();
include('database.php');

// Only allows admins
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $bookId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM book_database WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $bookId);
    $stmt->execute();
    header("Location: admin_books.php");
    exit();
}

// Handle adding a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $course = $_POST['course'];
    $price = $_POST['price'];
    $condition = $_POST['condition'];

    $stmt = $conn->prepare("
        INSERT INTO book_database (title, author, isbn, course, price, `condition`)
        VALUES (:title, :author, :isbn, :course, :price, :condition)
    ");
    $stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':isbn' => $isbn,
        ':course' => $course,
        ':price' => $price,
        ':condition' => $condition
    ]);
    header("Location: admin_books.php");
    exit();
}

// Handle editing a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $bookId = $_POST['book_id'];
    $price = $_POST['price'];
    $condition = $_POST['condition'];

    $stmt = $conn->prepare("
        UPDATE book_database SET price = :price, `condition` = :condition
        WHERE book_id = :book_id
    ");
    $stmt->execute([
        ':price' => $price,
        ':condition' => $condition,
        ':book_id' => $bookId
    ]);
    header("Location: admin_books.php");
    exit();
}

// Fetches all books
$stmt = $conn->query("SELECT * FROM book_database ORDER BY title ASC");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>admin book manager | campuscart</title>
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

        form {
            display: inline;
        }

        .add-form {
            margin-top: 40px;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        input, select {
            font-family: 'Cinzel', serif;
            padding: 6px;
            margin: 4px 0;
            width: 100%;
        }

        button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
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
    </style>
</head>
<body>

<!-- Nav -->
<div class="nav-bar">
    <a href="admin_dashboard.php">dashboard</a>
    <a href="admin_orders.php">orders</a>
    <a href="admin_books.php">manage books</a>
    <a href="admin_users.php">manage users</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php">logout</a>
    <?php endif; ?>
</div>

<h1>book manager</h1>

<!-- The book table -->
<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Course</th>
        <th>Price</th>
        <th>Condition</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($books as $book): ?>
    <tr>
        <td><?= htmlspecialchars($book['title']) ?></td>
        <td><?= htmlspecialchars($book['author']) ?></td>
        <td><?= htmlspecialchars($book['course']) ?></td>
        <td>
            <form method="post" action="admin_books.php">
                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                <input type="number" step="0.01" name="price" value="<?= $book['price'] ?>" required>
        </td>
        <td>
                <select name="condition" required>
                    <option value="new" <?= $book['condition'] === 'new' ? 'selected' : '' ?>>new</option>
                    <option value="used" <?= $book['condition'] === 'used' ? 'selected' : '' ?>>used</option>
                </select>
        </td>
        <td>
                <button type="submit" name="edit">update</button>
            </form>
            <a href="admin_books.php?delete=<?= $book['book_id'] ?>" onclick="return confirm('delete this book?');">delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Adds new book form -->
<div class="add-form">
    <h3>add new book</h3>
    <form method="post" action="admin_books.php">
        <input type="text" name="title" placeholder="title" required>
        <input type="text" name="author" placeholder="author" required>
        <input type="text" name="isbn" placeholder="isbn" required>
        <input type="text" name="course" placeholder="course" required>
        <input type="number" step="0.01" name="price" placeholder="price" required>
        <select name="condition" required>
            <option value="new">new</option>
            <option value="used">used</option>
        </select>
        <button type="submit" name="add">add book</button>
    </form>
</div>

</body>
</html>
