<?php 
  session_start();
  require("functions.php");

  $result = $db->query("SELECT * FROM users");
  $users = $result->fetchAll();
  //var_dump($users);
    ?>


<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <link rel="stylesheet" href="./css/homepage.css">
  <?php 
   $page_name = "Homepage"; 
   echo "<title>$page_name</title>"; ?>
</head>
<body>

<?php require 'menu.php'; ?>

<div class= "page-container">


<?php
if (isset($_SESSION['user_id'])) {
    echo "<p>Welcome " . htmlspecialchars($_SESSION['first_name']) . "</p>";
    echo '<a href="logout.php">Log out</a>';
} else {
    echo "<p>You are not logged in.</p>";
    echo '<a href="login.php">Log in</a> | <a href="register.php">Create account</a>';
}
?>

<div class = "dashboard-grid">
 <div class = "box">hej</div>
  <div class = "box"></div>
   <div class = "box"></div>


</div>
</div>
</body>
</html>