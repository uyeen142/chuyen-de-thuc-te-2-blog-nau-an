<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = intval($_POST['post_id']);

$stmt = $conn->prepare("SELECT 1 FROM favorite_posts WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $userId, $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $ins = $conn->prepare("INSERT INTO favorite_posts (user_id, post_id) VALUES (?, ?)");
    $ins->bind_param("ii", $userId, $postId);
    $ins->execute();
}

echo json_encode(['success' => true]);
