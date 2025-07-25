<?php
// Database configuration
$host = 'localhost';
$dbname = 'past_papers';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate inputs
        $required = ['paperTitle', 'courseYear', 'semester', 'pdfFile'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                die("Error: $field is required");
            }
        }

        // File upload handling
        $file = $_FILES['pdfFile'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            die("File upload error: " . $file['error']);
        }

        // Validate file type
        $allowedTypes = ['application/pdf'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Only PDF files are allowed");
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            die("Error: File too large. Maximum size is 5MB");
        }

        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $originalName = basename($file['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $newFilename;

        // Move the file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            die("Error: Failed to move uploaded file");
        }

        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO past_papers 
            (course_id, year, semester, file_name, file_path, file_size, uploaded_by)
            VALUES (:course_id, :year, :semester, :file_name, :file_path, :file_size, :uploaded_by)
        ");

        // For this example, we'll use course_id = 1 (ICT)
        // In a real app, you'd get this from a form or session
        $stmt->execute([
            ':course_id' => 1,
            ':year' => $_POST['courseYear'],
            ':semester' => $_POST['semester'],
            ':file_name' => $_POST['paperTitle'] . '.pdf',
            ':file_path' => $destination,
            ':file_size' => $file['size'],
            ':uploaded_by' => 1 // Default admin user
        ]);

        // Success - redirect back with success message
        header('Location: index.php?upload=success');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}