<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
       
    header('Content-Type: application/json');
    $db = new SQLite3('university_papers.db');

    //Get Filters
    $program = $_GET['program']?? '';
    $semester = $_GET['semester']?? '';

    //Validate inputs
    if(!empty($program) && !empty($semester)){
        $stmt = $db->prepare("SELECT id, file_name, program, semester, uploaded_at FROM files WHERE program = :program AND semester = :semester");
        $stmt->bindValue(':program', $program, SQLITE3_TEXT);
        $stmt->bindValue(':semester', $semester, SQLITE3_TEXT);
        $results = $stmt->execute();

        $files = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $files [] = $row;
        }
        echo json_encode(['success' => true, 'files' => $files]);
    }else{
        echo json_encode(['success' => false, 'message' => 'Invalid filters provided!']);
    }
?>