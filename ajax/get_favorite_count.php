<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'count' => 0, 'message' => 'Chưa đăng nhập']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM favorite_posts WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'count' => (int)($result['total'] ?? 0)
]);

$stmt->close();
$conn->close();
?>
