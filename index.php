<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tira_space";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>

