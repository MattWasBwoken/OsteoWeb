<?php
    namespace DB;
    use mysqli;
    use Exception;

    class DBConnection {
        private const HOST = 'localhost';
        private const USER = 'root';
        private const PSW = '';
        private const DBNAME = 'SMBD';
        
        private $con;

        public function getConnection() {
            try {
                $this->con = new mysqli(self::HOST, self::USER, self::PSW, self::DBNAME);
                if (!$this->con) {
                    throw new Exception("Errore di connessione al database: " . mysqli_connect_error($this->con));
                }
                mysqli_set_charset($this->con, 'utf8mb4');
                return $this->con;
            } catch (Exception $e) {
                return false;
            }
        }

        public function closeConnection($conn) {
            try{
                if ($this->con) {
                mysqli_close($this->con);
                $this->con = null;
                return true;
                } else {
                    throw new Exception("Errore di chiusura della connessione al database");
                }
            } catch (Exception $e) {
                return false;
            }
        }

        //query login
        public function checkCredenzialiLogin($username, $password)
        {
            $queryCheck = "SELECT * FROM Account WHERE Username=\"$username\" AND password=\"$password\"";
            $queryResult = mysqli_query($this->con, $queryCheck) or die("Errore nell'accesso al DB" . mysqli_error($this->connection));

            return mysqli_num_rows($queryResult) > 0;
        }
        // query utente
        public function checkAccount($username) {
            $query = "SELECT * FROM Account WHERE Username = \"$username\"";
            $result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_num_rows($result)>0;
        }
        public function checkAccountByCF($cf) {
            $query = "SELECT * FROM Account WHERE Utente = \"$cf\"";
            $result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_num_rows($result)>0;
        }

        public function checkUser($cf) {
            $query = "SELECT * FROM Utente WHERE CF = \"$cf\"";
            $result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_num_rows($result)>0;
        }

        public function getUsers() {
            $query = "SELECT Username FROM Account";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row['Username'];
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            return null;
        }

        public function registraUtente($cf, $nome, $cognome, $dNascita, $mail, $phone) {
            $dataNascita = null;
            $mailToInsert = null;
            $phoneToInsert = null;
            if(!empty($dNascita)){
                $dataNascita = $dNascita;
            }
            if(isset($mail) && $mail !== ""){
                $mailToInsert = $mail;
            }
            if(isset($phone) && $phone !== ""){
                $phoneToInsert = $phone;
            }
            if($this->checkUser($cf)){
                return false;
            }
            $query = "INSERT INTO Utente(CF, Nome, Cognome, DataNascita, Mail, Telefono) VALUES (\"$cf\", \"$nome\", \"$cognome\", \"$dataNascita\", \"$mailToInsert\", \"$phoneToInsert\")";

            $result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
            
            return mysqli_affected_rows($this->con)>0;
        }

        public function registraAccount($username, $password, $cf) {
            if($this->checkAccount($username)){
                return false;
            }
            $query = "INSERT INTO Account(Username, Password, Utente) VALUES (\"$username\", \"$password\", \"$cf\")";

            $result = mysqli_query($this->con, $query);
            
            return mysqli_affected_rows($this->con)>0;
        }

        public function getInfoUtente($username){
            $query = "SELECT * FROM Utente JOIN Account ON CF=Account.Utente WHERE Username = \"$username\"";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = mysqli_fetch_assoc($queryResult);
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        public function getInfoUtenteByCF($cf){
            $query = "SELECT * FROM Utente WHERE CF = \"$cf\"";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = mysqli_fetch_assoc($queryResult);
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        // query prenotazione
        public function newPrenotazione($sede, $giorno, $orario, $paziente, $note) {
            $query = "INSERT INTO Prenotazione(Sede, Giorno, Turno, Paziente, Messaggio) VALUES (\"$sede\", \"$giorno\", \"$orario\", \"$paziente\",\"$note\")";

            mysqli_query($this->con, $query) or die (mysqli_error($this->con));
            return mysqli_affected_rows($this->con) > 0;
        }

        public function removePrenotazione($id) {
            $query = "DELETE FROM Prenotazione WHERE ID=\"$id\"";
            mysqli_query($this->con, $query) or die(mysqli_error($this->con));

			return mysqli_affected_rows($this->con) > 0;
		}

        public function acceptPrenotazione($id) {
            $query = "UPDATE Prenotazione SET Stato = 1 WHERE ID=\"$id\"";
            mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_affected_rows($this->con) > 0;
        }

        public function rejectPrenotazione($id) {
            $query = "UPDATE Prenotazione SET Stato = 0 WHERE ID=\"$id\"";
            mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_affected_rows($this->con) > 0;
        }


        public function getPrenotazioni() { //query per admin
            $query = "SELECT * FROM Prenotazione JOIN Utente WHERE Paziente = CF";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row;
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            return null;
        }

        public function getPrenotazioniUtente($cf) { //query per utente
            $query = "SELECT * FROM Prenotazione AS P JOIN Utente AS U ON P.Paziente = U.CF WHERE Paziente = \"$cf\"";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row;
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        public function getPrenotazioniForDayAndPlace($day, $sede) { //query per backend
            $query = "SELECT * FROM Prenotazione WHERE Giorno = \"$day\" AND Sede = \"$sede\"";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row;
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        public function getUnavailableDates() {
            $query = "SELECT Giorno FROM Prenotazione GROUP BY Giorno HAVING COUNT(*) >= 9";
            $result = mysqli_query($this->con, $query) or die(mysqli_error($this->con));
            $unavailableDates = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $unavailableDates[] = $row['Giorno'];
                }
            }
            return $unavailableDates;
        }

        public function getAvailableTurns($day, $sede) {
            $prenotazioni = $this->getPrenotazioniForDayAndPlace($day, $sede);
            $times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
            if ($prenotazioni) {
                foreach ($prenotazioni as $prenotazione) {
                    unset($times[$prenotazione['Turno']-1]);
                }
            }
            return $times;
        }

        //query per news
        public function getNews() {
            $query = "SELECT * FROM News ORDER BY ID DESC";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row;
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        public function getNewsById($id) {
            $query = "SELECT * FROM News WHERE ID = \"$id\"";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = mysqli_fetch_assoc($queryResult);
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }
        }

        public function getLastNews() {
            $query = "SELECT * FROM News ORDER BY ID DESC LIMIT 3";
            $queryResult = mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            if(mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)) 
                {
                    $result[] = $row;
                }
                mysqli_free_result($queryResult);
                return $result;
            } 
            else {
                return null;
            }

        }

        public function addNews($title, $text, $data) {
            $query = "INSERT INTO News(Titolo, Testo, Data) VALUES (\"$title\", \"$text\", \"$data\")";

            mysqli_query($this->con, $query) or die (mysqli_error($this->con));
            return mysqli_affected_rows($this->con) > 0;
        }

        public function deleteNews($id) {
            $query = "DELETE FROM News WHERE ID=\"$id\"";
            mysqli_query($this->con, $query) or die(mysqli_error($this->con));

            return mysqli_affected_rows($this->con) > 0;
        }

        public function editNews($id, $title, $text, $data) {
            if (isset($title) && $title!=='' && isset($text) && $text!=='') {
                $query = "UPDATE News SET Titolo=\"$title\", Testo=\"$text\", Data=\"$data\" WHERE ID=\"$id\"";
            } else if (isset($title) && $title!=='' && !isset($text)) {
                $query = "UPDATE News SET Titolo=\"$title\", Data=\"$data\" WHERE ID=\"$id\"";
            } else if (!isset($title) && isset($text) && $text!=='') {
                $query = "UPDATE News SET Testo=\"$text\", Data=\"$data\" WHERE ID=\"$id\"";
            } else {
                return false;
            }
            mysqli_query($this->con, $query) or die(mysqli_error($this->con));
            return mysqli_affected_rows($this->con) > 0;
        }

    }
?>
