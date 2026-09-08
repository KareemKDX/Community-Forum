<?php
session_start();
require("functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$discussion_id = $_GET['id'];

// FETCH DISCUSSIONS AND JOIN USER THAT CREATED IT
$sql = "SELECT d.id, d.title, d.description, d.group_id, d.created_at,
               u.first_name, u.last_name
        FROM discussions d
        JOIN users u ON u.id = d.user_id
        WHERE d.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$discussion_id]);
$discussion = $stmt->fetch();

if (!$discussion) {
    die("Diskussionen finns inte.");
}

// CHECK THAT USER IS MEMBER 
$sql = "SELECT role FROM group_members WHERE user_id = ? AND group_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $discussion['group_id']]);
$membership = $stmt->fetch();

if (!$membership) {
    die("Du är inte medlem i den grupp denna diskussion tillhör.");
}

$error = '';

// ADD POST TO DISCUSSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $content = trim($_POST['content']);

    if ($content === '') {
        $error = "Svaret kan inte vara tomt.";
    } else {
        $sql = "INSERT INTO posts (content, user_id, discussion_id) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$content, $user_id, $discussion_id]);

        header('Location: show-discussion.php?id=' . $discussion_id);
        exit;
    }
}

// FETCH ALL POSTS IN DISCUSSION
$sql = "SELECT p.id, p.content, p.created_at, u.first_name, u.last_name
        FROM posts p
        JOIN users u ON u.id = p.user_id
        WHERE p.discussion_id = ?
        ORDER BY p.created_at ASC";
$stmt = $db->prepare($sql);
$stmt->execute([$discussion_id]);
$posts = $stmt->fetchAll();
?>

<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <link rel="stylesheet" href="./css/show-group.css">
  <title><?= htmlspecialchars($discussion['title']) ?></title>
</head>

<body>

<?php require 'menu.php'; ?>

<div class="page-container">
    <div class="forum-container">
  <div class = "forum-header">
        <a href="index.php" class="button-back">
        <-
    </a>
    </div>
        

        <div class="discussion-container">

          <div class="discussion-card">
            <div class="discussion-header">

                    <img
                        src="./images/profileimg.jpg"
                        alt="Profile picture"
                        class="profile-picture"
                    >

                    <div class="discussion-header-text">

                        <h4>
                            <?= htmlspecialchars($discussion['first_name'] . ' ' . $discussion['last_name']) ?>
                        </h4>

                        <p>
                            <?= htmlspecialchars($discussion['created_at']) ?>
                        </p>

                    </div>

                </div>


                <div class="discussion-content">

                    <h3>
                        <?= htmlspecialchars($discussion['title']) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($discussion['description']) ?>
                    </p>

                </div>
                  <div class = "discussion-items">
                   <p>Comments (<?= count($posts) ?>)</p>
                   </div>
            </div>

            
           <?php if ($error) { ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php } ?>


            <?php if (count($posts) === 0) { ?>

                <p>No comments yet.</p>

            <?php } else { ?>

                <?php foreach ($posts as $post) { ?>

                    <div class="discussion-post-card">

                        <div class="discussion-header">

                            <img
                                src="./images/profileimg.jpg"
                                alt="Profile picture"
                                class="profile-picture"
                            >

                            <div class="discussion-header-text">

                                <h4>
                                    <?= htmlspecialchars(
                                        $post['first_name'] . ' ' . $post['last_name']
                                    ) ?>
                                </h4>

                                <p>
                                    <?= htmlspecialchars($post['created_at']) ?>
                                </p>

                            </div>

                        </div>

                        <div class="discussion-content">
                       <p>
                                <?= htmlspecialchars($post['content']) ?>
                            </p>

                        </div>

                    </div>

                <?php } ?>

            <?php } ?>


            <div class="reply-container">

                <h3>Add comment</h3>

                <form method="POST" action="">

                    <textarea
                        name="content"
                        required
                    ></textarea>

                    <input
                        type="submit"
                        value="Svara"
                    >

                </form>

            </div>

        </div>

    </div>
</div>

</body>
</html>

