<?php
session_start();
if (!isset($_SESSION["expert_id"])) {
    header("Location: login.html");
    exit();
}
$expertName = $_SESSION["expert_name"] ?? "خبير";
$expertEmail = $_SESSION["email"] ?? "";
?>