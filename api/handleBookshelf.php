<?php 

/*
    MYE AV JSON_ENCODE BURDE ENDRES TIL BEDRE BRUKERGRENSESNITT ETTERHVERT.
*/
session_start();
require_once __DIR__ . "/../classes/Books.php";

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
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ingen id oppgitt']);
        exit;
    }

    foreach($_SESSION['bookshelf'] as $index => $book) {
        if($book->getBookId() === $id) {
            unset($_SESSION['bookshelf'][$index]);
            $_SESSION['bookshelf'] = array_values($_SESSION['bookshelf']);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Bok slettet']);
            exit;
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Fant ikke boken']);
    exit;
}

// Setter dataen som Books klassen trenger
if($data && isset($data['title'])) {
    $book = new Books($data);
                    
    // Legg boken i bookshelf - session
    $_SESSION['bookshelf'][] = $book;


    // Gir melding om det fungerer eller ei
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Bok lagt til bokhyllen!']);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Ugyldige bokdata']);
exit;