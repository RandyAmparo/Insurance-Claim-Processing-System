<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: index.html");
        exit;
    }
?>

<!-- html for da menu-->
<!DOCTYPE html>
<html>
<head><title>Main Menu</title>
<link rel="stylesheet" href="main_menu.css">
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>
  <p>Employee ID: <?php echo $_SESSION['employee_id']; ?></p>
  <hr>
  
  <h2>What would you like to do?</h2>
  <ul>
    <li><a href="view_claims.php">View Claims (Approved / Denied)</a></li>
    <li><a href="add_claim.php">File a New Claim</a></li>
    <li><a href="pending_claims.php">Approve / Deny Claims</a></li>
  </ul>
  
  <hr>
  <p><a href="logout.php">Log Out</a></p>
</body>
</html>
