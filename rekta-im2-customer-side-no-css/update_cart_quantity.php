<?php
session_start();
require_once 'config.php';

$detail_id = $_POST['trans_detail_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$detail_id || $quantity < 1) {
    echo "Invalid data.";
    exit;
}

// Get product price again for safety
$stmt = $mysqli->prepare("
    SELECT price
    FROM trans_details
    WHERE id = ?
");
$stmt->bind_param("i", $detail_id);
$stmt->execute();
$stmt->bind_result($price);
if ($stmt->fetch()) {
    $amount = $price * $quantity;
    $stmt->close();

    $update = $mysqli->prepare("UPDATE trans_details SET qty_out = ?, amount = ? WHERE id = ?");
    $update->bind_param("idi", $quantity, $amount, $detail_id);
    if ($update->execute()) {
        header("Location: add_to_cart.php");
        exit;
    } else {
        echo "Failed to update quantity.";
    }
} else {
    echo "Item not found.";
}
