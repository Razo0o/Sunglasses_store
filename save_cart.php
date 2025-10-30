<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

if (isset($_SESSION['username']) && isset($data['items'])) {
    $_SESSION['cart'] = $data;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => false]);
}