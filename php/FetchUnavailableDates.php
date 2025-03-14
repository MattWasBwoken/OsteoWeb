<?php

require 'dbClass.php'; // Assicurati di includere il file della classe DB
use DB\DBConnection;

$db = new DBConnection();
$connection = $db->getConnection();

if ($connection) {
    $unavailableDates = $db->getUnavailableDates(); // Restituisce le date non disponibili in formato JSON 
    header('Content-Type: application/json');
    echo json_encode(['unavailableDates' => $unavailableDates]);
} else {
    
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Errore di connessione al database']);
}

$db->closeConnection($connection);

