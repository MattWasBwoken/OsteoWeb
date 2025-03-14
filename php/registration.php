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
    require_once "./../html/accesso.html";
    $paginaHTML = ob_get_clean();

    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
        if($_SESSION['userTipo'] == 1) {
            header("Location: admin.php");
            exit();
        }
        else {
            header("Location: user.php");
            exit();
        }
    }
    
    $db = new DBConnection();
    $connection = $db->getConnection();
    $name_error="";
    $cognome_error="";
    $cf_error="";
    $mail_error="";
    $phone_error="";
    $user_error="";
    $psw_error="";
    if($connection){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit'])) 
            {
                $nome = cleanTxt($_POST['nome']);
                $cognome = cleanTxt($_POST['cognome']);
                $cf = cleanTxt($_POST['CF']);
                $mail = cleanTxt($_POST['mail']);
                $phone = cleanTxt($_POST['phone']);
                $user = cleanTxt($_POST['username']);
                $psw = cleanTxt($_POST['password']);

                if (empty($user))
                    $name_error = "Il campo email non deve essere vuoto.";
                if (empty($psw))
                    $cognome_error = "Il campo password non deve essere vuoto.";
                if (empty($cf))
                    $cf_error = "Il campo codice fiscale non deve essere vuoto.";
                if (empty($mail))    
                    $mail_error = "Il campo email non deve essere vuoto.";
                if (empty($phone))
                    $phone_error = "Il campo telefono non deve essere vuoto.";
                if (empty($user))
                    $user_error = "Il campo username non deve essere vuoto.";
                if (empty($psw))
                    $psw_error = "Il campo password non deve essere vuoto.";

                // Se tutto ok
                if ($name_error == "" && 
                $cognome_error == "" && 
                $cf_error == "" && 
                $mail_error == "" && 
                $phone_error == "" && 
                $user_error == "" && 
                $psw_error == "") {
                    
                    //try to register the user
                    $result = $db->registraUtente($cf, $nome, $cognome, $_POST['dataNascita'], $mail, $phone);

                    if ($result) {
                        if ($db->registraAccount($user, $psw, $cf)) {
                            $_SESSION['userID'] = $cf;
                            $_SESSION['logged_in'] = true;
                            $_SESSION['userNome'] = $nome;
                            $_SESSION['userCognome'] = $cognome;
                            $_SESSION['userTipo'] = 0;
                        } else {
                            $m_ut_error = "Username già esistente, ti preghiamo di sceglierne un altro.";
                        }
                    } else {
                        $m_ut_error = "Utente già esistente.";
                    }


                    if ($info['Privilegi'] == 1)
                        header("Location: admin.php");
                    else
                        header("Location: user.php");
                    exit;
                } else {
                        // Verifica se l'utente esiste e ha immesso una password sbagliata 
                        if($db->checkAccount($user)) 
                        {
                            $psw_ut_error = "La password immessa non è corretta.";
                        }
                        else {
                            $m_ut_error = "<span class=\"error_form\">Utente non registrato.</span>";
                        }
                        $wrong_pass_check = $db->checkAccount($user);
                }
            }
        }
    } else {
        header("Location: ../html/500.html");
        exit();
    }
    $closeResult = $db->closeConnection($connection);

    $paginaHTML = str_replace("{NomeError}", $name_error, $paginaHTML);
    $paginaHTML = str_replace("{CognomeError}", $cognome_error, $paginaHTML);
    $paginaHTML = str_replace("{CFError}", $cf_error, $paginaHTML);
    $paginaHTML = str_replace("{MailError}", $mail_error, $paginaHTML);
    $paginaHTML = str_replace("{PhoneError}", $phone_error, $paginaHTML);
    $paginaHTML = str_replace("{UserError}", $user_error, $paginaHTML);
    $paginaHTML = str_replace("{PswError}", $psw_error, $paginaHTML);
    
    echo $paginaHTML;

?>