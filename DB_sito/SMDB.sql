DROP TABLE IF EXISTS Prenotazione;
DROP TABLE IF EXISTS Account;
DROP TABLE IF EXISTS Utente;
DROP TABLE IF EXISTS News;

CREATE TABLE Utente(
    CF varchar(16) NOT NULL PRIMARY KEY,
    Nome varchar(30) NOT NULL,
    Cognome varchar(30) NOT NULL,
    DataNascita date,
    Mail varchar(255),
    Telefono varchar(10)
);

CREATE TABLE Account(
    Username varchar(30) PRIMARY KEY,
    Password varchar(255),
    Utente varchar(16),
    Privilegio BIT DEFAULT 0,
    FOREIGN KEY (Utente) REFERENCES Utente(CF) 
);

CREATE TABLE Prenotazione(
    ID SERIAL PRIMARY KEY,
    Sede int NOT NULL,
    Giorno date NOT NULL,
    Turno int NOT NULL,
    Paziente varchar(16) NOT NULL,
    Messaggio TEXT NOT NULL,
    Stato BIT,
    FOREIGN KEY (Paziente) REFERENCES Utente(CF) 
);

CREATE TABLE News(
    ID SERIAL PRIMARY KEY,
    Titolo varchar(255) NOT NULL,
    Testo TEXT NOT NULL,
    Data date NOT NULL
);

INSERT INTO Utente VALUES ('RSSMRA00A01H501A', 'Mario', 'Rossi', '2000-01-01', "rossimario@gmail.com", "0123456789"),
                          ('VRDGPP00A01H501A', 'Giuseppe', 'Verdi', '2000-01-01', "verdigiuseppe@gmail.com", "0123456789");

INSERT INTO Account VALUES ('user', 'user', 'RSSMRA00A01H501A', 0),
                           ('admin', 'admin', 'VRDGPP00A01H501A', 1);

INSERT INTO News VALUES ('1', 'Come ridurre lo stress', 'Lo stress accumulato durante la giornata può avere effetti negativi sul corpo, come tensione muscolare, dolori articolari e ansia. Un massaggio rilassante è la soluzione ideale per ridurre la tensione e ristabilire l’equilibrio mentale e fisico.', '2024-12-01'),
                        ('2', '10 Esercizi da Fare Durante le Vacanze', 'Non lasciare che le vacanze interrompano la tua routine di allenamento! Ti proponiamo 10 esercizi facili e veloci che puoi fare comodamente a casa, anche durante i giorni di festa. Che tu sia un principiante o un atleta esperto, questi esercizi ti aiuteranno a mantenere il corpo attivo, migliorare la tua forma fisica e combattere lo stress natalizio. Non dimenticare di dedicare un po’ di tempo ogni giorno al tuo benessere!', '2024-12-15'),
                        ('3', 'Benefici dell’Osteopatia per le Donne in Gravidanza', 'L’osteopatia è un trattamento sicuro e delicato che può aiutare a gestire molte delle problematiche comuni durante la gravidanza, come il mal di schiena, la sciatica e il gonfiore alle gambe. La nostra equipe di esperti osteopati è pronta ad offrirti soluzioni personalizzate per alleviare i fastidi legati alla gravidanza e migliorare il tuo benessere generale. Scopri come un trattamento osteopatico può supportarti durante questo periodo speciale.', '2024-12-20'),
                        ('4', 'Ferie natalizie', 'Le cliniche saranno chiuse per ferie durante il periodo natalizio dal 24 dicembre al 6 gennaio. Questo periodo di pausa ci permette di ricaricare le energie e di prepararci al meglio per offrirvi un servizio ancora più qualificato. Non preoccupatevi! I nostri servizi riprenderanno regolarmente dopo le festività. Vi auguriamo un sereno Natale e un felice anno nuovo!', '2024-12-21');
