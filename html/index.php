<?php 
  session_start();
  require("functions.php");

   //IF NOT LOGGED IN (SAVED IN SESSION): SET ARRAYS EMPTY 
   //IF LOGGED IN: CONTINUE -> FETCH GROUPS AND SAVE TO ARRAYS
   if (!isset($_SESSION['user_id'])) {
      $my_groups = [];
      $other_groups = [];
       $pending_group_ids = [];
   } else {
      $user_id = $_SESSION['user_id'];
 
      //GET GROUPS WHERE USER IS MEMBER
       $sql = "SELECT g.id, g.name, g.description 
         FROM forum_groups g
         JOIN group_members gm ON gm.group_id = g.id
         WHERE gm.user_id = ?";

         $stmt = $db->prepare($sql);
         $stmt->execute([$user_id]);
         $my_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

      //GET GROUPS WHERE USER IS NOT A MEMBER
      $sql = "SELECT g.id, g.name, g.description
         FROM forum_groups g
         WHERE g.id NOT IN (
         SELECT group_id
         FROM group_members
         WHERE user_id = ?
        )";

         $stmt = $db->prepare($sql);
         $stmt->execute([$user_id]);

         $other_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);


      //CHECK IF USER HAS PENDING APPLICATION 
         $sql = "SELECT group_id
         FROM applications
         WHERE user_id = ?
         AND status = 'pending'";

         $stmt = $db->prepare($sql);
         $stmt->execute([$user_id]);

         $pending_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $pending_group_ids = [];

         foreach ($pending_rows as $row) {
            $pending_group_ids[] = $row['group_id'];
         }

      // FETCH APPLICATIONS WHERE USER ADMIN
          $sql = "SELECT a.id, a.status, a.created_at,
          g.id AS group_id, g.name AS group_name,
          u.first_name, u.last_name
          FROM applications a
          JOIN forum_groups g ON g.id = a.group_id
          JOIN group_members gm ON gm.group_id = g.id
          JOIN users u ON u.id = a.user_id
          WHERE gm.user_id = ?
          AND gm.role = 'admin'
          AND a.status = 'pending'";

          $stmt = $db->prepare($sql);
          $stmt->execute([$user_id]);
          $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
   }
    ?>


<html>

<head>
  <link rel="stylesheet" href="./css/menu.css">
  <link rel="stylesheet" href="./css/globals.css">
  <link rel="stylesheet" href="./css/homepage.css">
   <link rel="stylesheet" href="./css/show-group.css">
  <?php 
   $page_name = "Homepage"; 
   echo "<title>$page_name</title>"; ?>
</head>
<body>

<?php require 'menu.php'; ?>

<div class= "page-container">




<?php
if (isset($_SESSION['user_id'])) : ?>
<div class="dashboard-header">
       
    <div class = "dashboard-welcome">
        <h3>DASHBOARD</h3>
        <p>manage your groups, view applications, and update your account settings.</p>
       </div>
    
      <div class = "dashboard-user">
       
         <p>User: <?php echo htmlspecialchars($_SESSION['first_name']) ?></p>
      
        </div>

        </div>

        
<div class = "dashboard-grid">


 <div class = "box dashboard-box">
   <div class = "dashboard-box-header">
      <div class = "dashboard-box-header-text">
      <h2>Groups</h2>
      </div>
         <div class = "dashboard-box-link">
            <a href="create-group.php">+ </a>
      </div>
    </div>
   
            <?php if (empty($my_groups)) : ?>

            <p>You are not a member of any groups yet.</p>

        <?php else : ?>

          <div class = "group-container">
            
          <?php foreach ($my_groups as $group) : ?>
             <div class = "group-card">
              
             <div class = "group-card-header">
                <a href="show-group.php?id=<?= $group['id'] ?>">
                    <h3><?= htmlspecialchars($group['name']) ?></h3>
                    <p><?= htmlspecialchars($group['description']) ?></p>
                </a>
                </div>

                <div>-></div>
                
                </div>

            <?php endforeach; ?>
            

        <?php endif; ?>


    
      </div>
 </div>

 
 <div class = "box dashboard-box">
   <div class = "dashboard-box-header">
      <h2>Other groups</h2>
        
    </div>
   <div class = "group-container">
            <?php if (empty($other_groups)) : ?>

            <p>There are no other groups available.</p>

        <?php else : ?>

          
            
          <?php foreach ($other_groups as $group) : ?>
             <div class = "group-card">
              
             <div class = "group-card-header">
                
                    <h3><?= htmlspecialchars($group['name']) ?></h3>
                    <p><?= htmlspecialchars($group['description']) ?></p>
        </div>
               <div class = "group-card-status">
             <?php if (in_array($group['id'], $pending_group_ids)) : ?>

    <p class = "waiting-approval">Waiting Approval</p>

    
<?php else : ?>
<form method="POST" action="apply-group.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $group['id'] ?>">
    <button class="button-accept" type="submit">Apply</button>
</form>

<?php endif; ?>
        </div>

                
                </div>

            <?php endforeach; ?>
            

        <?php endif; ?>


    
      </div>
 </div>

  <div class = "box dashboard-box">
   <div class = "dashboard-box-header">
      <h2>Applications requests</h2>
     
    </div>
   
            <?php if (empty($applications)) : ?>

            <p>You currently have no applications pending.</p>

        <?php else : ?>

         
    
    <div class="group-container">

    <?php foreach ($applications as $application) : ?>
        <div class="group-card">

            <div class="group-card-header">
                <h3><?= htmlspecialchars($application['group_name']) ?></h3>
                <p>
                  From:
                    <?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?>
                   
                </p>
            </div>

            <div class="group-card-status-form">
              
            <div>
                <form class = "form-status-container" method="POST" action="handle-application.php" style="display:inline;">
                    <input type="hidden" name="application_id" value="<?= $application['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button class = "button-accept" type="submit" value="Approve">Approve</button>
                </form>
                </div>
              
              <div>
                <form class = "form-status-container" method="POST" action="handle-application.php" style="display:inline;">
                    <input type="hidden" name="application_id" value="<?= $application['id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button class = "button-reject" type="submit" value="Reject">Reject</button>
                </form>
                </div>

            </div>

        </div>

            <?php endforeach; ?>
            

        <?php endif; ?>


    
      </div>
 </div>

    </div>
    




<?php else: ?> 

    <div class = "not-logged-in-container">

      <div class="hero-not-logged-in">
        <h1>Community Forum</h1>
        <p>
           Join groups, discuss, and talk to people around the world in topics that you are interested in.
        </p>
        Login or create an account to start your journey.
        <div class="hero-buttons">
            <a href="create-account.php" class="button-primary">Skapa konto</a>
            <a href="login.php" class="button-secondary">Login</a>
        </div>
    </div>
    </div>
    <div class="features">
        <div class="feature-card">
            <h3>Create groups</h3>
            <p>Create your own groups in topics you find interesting.</p>
        </div>
        <div class="feature-card">
            <h3>Discuss</h3>
            <p>Join groups & discussions.</p>
        </div>
        <div class="feature-card">
            <h3>Take control</h3>
            <p>Administrate and make your own groups suiting, the exact way you intended it to be.</p>
        </div>
    </div>

  

   

     <?php endif; ?>







</body>
</html>