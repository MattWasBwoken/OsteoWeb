<?php
    if (!isset($_SESSION)) {
        session_start();
    }
    session_destroy();
    $_SESSION['logged_in'] = false;
    header("Location: accesso.php");
?>