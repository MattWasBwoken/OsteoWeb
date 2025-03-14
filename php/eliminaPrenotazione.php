<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once 'dbClass.php';
    use DB\DBConnection;
    if(!isset($_SESSION)) {
        session_start();
    }

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db = new DBConnection();
        $connection = $db->getConnection();

        if ($connection) {
            $result = $db->removePrenotazione($id);
            if ($result) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {
            header("Location: ./../html/500.html");
            exit();
        }
    } else {
        echo 'error';
    }
?>