<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure only seller/admins can update orders
$stmt = $mysqli->prepare("SELECT user_type_id FROM user_login WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_type_id);
$stmt->fetch();
$stmt->close();

if ($user_type_id != 2) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$order_id || !$action) {
        header("Location: orderlogs.php?error=Missing+order+data");
        exit;
    }

    if ($action === 'confirm') {
        // 1. Update status
        $status = 'Preparing';
        $stmt = $mysqli->prepare("UPDATE trans_header SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();

        // 2. Deduct inventory
        $stmt = $mysqli->prepare("SELECT product_id, qty_out FROM trans_details WHERE trans_header_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $qty_out = $row['qty_out'];

            $updateStmt = $mysqli->prepare("UPDATE product_inventory SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
            $updateStmt->bind_param("iii", $qty_out, $product_id, $qty_out);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $stmt->close();

        header("Location: orderlogs.php?success=Order+confirmed+and+inventory+updated");
        exit;

    } elseif ($action === 'cancel') {
        // Update to cancelled only (no inventory change)
        $status = 'Cancelled';
        $stmt = $mysqli->prepare("UPDATE trans_header SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();

        header("Location: orderlogs.php?success=Order+cancelled");
        exit;
    }
}

header("Location: orderlogs.php?error=Invalid+action");
exit;
