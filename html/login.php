<?php
session_start();
require("functions.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, first_name, password_hash FROM users WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Fel e-post eller lösenord.';
    }
}
?>

<html>

<head>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="./css/globals.css">
  <link rel="stylesheet" href="./css/menu.css">
  <?php 
   $page_name = "Login";
    echo "<title>$page_name</title>"; ?>
</head>
<body>

<?php require 'menu.php'; ?>



<div class="form-page-container">


<div class = "form-card">

  <div class = "form-header">
    <h2>Login </h2>
</div>

  <form method="POST" action="" class = "form-container">

    <div class = "form-group">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    </div>

    <div class = "form-group">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    </div>

    <button class = "button-primary" type="submit" value="Log in">Login</button>

    
<?php
if ($error) {
    echo '<p class="error-text">' . $error . '</p>';
}
?>
     

  </form>
<p>No account yet? <a href="create-account.php">Press here</a></p>
</div>
</div>

</body>
</html>