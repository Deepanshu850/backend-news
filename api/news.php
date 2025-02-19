<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit();
}

$conn = getConnection();

$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : null;

$sql = "SELECT n.*, c.name as category_name 
        FROM news n 
        LEFT JOIN categories c ON n.category_id = c.id";

if ($category) {
    $sql .= " WHERE c.name = '$category'";
}

$sql .= " ORDER BY n.created_at DESC";

$result = $conn->query($sql);
$news = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'imageUrl' => $row['image_url'],
            'category' => $row['category_name'],
            'date' => $row['created_at'],
            'sponsored' => (bool)$row['sponsored'],
            'company' => $row['company'],
            'cta' => $row['cta'],
            'url' => $row['url']
        ];
    }
}

echo json_encode($news);
$conn->close();