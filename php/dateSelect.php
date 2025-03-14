<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once './dbClass.php';
    use DB\DBConnection;

    

    $db = new DBConnection();
    $connection = $db->getConnection();

    if(!isset($_SESSION)) {
        session_start();
    }

    if($connection) {
        if (isset($_POST['date']) && isset($_POST['sede'])) {
            $turns = $db->getAvailableTurns($_POST['date'], $_POST['sede']);
            header('Content-Type: application/json');
            echo json_encode(array_values($turns));
            exit;
        }
    } else {
        echo json_encode(['error' => 'Errore di connessione al database']);
    }

    $db->closeConnection($connection);

