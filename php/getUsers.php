<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once 'dbClass.php';
    use DB\DBConnection;
    $db = new DBConnection();
    $connection = $db->getConnection();

    if($connection) {
        $result = $db->getUsers();
        echo json_encode($result);
    }