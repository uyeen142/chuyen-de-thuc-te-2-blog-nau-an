<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Không hợp lệ']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

$stmt = $conn->prepare("DELETE FROM favorite_posts WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$success = $stmt->execute();
$stmt->close();

echo json_encode(['success' => $success]);
?>
