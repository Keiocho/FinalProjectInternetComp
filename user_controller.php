<?php
include('database.php');

function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserByEmail($email, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function emailExists($email, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

function registerUser($first, $last, $email, $password, $conn) {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, is_admin)
                            VALUES (:first, :last, :email, :password, 0)");
    return $stmt->execute([
        ':first' => $first,
        ':last' => $last,
        ':email' => $email,
        ':password' => $password
    ]);
}

function registerAdmin($first, $last, $email, $password, $conn) {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, is_admin)
                            VALUES (:first, :last, :email, :password, 1)");
    return $stmt->execute([
        ':first' => $first,
        ':last' => $last,
        ':email' => $email,
        ':password' => $password
    ]);
}

function deleteUserById($id, $conn) {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :id");
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function getAllUsers($conn) {
    $stmt = $conn->query("SELECT * FROM users ORDER BY last_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateUserProfile($id, $data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $data['first_name'], $data['last_name'], $data['email'], $id);
    return $stmt->execute();
}
?>
