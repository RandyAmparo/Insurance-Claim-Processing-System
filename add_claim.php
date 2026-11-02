<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: index.html");
        exit;
    }

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // create short variable names
        $c_first_name   = $_POST['c_first_name'];
        $c_last_name    = $_POST['c_last_name'];
        $coverage_type  = $_POST['coverage_type'];
        $incident_date  = $_POST['incident_date'];
        $claim_amount   = doubleval($_POST['claim_amount']); // so it can be decimal
        $cl_description = $_POST['cl_description'];

        // we are not worrying about the customer information for now, 
        // when making a new claim we want to be quick so ill just ask for their name
        // so we are using placeholder information.

        $today         = date("Y-m-d");
        $future_date   = date("Y-m-d", strtotime("+1 year"));
        $dummy_address = "Pending Update";
        $dummy_phone   = "000-000-0000";
        $dummy_limit   = 50000;

        @$db = new mysqli('localhost', 'root', '', 'insurance_firm_344'); // connecting php

        if (mysqli_connect_errno()) {
           echo "<p>Error: Could not connect to database.<br/>
                 Please try again later.</p>";
           exit;
        }

        // References used:
        // transcations: https://www.php.net/manual/en/mysqli.begin-transaction.php 
        // & https://stackoverflow.com/questions/2708237/php-mysql-transactions-examples
        // & https://www.youtube.com/watch?v=e6yLUvpcOZo
        // & https://stackoverflow.com/questions/2159045/when-should-i-use-transactions-in-my-queries

        $db->begin_transaction(); // basically makes a new customer, creates a policy id that links to our new customer, and all links to the new claim! 

        try {
            $query = "INSERT INTO Customers (first_name, last_name, address, c_number, join_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssss', $c_first_name, $c_last_name, $dummy_address, $dummy_phone, $today);
            $stmt->execute();
            $new_customer_id = $stmt->insert_id;
            $stmt->close();

            $query = "INSERT INTO Policies (customer_id, coverage_type, coverage_limit, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param('isiss', $new_customer_id, $coverage_type, $dummy_limit, $today, $future_date);
            $stmt->execute();
            $new_policy_id = $stmt->insert_id;
            $stmt->close();

            $query = "INSERT INTO Claims (policy_id, incident_date, cl_description, claim_amount, decision_status) VALUES (?, ?, ?, ?, 'Under Review')";
            $stmt = $db->prepare($query);
            $stmt->bind_param('issd', $new_policy_id, $incident_date, $cl_description, $claim_amount);
            $stmt->execute();
            $new_claim_id = $stmt->insert_id;
            $stmt->close();

            $db->commit();
            $message = "<p style='color:green'>Success! Claim #$new_claim_id filed for $c_first_name $c_last_name.</p>";
        } catch (Exception $e) {
            $db->rollback();
            $message = "<p style='color:red'>Error: Could not file claim.</p>"; // if everything fails
        }
        
        $db->close();
    }
?>

<!-- html stuff -->
<!DOCTYPE html>
<html>
<head><title>New Claim</title>
<link rel="stylesheet" href="add_claim.css">
</head>
<body>
  <h1>File a New Claim</h1>
  <?php echo $message; ?>

  <form action="add_claim.php" method="post">
    <h3>Customer Information</h3>
    First Name: <input type="text" name="c_first_name" required><br>
    Last Name:  <input type="text" name="c_last_name" required><br>
    
    <h3>Policy Information</h3> 
    Policy Type:     
    <select name="coverage_type" required>
        <option value="Auto Basic">Auto Basic</option>
        <option value="Auto Premium">Auto Premium</option>
        <option value="Home Standard">Home Standard</option>
        <option value="Home Premium">Home Premium</option>
        <option value="Renters Basic">Renters Basic</option>
    </select><br>

    <h3>Claim Details</h3>
    Incident Date: <input type="date" name="incident_date" required><br>
    Claim Amount:  <input type="number" step="0.01" name="claim_amount" required><br>
    Description:   <input type="text" name="cl_description" required><br>
    
    <input type="submit" value="Submit Claim">
  </form>
  <p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>