<?php
    require_once 'dbClass.php';
    use DB\DBConnection;

    if(!isset($_SESSION)) {
        session_start();
    }
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true  || $_SESSION['userTipo'] !== '1') {
        header("Location: accesso.php");
        exit();
    }

    ob_start();
    require_once "./../html/dashboard.html";
    $html = ob_get_clean();
    $html = str_replace("{pathScript}","./../js/dashboard_admin.js", $html);
    $db = new DBConnection();
    $connection = $db->getConnection();
    
    if($connection) {
        $table="<table id=\"prenotazioni\" aria-describedby=\"Elenco delle Prenotazioni\">";
        $table.="<caption id=\"table-description\">Elenco delle Prenotazioni</caption>";
        $table.="<thead><tr><th scope=\"col\">ID</th><th scope=\"col\">Data</th><th scope=\"col\">Ora</th><th scope=\"col\">Paziente</th><th scope=\"col\">Email</th><th scope=\"col\">Nome</th><th scope=\"col\">Note</th><th scope=\"col\">Operazioni</th></tr></thead>";
        $table.="<tbody>";

        $prenotazioni = $db->getPrenotazioni();
        if(isset($prenotazioni) && $prenotazioni != null) {
            foreach ($prenotazioni as $prenotazione) {
                $table.="<tr>";
                $table.="<td>".$prenotazione['ID']."</td>";
                $table.="<td>".$prenotazione['Giorno']."</td>";
                $table.="<td>".$prenotazione['Turno']."</td>";
                $table.="<td>".$prenotazione['Nome']." ".$prenotazione['Cognome']."</td>";
                if(isset($prenotazione['Mail']) && $prenotazione['Mail'] !== "") {
                    $table.="<td>".$prenotazione['Mail']."</td>";
                }
                else {
                    $table.="<td></td>";
                }
                if(isset($prenotazione['Telefono']) && $prenotazione['Telefono'] !== "") {
                    $table.="<td>".$prenotazione['Telefono']."</td>";
                }
                else {
                    $table.="<td></td>";
                }
                $table.="<td>".$prenotazione['Messaggio']."</td>";
                if(!isset($prenotazione['Stato'])) {
                    $table.="<td><button class='accept-btn' data-id='".$prenotazione['ID']."'>accetta</button><button class='reject-btn' data-id='".$prenotazione['ID']."'>rifiuta</button></td>";
                }
                else if ($prenotazione['Stato'] == 1) {
                    $table.="<td>Prenotazione già accettata</td>";
                }
                else if ($prenotazione['Stato'] == 0) {
                    $table.="<td>Prenotazione rifiutata</td>";
                }
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