<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '1');

    require_once './php/dbClass.php';
    use DB\DBConnection;

    function printArticle($id, $title, $text, $data) {
        $maxLength = 150; // Lunghezza massima del testo da mostrare
        $data_ita = new DateTime($data);
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . '...';
        }
        $output="<article class=\"boxNews\" id=\"".$id."\">";
        $output.="<h4>".$title."</h4>";
        $output.="<time datetime=\"".$data."\">".$data_ita->format('d/m/Y')."</time>";
        $output.="<p>".$text."</p>";
        $output.="<a href=\"./php/news.php#".$id."\">Vai alla notizia</a>";
        $output.="</article>";
        return $output;
    }
    
    if(!isset($_SESSION)) {
        session_start();
    }
    ob_start();
    require_once "./html/index.html";
    $html = ob_get_clean();

    $db = new DBConnection();
    $connection = $db->getConnection();

    if($connection) {
        $articles =$db->getLastNews();
        $toPrint = "";
        if($articles) {
            foreach ($articles as $article) {
                $toPrint .= printArticle($article['ID'], $article['Titolo'], $article['Testo'], $article['Data']);
            }
            $html = str_replace("<!--{news}-->", $toPrint, $html);
        }
    }

    echo $html;