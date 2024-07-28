<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$database = "my_school";  

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die(json_encode([
        'status' => 500,
        'message' => 'Connection failed: ' . $connection->connect_error
    ]));
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $connection->prepare('SELECT * FROM exams WHERE ExamID  = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams = $result->fetch_assoc();

    if ($exams) {
        echo json_encode($exams);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'exams not found']);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid exams ID']);
}
?>
