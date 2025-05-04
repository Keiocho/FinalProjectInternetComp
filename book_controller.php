<?php
include_once('database.php');

function getAllBooks($conn) {
    $stmt = $conn->query("SELECT * FROM book_database ORDER BY title ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addBook($data, $conn) {
    $stmt = $conn->prepare("
        INSERT INTO book_database (title, author, isbn, course, price, `condition`)
        VALUES (:title, :author, :isbn, :course, :price, :condition)
    ");
    return $stmt->execute([
        ':title' => $data['title'],
        ':author' => $data['author'],
        ':isbn' => $data['isbn'],
        ':course' => $data['course'],
        ':price' => $data['price'],
        ':condition' => $data['condition']
    ]);
}

function updateBook($id, $data, $conn) {
    $stmt = $conn->prepare("
        UPDATE book_database SET price = :price, `condition` = :condition
        WHERE book_id = :book_id
    ");
    return $stmt->execute([
        ':price' => $data['price'],
        ':condition' => $data['condition'],
        ':book_id' => $id
    ]);
}

function deleteBook($id, $conn) {
    $stmt = $conn->prepare("DELETE FROM book_database WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $id);
    return $stmt->execute();
}

function searchBooks($searchTerm, $conn) {
    $query = "%{$searchTerm}%";
    $stmt = $conn->prepare("
        SELECT * FROM book_database
        WHERE title LIKE :search
        OR author LIKE :search
        OR isbn LIKE :search
        OR course LIKE :search
    ");
    $stmt->bindParam(':search', $query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
