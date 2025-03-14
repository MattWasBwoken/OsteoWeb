<?php
    require_once 'dbClass.php';
    use DB\DBConnection;

    if(!isset($_SESSION)) {
        session_start();
    }
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: accesso.php");
        exit();
    }

    ob_start();
    require_once "./../html/dashboard.html";
    $html = ob_get_clean();
    if($_SESSION['userTipo'] === '0') {
        $html = str_replace("{pathScript}","./../js/dashboard_user.js", $html);
    }
    else if($_SESSION['userTipo'] === '1') {
        $html = str_replace("{pathScript}","./../js/dashboard_admin.js", $html);
    }
    $db = new DBConnection();
    $connection = $db->getConnection();
    
    $times = [ '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    if($connection) {
        $table="<table id=\"prenotazioni\" aria-describedby=\"table_description\">";
        $table.="<caption id=\"table-description\">Elenco delle Prenotazioni</caption>";

        if( $_SESSION['userTipo'] === '1') {
            $table.="<thead><tr><th scope=\"col\">ID</th><th scope=\"col\">Data</th><th scope=\"col\">Sede</th><th scope=\"col\">Ora</th><th scope=\"col\">Paziente</th><th scope=\"col\">Email</th><th scope=\"col\">Telefono</th><th scope=\"col\">Note</th><th scope=\"col\">Operazioni</th></tr></thead>";
        } else if ($_SESSION['userTipo'] === '0') {
            $table.="<thead><tr><th scope=\"col\">ID</th><th scope=\"col\">Data</th><th scope=\"col\">Sede</th><th scope=\"col\">Ora</th><th scope=\"col\">Messaggio</th><th scope=\"col\">Stato</th><th scope=\"col\">Operazione</th></tr></thead>";
        }
        
        $table.="<tbody>";
        $prenotazioni = null;
        if ($_SESSION['userTipo'] === '1') {
            $prenotazioni = $db->getPrenotazioni();
        } elseif ($_SESSION['userTipo'] === '0') {
            $prenotazioni = $db->getPrenotazioniUtente($_SESSION['userID']);
        }

        if(isset($prenotazioni) && $prenotazioni != null) {
            foreach ($prenotazioni as $prenotazione) {
                if ($_SESSION['userTipo'] === '1') {
                    $table.="<tr>";
                    $table.="<th scope=\"row\">".$prenotazione['ID']."</th>";
                    $table.="<td data-title=\"Data\">".$prenotazione['Giorno']."</td>";
                    if ($prenotazione['Sede'] === '1') {
                        $table.= "<td data-title=\"Sede\">Abano Terme</td>";
                    } else if ($prenotazione['Sede'] === '2') {
                        $table.= "<td data-title=\"Sede\">Padova</td>";
                    }
                    $table.="<td data-title=\"Ora\">".$times[$prenotazione['Turno']-1]."</td>"; // CHEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEECK
                    $table.="<td data-title=\"Paziente\">".$prenotazione['Nome']." ".$prenotazione['Cognome']."</td>";
                    if(isset($prenotazione['Mail']) && $prenotazione['Mail'] !== "") {
                        $table.="<td data-title=\"Email\">".$prenotazione['Mail']."</td>";
                    }
                    else {
                        $table.="<td data-title=\"Email\"></td>";
                    }
                    if(isset($prenotazione['Telefono']) && $prenotazione['Telefono'] !== "") {
                        $table.="<td data-title=\"Telefono\">".$prenotazione['Telefono']."</td>";
                    }
                    else {
                        $table.="<td data-title=\"Telefono\"></td>";
                    }
                    $table.="<td data-title=\"Note\">".$prenotazione['Messaggio']."</td>";
                    if(!isset($prenotazione['Stato'])) {
                        $table.="<td data-title=\"Operazioni\"><button class='accept-btn' data-id='".$prenotazione['ID']."'>accetta</button><button class='reject-btn' data-id='".$prenotazione['ID']."'>rifiuta</button></td>";
                    }
                    else if ($prenotazione['Stato'] == 1) {
                        $table.="<td data-title=\"Operazioni\">Prenotazione già accettata</td>";
                    }
                    else if ($prenotazione['Stato'] == 0) {
                        $table.="<td data-title=\"Operazioni\">Prenotazione rifiutata</td>";
                    }
                    $table.="</tr>";
                } else if ($_SESSION['userTipo'] === '0') {
                    $table.="<tr >";
                    $table.="<th scope=\"row\">".$prenotazione['ID']."</th>";
                    $table.="<td data-title=\"Data\">".$prenotazione['Giorno']."</td>";
                    if ($prenotazione['Sede'] === '1') {
                        $table.= "<td data-title=\"Sede\">Abano Terme</td>";
                    } else if ($prenotazione['Sede'] === '2') {
                        $table.= "<td data-title=\"Sede\">Padova</td>";
                    }
                    $table.="<td data-title=\"Ora\">".$times[$prenotazione['Turno']-1]."</td>";
                    $table.="<td data-title=\"Messaggio\">".$prenotazione['Messaggio']."</td>";
                    $table.="<td data-title=\"Stato\">";
                    if($prenotazione['Stato']=='0') {
                        $table.="non accettato";
                    } else if ($prenotazione['Stato']=='1') {
                        $table.="accettato";
                    } else {
                        $table.="in attesa";
                    }
                    $table.="</td>";
                    $table.="<td data-title=\"Operazione\"><button class=\"delete-btn\" data-id=\"".$prenotazione['ID']."\">Elimina</button></td>";
                    $table.="</tr>";
                }
            }
            $table.="</table>";
        }else{
            $table.="<tr><td colspan=\"9\" scope=\"rowgroup\">Nessuna prenotazione presente</td></tr>";
        }
        $table.="</tbody>";
        $table.="</table>";
        if ($_SESSION['userTipo'] === '1') {
            $table.="<p id=\"table_description\" class=\"hidden\">>In questa tabella sono rappresentate le prenotazioni effettuate dai pazienti</p>";
        } else if ($_SESSION['userTipo'] === '0') {
            $table.="<p id=\"table_description\" class=\"hidden\">>In questa tabella sono rappresentate le prenotazioni effettuate da te</p>";
        }
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