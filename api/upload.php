<?php
header('Content-Type: application/json');

$host = "localhost";
$dbname = "past_papers";
$username = "root";
$password = "";

// Connect to the DB
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Database Connection Failed: " . $conn->connect_error]));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['paperTitle'] ?? '';
    $year = $_POST['courseYear'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $file = $_FILES['pdfFile'] ?? null;

    // Validate inputs
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Paper title is required']);
        exit;
    }

    if (empty($year)) {
        echo json_encode(['success' => false, 'message' => 'Year is required']);
        exit;
    }

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit;
    }

    // Upload folder
    $uploadDir = 'uploads/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate unique filename
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $fileExt;
    $targetFile = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Save metadata to DB
        $stmt = $conn->prepare("INSERT INTO past_papers (title, year, semester, filename, filepath) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siss", $title, $year, $semester, $filename, $targetFile);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error moving uploaded file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>