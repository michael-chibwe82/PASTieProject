<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Download File Endpoint
    $dataBase = new SQLite3('university_papers.db');  // Correct database connection path

    // Get file ID
    $fileId = $_GET['id'] ?? '';

    if (!empty($fileId)) {
        $stmt = $dataBase->prepare("SELECT file_name, file_path FROM files WHERE id = :id");
        $stmt->bindValue(':id', $fileId, SQLITE3_INTEGER);  // Correct placement of bindValue()
        $results = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($results) {
            $filePath = $results['file_path'];  // Correct variable name and access

            // Serve the file for Download
            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');  // Corrected header format
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo "File not Found!";
            }
        } else {
            echo "Invalid file Id";
        }
    } else {
        echo "No file Id Provided!";
    }
?>
