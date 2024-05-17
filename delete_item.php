<?php
require '../config/database.php';
require '../src/Calculator.php';
session_start();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$calculator = new Calculator($pdo);
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $id, 'user_id' => $userId]);

    if ($stmt->rowCount() > 0) {
        $items = $calculator->getItems($userId);
        $total = $calculator->calculateTotal($items);

        $response['success'] = true;
        $response['total'] = $total;
    } else {
        $response['message'] = 'Item not found or not authorized.';
    }
}

echo json_encode($response);
?>
