<?php 

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookfinder');
$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;

//Oppretter PDO for å koble til databasen, 
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Gjør at scriptet stoppes og gir en exception om en feil dukker opp
} catch (PDOException $e) {
    //Error i feillogg med dato og melding
    error_log('Tid: ' . date("Y-m-d H:i:s") . 'Database error: ' . $e->getMessage());
    
    //Stopper scriptet og gir bruker generell feilmelding uten back-end logikk.
    die("Beklager, det oppstod en feil ved tilkobling til databasen.");
}
?>