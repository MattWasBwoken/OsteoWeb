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

    function printArticle($id, $title, $img, $text) {
        $output="<article class=\"news\" id=\"".$id."\">";
        $output.="<h3>".$title."</h3>";
        $output.="<p>".$text."</p>";
        $output.="<button class=\"edit-btn\" data-id=\""+$id+"\">Modifica</button>";
        $output.="<button class=\"delete-btn\" data-id=\""+$id+"\">Elimina</button>";
        $output.="</article>";
        return $output;
    }
    
    ob_start();
    require_once "./../html/news.html";

    $paginaHTML = ob_get_clean();

    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || $_SESSION['userTipo'] !== '1') {
        header("Location: ./../html/notAuthorized.html");
        exit();
    }

    $db = new DBConnection();
    $connection = $db->getConnection();

    $nameError = "";
    $textError = "";

    if($connection){
        $toPrint = "";
        //gestione post
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $title = cleanTxt($_POST['title']);
            $text = cleanTxt($_POST['text']);
            $data = date('Y-m-d H:i:s');
            if (!empty($title) && !empty($text)) {
                $result = false;    
                if (isset($_POST['create'])) {
                    $result = $db->addNews($title, $text, $data);
                } else if (isset($_POST['edit'])) {
                    $result = $db->editNews($_GET['id'], $title, $text, $data);
                }
                if ($result) {
                    header("Location: ./../html/confermaInserimentoNews.html");
                    exit();
                } else {
                    header("Location: ./../html/500.html");
                    exit();
                }
            } else {
                if (empty($title)) {
                    $toPrint = str_replace("{NomeError}", "Il campo titolo non deve essere vuoto.", $toPrint);
                }
                if (empty($text)) {
                    $toPrint = str_replace("{TextError}", "Il campo testo non deve essere vuoto.", $toPrint);
                }
            }
        }

        if ($_GET['ope'] === 'create') {
            $toPrint.="<form id=\"create_form\" class=\"newsForm\" method=\"post\">";
            $toPrint.="<fieldset>";
            $toPrint.="<label for=\"title\">Titolo:</label>";
            $toPrint.="<input type=\"text\" id=\"title\" name=\"title\" required=\"required\">";
            $toPrint.="<p class=\"error\" id=\"nome_error\" aria-live=\"polite\">{NomeError}</p>";    
            $toPrint.="<label for=\"text\">Testo:</label>";
            $toPrint.="<textarea name=\"text\" id=\"text\" name=\"text\" required=\"required\"></textarea>";
            $toPrint.="<p class=\"error\" id=\"text_error\" aria-live=\"polite\">{TextError}</p>";
            $toPrint.="<input type=\"submit\" id=\"create\" name=\"create\" value=\"Crea\"></input>";
            $toPrint.="</fieldset>";
            $toPrint.="</form>";
        } else if ($_GET['ope'] === 'edit' && isset($_GET['id'])) {
            $news = $db->getNewsById($_GET['id']);
            if ($news) {
                $toPrint.="<form id=\"edit_form\" class=\"newsForm\" method=\"post\">";
                $toPrint.="<fieldset>";
                $toPrint.="<label for=\"title\">Titolo:</label>";
                $toPrint.="<input type=\"text\" name=\"title\" id=\"title\" required=\"required\" value=\"".$news['Titolo']."\">";
                $toPrint.="<p class=\"error\" id=\"nome_error\" required=\"required\"aria-live=\"polite\">{NomeError}</p>";
                $toPrint.="<label for=\"text\">Testo:</label>";
                $toPrint.="<textarea name=\"text\" id=\"text\" required=\"required\">".$news['Testo']."</textarea>";
                $toPrint.="<p class=\"error\" id=\"text_error\" aria-live=\"polite\">{TextError}</p>";
                $toPrint.="<input type=\"submit\" id=\"edit\" name=\"edit\" value=\"Modifica\"></input>";
                $toPrint.="</fieldset>";
                $toPrint.="</form>";

            } else {
                header("Location: ./../html/500.html");
                exit();
            }
            
        } else if ($_GET['ope'] === 'delete' && isset($_GET['id'])) {
            $result = $db->deleteNews($_GET['id']);
            if ($result) {
                echo 'success';
                exit();
            } else {
                echo 'error';
                exit();
            }
        }
    } else {
        header("Location: ./../html/500.html");
        exit();
    }
    $close = $db->closeConnection($connection);


    $paginaHTML = str_replace("{articles}", $toPrint, $paginaHTML);
    $paginaHTML = str_replace("{NomeError}", $nameError, $paginaHTML);
    $paginaHTML = str_replace("{TextError}", $textError, $paginaHTML);
    echo $paginaHTML;