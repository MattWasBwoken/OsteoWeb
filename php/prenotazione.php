<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once 'dbClass.php';
    use DB\DBConnection;

    function cleanTxt($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlentities($value);
        return $value;
    }

    ob_start();
    require_once "./../html/prenotazione.html";
    $page = ob_get_clean();

    if (!isset($_SESSION)) {
        session_start();
    }
    
    $db = new DBConnection();
    $connection = $db->getConnection();

    //errori
    $name_error = "";
    $surname_error = "";
    $cf_error = "";
    $mail_error = "";
    $phone_error = "";
    $sede_error = "";
    $data_error = "";
    $turno_error = "";
    $msg_error = "";
    
    if ($connection) {
        $loginTip = "";
        if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $loginTip="<p>Hai già un account? Effettua il <a href=\"./../php/accesso.php\" lang=\"en\" xml:lang=\"en\">login</a> oppure <a href=\"./../php/accesso.php\">registrati</a>  così da risparmiarti l'inserimento di qualche campo &#128512</p>";
        }
        $page = str_replace("{login}", $loginTip, $page);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['Prenota'])) {
                
                $sede = $_POST['sede'];
                $data = cleanTxt($_POST['data']);
                $turno = cleanTxt($_POST['turno']);
                $nome = cleanTxt($_POST['nome']);
                $cognome = cleanTxt($_POST['cognome']);
                $cf = cleanTxt($_POST['CF']);
                $mail = cleanTxt($_POST['mail']);
                $phone = $_POST['phone'];
                $msg = cleanTxt($_POST['note']);
                
                if(empty($sede) && ($sede !== "1" || $sede !== "2")) {
                    $sede_error = "Il campo sede non deve essere vuoto.";
                }
                if(empty($nome)) {
                    $name_error = "Il campo nome non deve essere vuoto.";
                }
                if(empty($cognome)) {
                    $surname_error = "Il campo cognome non deve essere vuoto.";
                }
                if(empty($cf)) {
                    $cf_error = "Il campo codice fiscale non deve essere vuoto.";
                }
                if(empty($mail)) {
                    $mail_error = "Il campo mail non deve essere vuoto.";
                }
                if(empty($phone)) {
                    $phone_error = "Il campo telefono non deve essere vuoto.";
                }
                if(empty($data)) {
                    $data_error = "Il campo data non deve essere vuoto.";
                }
                if(empty($turno)) {
                    $turno_error = "Il campo turno non deve essere vuoto.";
                }

                if($name_error==="" && $surname_error==="" && $cf_error==="" && $mail_error==="" && $phone_error==="" && $data_error==="" && $turno_error==="" && $msg_error==="" && $sede_error==="") {
                    $db->registraUtente($cf, $nome, $cognome, $_POST['Birth'], $mail, $phone);
                    //IMPOSTA SESSION SE NON GIA' IMPOSTATA
                    if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] !== true) {
                        $_SESSION['userID'] = $cf;
                        $_SESSION['userNome'] = $nome;
                        $_SESSION['userCognome'] = $cognome;
                    }
                    $result = $db->newPrenotazione($sede, $data, $turno, $cf, $msg);                    
                    if($result) {
                        $db->closeConnection($connection);
                        header("Location: ./../php/confermaPrenotazione.php");
                        exit();
                    } else {
                        $db->closeConnection($connection);
                        header("Location: ./../html/500.html");
                        exit();
                    }
                }

                
            }
        }

    } else {
        $db->closeConnection($connection);
        header("Location: ./../html/500.html");
        exit();
    }
    $db->closeConnection($connection);
    
    $page = str_replace("{sedeError}", $sede_error, $page);
    $page = str_replace("{dataError}", $data_error, $page);
    $page = str_replace("{turnoError}", $turno_error, $page);
    $page = str_replace("{nameError}", $name_error, $page);
    $page = str_replace("{cognomeError}", $surname_error, $page);
    $page = str_replace("{cfError}", $cf_error, $page);
    $page = str_replace("{mailError}", $mail_error, $page);
    $page = str_replace("{phoneError}", $phone_error, $page);
    $page = str_replace("{msgError}", $msg_error, $page);
    echo $page;


