<?php 
require_once __DIR__ . "/../classes/Books.php";
require_once __DIR__ . "/../classes/BookDB.php";
require_once __DIR__ . "/../scripts/checkLoginStatus.php";
require_once __DIR__ . '/../scripts/sessionStart.php';

header('Content-Type: application/json');
mustBeLoggedIn();
$userID = $_SESSION['userID'] ?? null;


try {
    $bookDB = new BookDB($pdo);
} catch (PDOException $e) {
    //Error i feillogg med dato og melding
    error_log('Tid: ' . date("Y-m-d H:i:s") . 'Database error: ' . $e->getMessage());
    
    //Stopper scriptet og gir bruker generell feilmelding uten back-end logikk.
    die("Beklager, det oppstod en feil ved tilkobling til databasen.");
}

$data = json_decode(file_get_contents("php://input"), true);


//Fjerner booken fra bokhyllen.
if(isset($data['action']) && $data['action'] === 'remove') {
    $bookID = $data['bookID'] ?? null;
    
    if($bookID) {
        $bookDB->userRemoveBook($userID, $bookID);
        echo json_encode(['success' => true, 'message' => 'Bok slettet']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fant ikke boken']);
    }
    exit;
}

//Legger til bok i databasen. Både for bruker og generelt
if($data && isset($data['title'])) {
    
    //MÅ MULIGENS HÅNDTERE FLERE FORFATTERE
    
    $dbData = [
        'bookID'           => $data['bookID'],
        'title'        => $data['title'],
        'authors'           => $data['authors'], 
        'description' => $data['description'] ?? '',
        'pageCount'       => $data['pageCount'] ?? 0
    ];                    
    
    $bookDB->insertBook($dbData);    
    $bookDB->userAddBook($userID, $data['bookID']);


    // Gir melding om det fungerer eller ei    
    echo json_encode(['success' => true, 'message' => 'Bok lagt til bokhyllen!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ugyldig bokdata']);

?>