<?php
session_start();
require("functions.php");

// Skydda sidan - måste vara inloggad
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];

// Kolla att gruppen finns
$sql = "SELECT id, name FROM forum_groups WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    die("Gruppen finns inte.");
}

// CHECK IF MEMBER
$sql = "SELECT role FROM group_members WHERE user_id = ? AND group_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $group_id]);
$membership = $stmt->fetch();

if (!$membership) {
    die("Du är inte medlem i denna grupp.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title === '' || $description === '') {
        $error = "Både ämne och beskrivning måste fyllas i.";
    } else {
        $sql = "INSERT INTO discussions (title, description, user_id, group_id) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$title, $description, $user_id, $group_id]);

        $discussion_id = $db->lastInsertId();

        header('Location: show-discussion.php?id=' . $discussion_id);
        exit;
    }
}
?>

<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <title>Starta diskussion</title>
</head>
<body>

<?php require 'menu.php'; ?>

<h1>Starta diskussion i <?= htmlspecialchars($group['name']) ?></h1>

<?php if ($error) { ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php } ?>

<form method="POST" action="">
    <label for="title">Ämne:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Beskrivning:</label>
    <textarea id="description" name="description" required></textarea>

    <input type="submit" value="Starta diskussion">
</form>

</body>
</html>