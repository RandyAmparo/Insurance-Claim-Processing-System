<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: index.html");
        exit;
    }

    // establishing varibales now to reference them later
    $message = "";
    $today = date("Y-m-d");
    $reviewed_by = $_SESSION['employee_id']; //current employee using the session

    // connect database stuff
    @$db = new mysqli('localhost', 'root', '', 'insurance_firm_344');

    if (mysqli_connect_errno()) {
       echo "<p>Error: Could not connect to database.<br/>
             Please try again later.</p>";
       exit;
    }

    // for denied claims, it sets the decicsion status to be denied, 
    // and takes todays date and marks the employee who reviewed it

    if (isset($_POST['action']) && $_POST['action'] == 'deny') {
        $claim_id = $_POST['claim_id']; //grabs the claim id that the employee clicked
        
        $query = "UPDATE Claims SET decision_status = 'Denied', decision_date = ?, reviewed_by = ? WHERE claim_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('sii', $today, $reviewed_by, $claim_id);
        $stmt->execute();
        $message = "<p style='color:red'>Claim #$claim_id DENIED.</p>"; // if a claim is denied say it in big fat red letters and DO NOT RECORD THE PAYMENT
        $stmt->close();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'approve') { // if a claim is approved SAy approved in big green letters AND THEN RECORD THE PAYMENT INTO THE DATABSE
        $claim_id = $_POST['claim_id'];
        
        $query = "SELECT claim_amount FROM Claims WHERE claim_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $claim_id);
        $stmt->execute();
        $stmt->bind_result($claim_amount);
        $stmt->fetch();
        $stmt->close();

        $db->begin_transaction(); // updates the claim details, and then inserts a record into payments. this make sures both queries pass

        try {
            $query = "UPDATE Claims SET decision_status = 'Approved', decision_date = ?, reviewed_by = ? WHERE claim_id = ?"; // UPDATE THE CLAIM
            $stmt = $db->prepare($query);
            $stmt->bind_param('sii', $today, $reviewed_by, $claim_id);
            $stmt->execute();
            $stmt->close();

            $query = "INSERT INTO Payments (claim_id, payment_amount, payment_date) VALUES (?, ?, ?)"; // UPDATE PAYMENTS
            $stmt = $db->prepare($query);
            $stmt->bind_param('ids', $claim_id, $claim_amount, $today);
            $stmt->execute();
            $stmt->close();

            $db->commit();
            $message = "<p style='color:green'>Claim #$claim_id APPROVED.</p>";
        } catch (Exception $e) {
            $db->rollback();
            $message = "<p style='color:red'>Transaction failed.</p>";
        }
    }

    // References used
    // transcations: https://www.php.net/manual/en/mysqli.begin-transaction.php 
    // & https://stackoverflow.com/questions/2708237/php-mysql-transactions-examples
    // & https://www.youtube.com/watch?v=e6yLUvpcOZo
    // & https://stackoverflow.com/questions/2159045/when-should-i-use-transactions-in-my-queries

?>

<!-- httmtlllllll -->
<!DOCTYPE html>
<html>
<head><title>Pending Claims</title>
 <link rel="stylesheet" href="pending_claims.css">
</head>
<body>
  <h1>Approve / Deny Claims</h1>
  <?php echo $message; ?>

  <?php

  // show me claims where its under review

    $query = "SELECT Claims.claim_id, Customers.first_name AS c_first, Customers.last_name AS c_last, Policies.coverage_type, Claims.claim_amount, Claims.cl_description, Claims.incident_date
              FROM Claims
              JOIN Policies  ON Claims.policy_id = Policies.policy_id
              JOIN Customers ON Policies.customer_id = Customers.customer_id
              WHERE Claims.decision_status = 'Under Review'
              ORDER BY Claims.claim_id ASC";

    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
      echo "<table border='1' cellpadding='5'>";
      echo "<tr><th>Claim ID</th><th>Customer</th><th>Coverage</th><th>Amount</th><th>Description</th><th>Incident</th><th>Action</th></tr>";

      while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['claim_id'] . "</td>";
        echo "<td>" . $row['c_first'] . " " . $row['c_last'] . "</td>";
        echo "<td>" . $row['coverage_type'] . "</td>";
        echo "<td>$" . number_format($row['claim_amount'], 2) . "</td>";
        echo "<td>" . $row['cl_description'] . "</td>";
        echo "<td>" . $row['incident_date'] . "</td>";
        echo "<td>";
        
        // THIS CODE IS RESPONSIBLE FOR THE BUTTONS!!! EDIT WISELY
        // https://www.w3schools.com/php/php_superglobals_post.asp

        echo "<form action='pending_claims.php' method='post' style='display:inline;'> 
                <input type='hidden' name='claim_id' value='" . $row['claim_id'] . "'>
                <input type='hidden' name='action' value='approve'>
                <input type='submit' value='Approve'>
              </form> ";
              
        echo "<form action='pending_claims.php' method='post' style='display:inline;'>
                <input type='hidden' name='claim_id' value='" . $row['claim_id'] . "'>
                <input type='hidden' name='action' value='deny'>
                <input type='submit' value='Deny'>
              </form>";
        echo "</td></tr>";
      }
      echo "</table>";
    } else {
      echo "<p>No pending claims at this time. You're all caught up! :)</p>";
    }
    $db->close();
  ?>
  
  <p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
