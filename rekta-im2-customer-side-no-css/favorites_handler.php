<?php
session_start();
require 'config.php';
if (!isset($_SESSION['loggedin'], $_POST['product_id'])) exit(json_encode(['success'=>false]));

$userId = $_SESSION['id'];
$productId = intval($_POST['product_id']);

// Ensure favorites table exists:
// CREATE TABLE favorites (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, product_id INT, added_on DATETIME DEFAULT CURRENT_TIMESTAMP);

$stmt = $mysqli->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii",$userId,$productId);
$stmt->execute(); $stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($favId); $stmt->fetch(); $stmt->close();
    $stmt = $mysqli->prepare("DELETE FROM favorites WHERE id = ?");
    $stmt->bind_param("i",$favId);
    $stmt->execute(); $stmt->close();
    echo json_encode(['success'=>true,'action'=>'removed']);
} else {
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?,?)");
    $stmt->bind_param("ii",$userId,$productId);
    $stmt->execute(); $stmt->close();
    echo json_encode(['success'=>true,'action'=>'added']);
}
