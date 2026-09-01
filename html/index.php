
  <?php 
 
  require("functions.php");


$result = $db->query("SELECT * FROM users");
$users = $result->fetchAll();
var_dump($users);
  ?>


<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">

  <link rel="stylesheet" href="./css/globals.css">
  <?php 
  $page_name = "PHP PAGE";
  $page_name = "PHP PAGE"; echo "<title>$page_name</title>"; ?>
</head>
<body>
 
<?php require 'menu.php'; ?>

<?php 
echo "<h1>$page_name</h1>";
echo '<img src="picture1.jpeg" alt="Min bild">';
?>

</body>
</html>