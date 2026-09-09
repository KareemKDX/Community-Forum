<?php
session_start();
require("functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$application_id = $_POST['application_id'];
$action = $_POST['action'];

// FETCH APPLICATION + USER
$sql = "SELECT id, user_id, group_id, status FROM applications WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$application_id]);
$application = $stmt->fetch();

if (!$application) {
    die("Ansökan finns inte.");
}

// CHECK IF USER IS ACTUALLY ADMIN 
$sql = "SELECT role FROM group_members WHERE user_id = ? AND group_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $application['group_id']]);
$my_membership = $stmt->fetch();

if (!$my_membership || $my_membership['role'] !== 'admin') {
    die("Du har inte behörighet att hantera denna ansökan.");
}

// IF APPROVE
if ($action === 'approve') {
    // ADD USER
    $sql = "INSERT INTO group_members (user_id, group_id, role) VALUES (?, ?, 'member')";
    $stmt = $db->prepare($sql);
    $stmt->execute([$application['user_id'], $application['group_id']]);

    // REMOVE APPLICATION FROM TABLE AFTER APPROVE
    $sql = "DELETE FROM applications WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$application_id]);


    //IF REJECTED
} elseif ($action === 'reject') {
    // UPDATE STATUS TO REJECTED
    $sql = "UPDATE applications SET status = 'rejected' WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$application_id]);
}

header("Location: index.php");
exit;