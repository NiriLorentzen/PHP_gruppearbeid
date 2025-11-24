<?php 
require_once __DIR__ . "/../classes/Books.php";
session_start();

header('Content-Type: application/json');


if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ikke valid metode']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($_SESSION['bookshelf'])) {
    $_SESSION['bookshelf'] = [];
}


//Fjerner booken fra bokhyllen.
if(isset($data['action']) && $data['action'] === 'remove') {
    $id = $data['id'] ?? null;
    
    if($id === null) {
        echo json_encode(['success' => false, 'message' => 'Ingen id oppgitt']);
        exit;
    }

    foreach($_SESSION['bookshelf'] as $index => $book) {
        if((string)$book->getBookId() === (string)$id) {
            unset($_SESSION['bookshelf'][$index]);
            $_SESSION['bookshelf'] = array_values($_SESSION['bookshelf']);
            
            echo json_encode(['success' => true, 'message' => 'Bok slettet']);
            exit;
        }
    }    
    echo json_encode(['success' => false, 'message' => 'Fant ikke boken']);
    exit;
}

// Setter dataen som Books klassen trenger
if($data && isset($data['title'])) {
    $book = new Books($data);
                    
    // Legg boken i bookshelf - session
    $_SESSION['bookshelf'][] = $book;


    // Gir melding om det fungerer eller ei    
    echo json_encode(['success' => true, 'message' => 'Bok lagt til bokhyllen!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ugyldige bokdata']);
exit;