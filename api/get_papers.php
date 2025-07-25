<?php
header('Content-Type: application/json');

$host = "localhost";
$dbname = "past_papers";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => "Database Connection Failed: " . $conn->connect_error]));
}

$sql = "SELECT id, title, year, semester FROM past_papers ORDER BY year DESC, semester";
$result = $conn->query($sql);

$papers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $papers[] = $row;
    }
}

echo json_encode($papers);
$conn->close();
?>