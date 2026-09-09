<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

session_start();
require("functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['id'];

$sql = "INSERT INTO applications (status, user_id, group_id)
        VALUES ('pending', ?, ?)";

$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $group_id]);

header("Location: index.php");
exit;