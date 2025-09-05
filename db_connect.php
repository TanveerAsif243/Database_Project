<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cse370_project";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection Failed: ". $conn->connect_error);
}else{
    echo "Connection ok";
    mysqli_select_db($conn, $dbname);
}
?>
   
  