<?php
//Database Connection Settings

$servername = "localhost";
$username = "root"; //Default for xampp
$password = ""; // Default is empty
$dbname = "past_papers";

//Connect to mysql
$conn = new mysqli ($servername, $username, $password, $dbname);

//Check Connection
if($conn -> connect_error){
    die("Connection failed: " . $conn -> connect_error);
}

//Get form Data

$name = $_POST['name'];
$email = $_POST['email'];

//Insert into Database

$sql = "INSERT INTO users (username, email) VALUES ('$name', '$email')";

if($conn->query($sql) === TRUE) {
    echo "Record saved Successfully!";
}else {
    echo "Error!" . $conn->error;
}

$conn->close();

?>