<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "wash_wave";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>