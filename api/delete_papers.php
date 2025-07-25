<?php
header('Content-Type: application/json');

$host = "localhost";
$dbname = "Past_Paper";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Database Connection Failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Paper ID is required']);
        exit;
    }

    // First get the file path to delete the physical file
    $stmt = $conn->prepare("SELECT filepath FROM past_papers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Paper not found']);
        exit;
    }
    
    $paper = $result->fetch_assoc();
    $filepath = $paper['filepath'];
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM past_papers WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete the file
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        echo json_encode(['success' => true, 'message' => 'Paper deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting paper: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>