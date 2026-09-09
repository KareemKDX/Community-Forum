<?php
session_start();
require("functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['id'];

// FETCH GROUP INFO
$sql = "SELECT id, name, description FROM forum_groups WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    die("Group not found.");
}

// CHECK IF USER IS MEMBER
$sql = "SELECT role FROM group_members WHERE user_id = ? AND group_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch();

$is_member = $membership !== false;

// FETCH ALL DISCUSSIONS IF USER MEMBER IS VALIDATED
$discussions = [];
if ($is_member) {
    $sql = "SELECT d.id, d.title, d.description, d.created_at, u.first_name, u.last_name
            FROM discussions d
            JOIN users u ON u.id = d.user_id
            WHERE d.group_id = ?
            ORDER BY d.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$group_id]);
    $discussions = $stmt->fetchAll();
}


?>

<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <link rel="stylesheet" href="./css/show-group.css">

  <title><?= 
   htmlspecialchars($page_name) ?></title>
</head>
<body>

<?php require 'menu.php'; ?>


<div class = "page-container">
    <div class = "forum-container">
<div class = "forum-wrapper">
    <div class = "forum-header">
        <a href="index.php" class="button-back">
        <-
    </a>
    </div>
<div class = "forum-header">
<h1><?= htmlspecialchars($group['name']) ?></h1>
<p><?= htmlspecialchars($group['description']) ?></p>


<?php if (!$is_member) { ?>

    <p>You are not a member in this group. Error.</p>

<?php } else { ?>


     <div class = "forum-create-button">
    <a href="create-discussion.php?group_id=<?= $group['id'] ?>">
        + Start a new discussion</a>
    </div>

  
         </div>

    <?php if (count($discussions) === 0) { ?>
    <div class = "empty-message">
        <p>No discussions added to this group yet.</p>
        </div>
    <?php } else { ?>

      
        <ul>

        <div class = "discussion-container">
              <h2>Discussions</h2>
            <?php foreach ($discussions as $discussion) { ?>

           <a href="show-discussion.php?id=<?= $discussion['id'] ?>">
            <div class = "discussion-card">

            <div class = "discussion-header">
           
      
            <img 
            src="./images/profileimg.jpg" 
            alt="Profile picture"
            class="profile-picture"
            >
               
            <div class = "discussion-header-text">
            <h4>
              <?= htmlspecialchars($discussion['first_name']
             . ' ' . $discussion['last_name']) ?>
             </h4>
            
            <p>
              <?= $discussion['created_at'] ?>
            </p>
              </div>
                
            </div>

            <div class = "discussion-content">
              <h3>
            <?= htmlspecialchars($discussion['title']) ?>
        </h3>
            
            <p>
              <?=  htmlspecialchars($discussion['description'])  ?>
         </p>

            </div>
                  

                </div>
                </a>
            <?php } ?>
            </div>
        </ul>
    <?php } ?>

<?php } ?>
</div>
</div>
</div>
</body>
</html>