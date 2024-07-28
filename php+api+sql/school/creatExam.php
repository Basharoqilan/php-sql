<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

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

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'POST') {
    $inputdata = json_decode(file_get_contents("php://input"), true);
    
    if (empty($inputdata)) {
        $inputdata = $_POST;
    }

    echo storeExam($inputdata);
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

function error422($message)
{
    $data = [
        'status' => 422,
        'message' => $message
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function storeExam($examInput)
{
    global $connection;

    $SubjectID = $examInput['SubjectID'] ?? '';
    $ExamDate = $examInput['ExamDate'] ?? '';
    $MaxScore = $examInput['MaxScore'] ?? '';

    if (empty(trim($ExamDate))) {
        return error422('Enter the ExamDate');
    }  elseif (empty(trim($SubjectID))) {
        return error422('Enter the SubjectID');
    }elseif (empty(trim($MaxScore))) {
        return error422('Enter the MaxScore');
    } else {
        $sql = "INSERT INTO exams (SubjectID,ExamDate, MaxScore) VALUES (?,?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iss", $SubjectID,$ExamDate, $MaxScore);

        if ($stmt->execute()) {
            $data = [
                'status' => 201,
                'message' => 'Exam created successfully'
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error: ' . $stmt->error
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }

        $stmt->close();
    }
}

$connection->close();
?>