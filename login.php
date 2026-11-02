<?php

    session_start(); // allows the user to navigate through different pages while website remembers who they are

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // create short variable names to store
        $email = $_POST['email'];
        $e_password = $_POST['e_password'];

        // connecting php and SQL

        @$db = new mysqli('localhost', 'root', '', 'insurance_firm_344');

        if (mysqli_connect_errno()) {
           echo "<p>Error: Could not connect to database.<br/>
                 Please try again later.</p>";
           exit;
        }

        // looks through the employee table and grabs the id and first name basde on the input from the form

        $query = "SELECT employee_id, first_name FROM Employees WHERE email = ? AND e_password = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $email, $e_password);
        $stmt->execute();
        $stmt->bind_result($employee_id, $first_name);

        // based on the result of bind_result, stmt->fetch will go fetch the data
        // if the email and password work, it will save the employee id and first name throughout the entire session
        // after login you are redirected to the menu

        if ($stmt->fetch()) {
            $_SESSION['employee_id'] = $employee_id;
            $_SESSION['first_name'] = $first_name;
            $stmt->close();
            $db->close();
            header("Location: menu.php");
            exit;
        } else {
            $error = "Invalid email or password."; // if no record is found say this
            $stmt->close();
        }
        $db->close();
    }

    // References Used
    
    // starting sessions: https://www.w3schools.com/php/php_sessions.asp
    // database connection error: https://www.w3schools.com/php/func_mysqli_connect_errno.asp
    // prevent sql injection: https://www.w3schools.com/php/php_mysql_prepared_statements.asp 
    // redirects to the menu: https://www.w3schools.com/php/func_network_header.asp

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee login</title>
    <link rel="stylesheet" href="favoriteOne.css">
</head>
<body>
    <h1>Insurance Management System</h1>
    <h2>Employee login</h2>
    <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
    <form action="login.php" method="POST">
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
          
        <div>
            <label>Password:</label>
            <input type="password" name="e_password" required>
        </div>
        
            <button type="submit">Log in</button>
    </form>

</body>
</html>