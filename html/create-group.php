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
  <link rel="stylesheet" href="./css/homepage.css">
   <link rel="stylesheet" href="./css/show-group.css">
  <title>Create group</title>
</head>
<body>

<?php require 'menu.php'; ?>


<div class = "page-container">

<div class="dashboard-header">


<?php
if (isset($_SESSION['user_id'])) : ?>
       
    <div class = "dashboard-welcome">
        <h3>DASHBOARD</h3>
        <p>manage your groups, view applications, and update your account settings.</p>
       </div>
    
      <div class = "dashboard-user">
         <p>User: <?php echo htmlspecialchars($_SESSION['first_name']) ?></p>
        </div>

        </div>

<?php if ($error) {
    echo '<p class="error">' . $error . '</p>';
} ?>

<div class = "forum-header">
        <a href="index.php" class="button-back">
        <-
    </a>
    </div>


     <div class="reply-container center">
<div class = "form-card style-create">
<form method="POST" action="" class = "form-container">
    <label for="name">Group name:</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Group description:</label>
    <input type="text" id="description" name="description" required>

    <button type="submit" class = "button-primary" value="Skapa grupp">Create</button>
</form>
</div>
</div>
</div>

<?php else: ?> 
     <p>You are not logged in.</p>
     <a href="login.php">Log in</a> | <a href="register.php">Create account</a>;
     <?php endif; ?>


</body>
</html>