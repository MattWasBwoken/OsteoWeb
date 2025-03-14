<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once 'dbClass.php';
    use DB\DBConnection;

    function printArticle($id, $title, $text, $data) {
        $data_ita = new DateTime($data);
        $output="<article class=\"notizia\" id=\"".$id."\">";
        $output.="<h3><time datetime=\"".$data."\">".$data_ita->format('d/m/Y')."</time> | ". $title."</h3>";
        $output.="<p>".$text."</p>";
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && isset($_SESSION['userTipo']) && $_SESSION['userTipo'] == '1') {
            $output.="<button class=\"edit-btn\" data-id=\"".$id."\">Modifica</button>";
            $output.="<button class=\"delete-btn\" data-id=\"".$id."\">Elimina</button>";
        }
        $output.="</article>";
        return $output;
    }
    
    ob_start();
    require_once "./../html/news.html";

    $paginaHTML = ob_get_clean();

    if (!isset($_SESSION)) {
        session_start();
    }

    $db = new DBConnection();
    $connection = $db->getConnection();
    if($connection){
        $articles = $db->getNews();
        $toPrint = "<section>";
        if ($articles) {
            foreach ($articles as $article) {
                $toPrint .= printArticle($article['ID'], $article['Titolo'], $article['Testo'], $article['Data']);
            }
            
        } else {
            $toPrint = "<p>Non ci sono articoli</p>";
        }

        if (isset($_SESSION['userTipo']) && $_SESSION['userTipo'] == '1') {
            $toPrint.="<button class=\"add-btn\">Aggiungi</button>";
        }
        $toPrint.="</section>";
    }
    else {
        header("Location: ./../html/500.html");
        exit();
    }
    $close = $db->closeConnection($connection);
    $paginaHTML = str_replace("{articles}", $toPrint, $paginaHTML);


    echo $paginaHTML;
    
