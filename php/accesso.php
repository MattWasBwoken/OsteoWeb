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
    $html = ob_get_clean();

    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
        header("Location: dashboard.php");
        exit();
    }
    $db = new DBConnection();
    $connection = $db->getConnection();
    //messaggio errori login
    $login_error="";
    $user_errorL="";
    $psw_errorL="";
    //messaggi errore registrazione
    $name_error="";
    $cognome_error="";
    $cf_error="";
    $mail_error="";
    $phone_error="";
    $user_errorR="";
    $psw_errorR="";

    if($connection){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit_l'])) {
                $user = cleanTxt($_POST['username_l']);
                $psw = cleanTxt($_POST['password_l']);

                if (empty($user))
                    $user_errorL = "Il campo email non deve essere vuoto.";

                if (empty($psw))
                    $psw_errorL = "Il campo password non deve essere vuoto.";

                // Se tutto ok
                if ($user_errorL=="" && $psw_errorL=="")
                {
                    $result = $db->checkCredenzialiLogin($user, $psw); // da aggiungere hash
                    
                    if ($result) {
                        $info = $db->getInfoUtente($user);
                        // Salvataggio informazioni dell'utente nella sessione
                        $_SESSION['userID'] = $info['CF'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['userNome'] = $info['Nome'];
                        $_SESSION['userCognome'] = $info['Cognome'];
                        $_SESSION['userTipo'] = $info['Privilegio'];
                        
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // Verifica se l'utente esiste e ha immesso una password sbagliata 
                        if($db->checkAccount($user)) 
                        {
                            $login_error = "La password immessa non è corretta.";
                        }
                        else {
                            $login_error = "Utente non registrato.";
                        }
                        $wrong_pass_check = $db->checkAccount($user);
                    }
                }
            } elseif (isset($_POST['submit_r'])) {
                $nome = cleanTxt($_POST['nome']);
                $cognome = cleanTxt($_POST['cognome']);
                $cf = cleanTxt($_POST['CF']);
                $data = $_POST['dataNascita'];
                $mail = cleanTxt($_POST['mail']);
                $phone = $_POST['phone'];
                $user = cleanTxt($_POST['username_r']);
                $psw = cleanTxt($_POST['password_r']);
                
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
                    $user_errorR = "Il campo username non deve essere vuoto.";
                if (empty($psw))
                    $psw_errorR = "Il campo password non deve essere vuoto.";

                // Se tutto ok
                if ($name_error == "" && 
                    $cognome_error == "" && 
                    $cf_error == "" && 
                    $mail_error == "" && 
                    $phone_error == "" && 
                    $user_errorR == "" && 
                    $psw_errorR == "") {
                    
                    //try to register the user
					$result = $db->registraUtente($cf, $nome, $cognome, $data, $mail, $phone);
                    if ($db->registraAccount($user, $psw, $cf)) {
                        $_SESSION['userID'] = $cf;
                        $_SESSION['logged_in'] = true;
                        $_SESSION['userNome'] = $nome;
                        $_SESSION['userCognome'] = $cognome;
                        $_SESSION['userTipo'] = 0;
                    } else {
                        $login_error = "Username già esistente, ti preghiamo di sceglierne un altro.";
                    }


                    header("Location: dashboard.php");
                } else {
                        // Verifica se l'utente esiste e ha immesso una password sbagliata 
                        if($db->checkAccount($user)) 
                        {
                            $login_error = "La password immessa non è corretta.";
                        }
                        else {
                            $login_error = "<span class=\"error_form\">Utente non registrato.</span>";
                        }
                        $wrong_pass_check = $db->checkAccount($user);
                }
            }
        } 
    }else{
        header("Location: ../html/500.html");
        exit();
    }
    $closeResult = $db->closeConnection($connection);

    $html = str_replace("{LoginError}", $login_error, $html);
    $html = str_replace("{UserErrorL}", $user_errorL, $html);
    $html = str_replace("{PswErrorL}", $psw_errorL, $html);

    $html = str_replace("{NomeError}", $name_error, $html);
    $html = str_replace("{CognomeError}", $cognome_error, $html);
    $html = str_replace("{CFError}", $cf_error, $html);
    $html = str_replace("{MailError}", $mail_error, $html);
    $html = str_replace("{PhoneError}", $phone_error, $html);
    $html = str_replace("{UserErrorR}", $user_errorR, $html);
    $html = str_replace("{PswErrorR}", $psw_errorR, $html);

    echo $html;

