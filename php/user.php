<?php
    require_once 'dbClass.php';
    use DB\DBConnection;

    if(!isset($_SESSION)) {
        session_start();
    }
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true  || $_SESSION['userTipo'] != 0) {
        header("Location: accesso.php");
        exit();
    }

    ob_start();
    require_once "./../html/dashboard.html";
    $html = ob_get_clean();
    $html = str_replace("{pathScript}","./../js/dashboard_user.js", $html);
    $db = new DBConnection();
    $connection = $db->getConnection();
    
    $times = [ '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    if($connection) {
        $table="<table id=\"prenotazioni\" aria-describedby=\"Elenco delle Prenotazioni\">";
        $table.="<caption id=\"table-description\">Elenco delle Prenotazioni</caption>";
        $table.="<thead><tr><th scope=\"col\">ID</th><th scope=\"col\">Data</th><th scope=\"col\">Ora</th><th scope=\"col\">Messaggio</th><th scope=\"col\">Stato</th><th scope=\"col\">Operazione</th></tr></thead>";
        $table.="<tbody>";

        $prenotazioni = $db->getPrenotazioniUtente($_SESSION['userID']);
        if(isset($prenotazioni) && $prenotazioni != null) {
            foreach ($prenotazioni as $prenotazione) {
                $table.="<tr>";
                $table.="<td>".$prenotazione['ID']."</td>";
                $table.="<td>".$prenotazione['Giorno']."</td>";
                $table.="<td>".$times[$prenotazione['Turno']-1]."</td>";
                $table.="<td>".$prenotazione['Messaggio']."</td>";
                $table.="<td>";
                if($prenotazione['Stato']==0) {
                    $table.="non accettato";
                } else {
                    $table.="accettato";
                }
                $table.="</td>";
                $table.="<td><button class=\"delete-btn\" data-id=\"".$prenotazione['ID']."\">Elimina</button></td>";
                $table.="</tr>";
            }
            $table.="</table>";
        }else{
            $table.="<tr><td colspan=\"8\">Nessuna prenotazione presente</td></tr>";
        }
        $table.="</tbody>";
        $table.="</table>";
    }
    else {
        $db->closeConnection($connection);
        header("Location: ./../html/500.html");
        exit();
    }
    $db->closeConnection($connection);

    $html = str_replace("{username}", $_SESSION['userNome']." ".$_SESSION['userCognome'], $html);
    $html = str_replace("{insert_prenotations}", $table, $html);
    echo $html;
?>