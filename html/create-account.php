<?php
session_start();
require("functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    //SAVE VALUES FROM FORM TO VARIABLES
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // INSERT USER TO DATABASE
    $sql = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->execute([$first_name, $last_name, $email, $password_hash]);

    $account_created = true;
}


?>





<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="./css/globals.css">
  <?php 
   $page_name = "Login";
    echo "<title>$page_name</title>"; ?>
</head>
<body>
 
<?php require 'menu.php'; ?>

 <div class="form-page-container">

 <div class= "form-card">

 
  <div class = "form-header">
    <h2>Registration </h2>
</div>


  <form method="POST" action="" class="form-container">
   
      <div class="form-group"> 
      <label for="first_name">First Name:</label>
      <input type="text" id="first_name" name="first_name" required>
      </div>

      <div class="form-group"> 
      <label for="last_name">Last name:</label>
      <input type="text" id="last_name" name="last_name" required>
      </div>

      <div class="form-group"> 
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group"> 
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      </div>
    <button class = "button-primary" type="submit" value="Create Account">Create Account</button>

  </form>

 <p>Already a member? <a href="create-account.php">Sign in here</a></p>
   </div>
  </div>
</div>

  <?php
  if (isset($account_created)) {
      echo "<p>User created successfully.</p>";
  }
  ?>



</body>
</html>