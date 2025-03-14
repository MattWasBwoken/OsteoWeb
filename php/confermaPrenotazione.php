<?php
    require_once 'dbClass.php';
    use DB\DBConnection;

    if(!isset($_SESSION)) {
        session_start();
    }

    function cleanTxt($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlentities($value);
        return $value;
    }

    ob_start();
    require_once "./../html/confermaPrenotazione.html";
    $html = ob_get_clean();

    $db = new DBConnection();
    $connection = $db->getConnection();
    
    $times = [ '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
    if($connection) {
        $toPrint = "<p><strong>Se non hai un account, iscriviti così da poter vedere lo stato della tua prenotazione</strong></p>";
        $toPrint .= "<div id=\"registrazione\"><form  action=\"./confermaPrenotazione.php\" method=\"post\">
                        <label for=\"username_r\" class=\"requiredField\">Username</label>
                        <input type=\"text\" id=\"username_r\" name=\"username_r\" required>
                        <p id=\"username_error_r\" class=\"error hidden\" aria-live=\"polite\">{UserErrorR}</p>

                        <label for=\"password_r\" class=\"requiredField\">Password</label>
                        <input type=\"password\" id=\"password_r\" name=\"password_r\" required>
                        <p id=\"password_error_r\" class=\"error hidden\" aria-live=\"polite\">{PswErrorR}</p>

                        <p>I campi indicati con<span class=\"requiredField\"></span> sono obbligatori.</p>
                        <input type=\"submit\" id=\"submit\" name=\"submit\" value=\"Registrati\">
        
        </form></div>";
        if(isset($_SESSION['userID']) && !$db->checkAccountByCF($_SESSION['userID'])) {
            $html = str_replace("{account}", $toPrint, $html);
        } else {
            $html = str_replace("{account}", "", $html);
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit'])) {
                $username = cleanTxt($_POST['username_r']);
                $password = cleanTxt($_POST['password_r']);
                if (!empty($username) && !empty($password)) {
                    $result = $db->registraAccount($username, $password, $_SESSION['userID']);
                    if ($result) {
                        $_SESSION['logged_in'] = true;
                        $_SESSION['userTipo'] = '0';
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $toPrint = str_replace("{UserErrorR}", "Username già in uso.", $toPrint);
                    }
                } else {
                    if (empty($username)) {
                        $toPrint = str_replace("{UserErrorR}", "Il campo username non deve essere vuoto.", $toPrint);
                    }
                    if (empty($password)) {
                        $toPrint = str_replace("{PswErrorR}", "Il campo password non deve essere vuoto.", $toPrint);
                    }
                }
            }
        }
    }

    echo $html;
