<?php
$host = 'localhost';
$db = 'b2b';
$user = 'root';
$pass = ''; // Add your MySQL password if any

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
