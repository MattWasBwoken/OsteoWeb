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
    $m_ut_error="";
    $psw_ut_error="";
    if($connection){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit'])) 
            {
                $user = cleanTxt($_POST['username']);
                $psw = cleanTxt($_POST['password']);

                if (empty($user))
                    $m_ut_error = "Il campo email non deve essere vuoto.";

                if (empty($psw))
                    $psw_ut_error = "Il campo password non deve essere vuoto.";

                // Se tutto ok
                if ($m_ut_error=="" && $psw_ut_error=="")
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
                        

                        if ($info['Privilegio'] == 1)
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
        } 
    }else{
        header("Location: ../html/500.html");
        exit();
    }
    $closeResult = $db->closeConnection($connection);

    $paginaHTML = str_replace("{MailError}", $m_ut_error, $paginaHTML);
    $paginaHTML = str_replace("{PswError}", $psw_ut_error, $paginaHTML);

    echo $paginaHTML;

?>