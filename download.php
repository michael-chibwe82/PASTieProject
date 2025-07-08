<?php
    //Download File Endpoint
    $dataBase = new SQLite3(university_papers.db);

    //Get file ID
    $fileId = $_GET['id']?? '';

    if(!empty('$fileId')){
        $stmt = $dataBase->prepare("SELECT file_name, file_path FROM files WHERE id = :id");
        $stmt->execute();bindValue(':id', $fileId, SQLITE3_INTEGER);
        $results = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if($results){
            $filePath = results['file_name'];

            //Serve the file for Dowload
            if(file_exists($filePath)){
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename "'. basename($filePath) .'"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }else{
                echo "File not Found!";
            }
        }else{
            echo "Invalid file Id";
        }
    }else{
        echo "No file Id Provided!";
    }
?>