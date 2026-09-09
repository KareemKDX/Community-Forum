<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    echo '
    <nav class="navbar">
    <div class ="nav-logo">
     <h4>FORUM</h4>
     </div>
        <div class="navbar-links">
       
            <a href="index.php">Home</a>
            </div>
            <div class = "nav-user">
            <a href="logout.php" class="navbar-login">Log out</a>
            </div>
        </div>
    </nav>
    ';
} else {
    echo '
    <nav class="navbar">
     <div class ="nav-logo">
     <h4>FORUM</h4>
     </div>
        <div class="navbar-links">
         <a href="index.php">Home</a>
              </div>
               <div class = "nav-user">
            <a href="create-account.php" class="navbar-create">Create Account</a>
            <a href="login.php" class="navbar-login">Login</a>
               </div>
        </div>
    </nav>
    ';
}
?>