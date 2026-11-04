<?php
    session_start();

    // makes sure the user is logged in, if not redirect them to the login page
    if (!isset($_SESSION['employee_id'])) {
        header("Location: index.html");
        exit;
    }
?>

<!-- html -->
<!DOCTYPE html>
<html>
<head><title>View Claims</title>
<link rel="stylesheet" href="view_claims.css">
</head>
<body>
  <h1>Claims History</h1>

  <?php

    // connecting to the beautifuyl database
    @$db = new mysqli('localhost', 'root', '', 'insurance_firm_344');

    if (mysqli_connect_errno()) {
       echo "<p>Error: Could not connect to database.<br/>
             Please try again later.</p>";
       exit;
    }

    // select the claim ids, a customers full name, the coverage type from polciies, the claim amount
    // the claim decision, date, and the employee name who reviewed it and joins the tables across the database
    // to give us a table with information relevant to what an employee might see. IT WILL ONLY SHOW
    // CLAIMS THAT HAVE BEEN APPROVED OR DENIED!!! 

    $query = "SELECT Claims.claim_id,
                     Customers.first_name AS c_first, Customers.last_name AS c_last,
                     Policies.coverage_type,
                     Claims.claim_amount, Claims.cl_description,
                     Claims.decision_status, Claims.decision_date,
                     Employees.first_name AS e_first, Employees.last_name AS e_last
              FROM Claims
              JOIN Policies  ON Claims.policy_id   = Policies.policy_id
              JOIN Customers ON Policies.customer_id = Customers.customer_id
              LEFT JOIN Employees ON Claims.reviewed_by = Employees.employee_id
              WHERE Claims.decision_status IN ('Approved', 'Denied')
              ORDER BY Claims.claim_id DESC";

    $result = $db->query($query);

    //this is basically saying if you find any rows with matching information display it back to me for viewing purposes
    // if not say theres no claims 

    if ($result && $result->num_rows > 0) { // if the result of rows is greater than zero
      echo "<table border='1' cellpadding='5'>";
      echo "<tr>
              <th>Claim ID</th><th>Customer</th><th>Coverage</th>
              <th>Amount</th><th>Description</th>
              <th>Reviewer</th><th>Status</th><th>Decision Date</th>
            </tr>";

      while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['claim_id'] . "</td>";
        echo "<td>" . $row['c_first'] . " " . $row['c_last'] . "</td>";
        echo "<td>" . $row['coverage_type'] . "</td>";
        echo "<td>$" . number_format($row['claim_amount'], 2) . "</td>";
        echo "<td>" . $row['cl_description'] . "</td>";
        echo "<td>" . ($row['e_first'] ? $row['e_first'] . " " . $row['e_last'] : "—") . "</td>";
        echo "<td>" . $row['decision_status'] . "</td>";
        echo "<td>" . $row['decision_date'] . "</td>";
        echo "</tr>";
      }
      echo "</table>";
    } else {
      echo "<p>No processed claims yet.</p>";
    }

    $db->close();

    // referenced used
    // joins: https://www.w3schools.com/sql/sql_join.asp
    // decimals line 65: https://www.w3schools.com/php/func_string_number_format.asp
    // display rows: https://www.w3schools.com/php/func_mysqli_fetch_assoc.asp & https://www.w3schools.com/php/php_mysql_select.asp

  ?>

  <p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
