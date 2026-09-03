<?php
session_start();
require("functions.php");


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST["name"];
    $description = $_POST['description'];

    if ($name === '') {
        $error = "Group name has to be added";
   
        } else {
        
    // INSERT GROUP TO DATABASE
        $sql = "INSERT INTO forum_groups (name, description, created_by) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $description, $_SESSION['user_id']]);

       
        $group_id = $db->lastInsertId();

        // CREATE GROUP MEMBER AND MAKE CREATOR ADMIN AUTOMATICALLY
        $sql = "INSERT INTO group_members (user_id, group_id, role) VALUES (?, ?, 'admin')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $group_id]);

        header('Location: index.php');
        exit;
    }
}
?>

<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <title>Create group</title>
</head>
<body>

<?php require 'menu.php'; ?>

<h1>CREATE GROUP</h1>

<?php if ($error) {
    echo '<p class="error">' . $error . '</p>';
} ?>

<form method="POST" action="">
    <label for="name">Group name:</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Group description:</label>
    <input type="text" id="description" name="description" required>

    <input type="submit" value="Skapa grupp">
</form>

</body>
</html>