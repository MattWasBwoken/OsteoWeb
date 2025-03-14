<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once 'dbClass.php';
    use DB\DBConnection;
    if(!isset($_SESSION)) {
        session_start();
    }

    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true  || $_SESSION['userTipo'] !== '1') {
        header("Location: ./../NotAutherized.html");
        exit();
    }

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $db = new DBConnection();
        $connection = $db->getConnection();
        if($connection) {
            if($_GET['ope']==='accept') {
                $result = $db->acceptPrenotazione($id);
                if ($result) {
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
            else if($_GET['ope']==='reject') {
                $result = $db->rejectPrenotazione($id);
                if ($result) {
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
        }else {
            echo 'error';
        }
    } else {
        echo 'error';
    }