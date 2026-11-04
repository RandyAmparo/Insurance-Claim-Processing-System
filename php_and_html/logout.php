<?php

    // destroys the current session and redirects the user to the login page!

    session_start();
    session_destroy();
    header("Location: index.html");
    exit;
?>
