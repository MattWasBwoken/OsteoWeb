<?php

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

require_once 'dbClass.php';
use DB\DBConnection;

if(!isset($_SESSION)) {
    session_start();
}

$db = new DBConnection();
$connection = $db->getConnection();

if ($connection) {
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['userID'])) {
        $result = $db->getInfoUtenteByCF($_SESSION['userID']);
        echo json_encode($result);
    }
}
$db->closeConnection($connection);