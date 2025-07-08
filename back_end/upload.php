<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Content-Type: application/json");

    // Database Connection
    try {
        $db = new SQLite3('university_paper.db');
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to connect to database!", "error" => $e->getMessage()]);
        exit;
    }

    // Check if a file is uploaded
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $program = trim($_POST['program']);
        $semester = trim($_POST['semester']);

        // Validate input
        if ($file['error'] === 0 && !empty($program) && !empty($semester)) {
            $uploadDir = "uploads/";
            
            // Ensure uploads directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filePath = $uploadDir . basename($file['name']);

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Ensure the database table exists
                $db->exec("CREATE TABLE IF NOT EXISTS files (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    file_name TEXT,
                    file_path TEXT,
                    program TEXT,
                    semester TEXT,
                    uploaded_at DATETIME
                )");
            
                // Save metadata to the database
                $stmt = $db->prepare("INSERT INTO files (file_name, file_path, program, semester) VALUES (:file_name, :file_path, :program, :semester)");
                $stmt->bindValue(':file_name', $file['name'], SQLITE3_TEXT);
                $stmt->bindValue(':file_path', $filePath, SQLITE3_TEXT);
                $stmt->bindValue(':program', $program, SQLITE3_TEXT);
                $stmt->bindValue(':semester', $semester, SQLITE3_TEXT);
                $stmt->execute();

                //Update the uploaded at Column if its NULL
                $UpdatedStmt = $db->prepare("UPDATE files SET uploaded_at = CURRENT_TIMESTAMP WHERE uploaded_at IS NULL");
                $UpdatedStmt->execute();

                // Return a success message
                echo json_encode(["success" => true, "message" => "File uploaded successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to upload file!"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input!"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request!"]);
    }
?>
