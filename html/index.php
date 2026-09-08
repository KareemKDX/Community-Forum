<?php 
  session_start();
  require("functions.php");

   //IF NOT LOGGED IN: SET EMPTY ARRAY
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

        
   }
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

<div class="dashboard-header">


<?php
if (isset($_SESSION['user_id'])) : ?>
       
    <div class = "dashboard-welcome">
        <h3>DASHBOARD</h3>
        <p>manage your groups, view applications, and update your account settings.</p>
       </div>
    
      <div class = "dashboard-user">
       
         <p>Logged in: <?php echo htmlspecialchars($_SESSION['first_name']) ?></p>
        <a href="logout.php">Log out</a>
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
   
            <?php if (empty($other_groups)) : ?>

            <p>You are not a member of any groups yet.</p>

        <?php else : ?>

          <div class = "group-container">
            
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

    <a class = "apply" href="apply-group.php?id=<?= $group['id'] ?>">
        Apply
    </a>

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

            <p>You are not a member of any groups yet.</p>

        <?php else : ?>

          <div class = "group-container">
            
          <?php foreach ($applications as $application) : ?>
             <div class = "group-card">
              
             <div class = "group-card-header">
                <a href="show-application.php?id=<?= $group['id'] ?>">
                    <h3><?= htmlspecialchars($group['name']) ?></h3>
                    <p><?= htmlspecialchars($application['status']) ?></p>
                </a>
                </div>

                <div>Apply</div>
                
                </div>

            <?php endforeach; ?>
            

        <?php endif; ?>


    
      </div>
 </div>

    
    




<?php else: ?> 
     <p>You are not logged in.</p>
     <a href="login.php">Log in</a> | <a href="register.php">Create account</a>;
     <?php endif; ?>





</div>
</div>
</body>
</html>