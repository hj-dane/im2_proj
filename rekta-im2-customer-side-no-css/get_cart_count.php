<?php
session_start();
require 'config.php';
$count = 0;
if (isset($_SESSION['loggedin'])) {
    $userId = $_SESSION['id'];
    $stmt = $mysqli->prepare("SELECT th.id FROM trans_header th WHERE th.customer_id = ? AND th.trans_type_id = 1 AND th.status = 'cart'");
    $stmt->bind_param("i",$userId);
    $stmt->execute(); $stmt->bind_result($cartId);
    if ($stmt->fetch()) {
        $stmt->close();
        $stmt = $mysqli->prepare("SELECT SUM(qty_in) FROM trans_details WHERE trans_header_id = ?");
        $stmt->bind_param("i",$cartId);
        $stmt->execute(); $stmt->bind_result($count);
        $stmt->fetch(); $stmt->close();
    } else {
        $stmt->close();
    }
}
echo json_encode(['count'=>intval($count)]);
