<?php 

/*
    MYE AV JSON_ENCODE BURDE ENDRES TIL BEDRE BRUKERGRENSESNITT ETTERHVERT.
*/

require_once "classes/Books.php";
session_start();

include 'scripts/navbar.php';

if(!isset($_SESSION['bookshelf'])) {
    $_SESSION['bookshelf'] = [];
}

if($_SERVER ['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true); 
        
    
    
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
    
}

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <script src="main.js" defer></script>
    <title>Bokhylle</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
    <h1>Din Bokhylle</h1>

    <?php if (empty($_SESSION['bookshelf'])): ?>
        <h2>Bokhyllen din er tom.</h2>
    <?php else: ?>
        <?php foreach($_SESSION['bookshelf'] as $index => $book): ?>
            <div class="bookItem"style="border:1px solid #ccc; padding:10px; margin:10px;">

                <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
                <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
                <p><?= htmlspecialchars($book->getDescription()) ?></p>

                <?php if ($book->getThumbnail()): ?>
                    <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="bokomslag">
                <?php endif; ?>

                <button type="button" class="removeBookBtn" data-id="<?= $book->getBookId() ?>">Fjern boken fra hyllen</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

<script>
document.querySelectorAll(".removeBookBtn").forEach(btn => {
    btn.addEventListener("click", async () => {
        
        const bookIndex = btn.dataset.index;
        const bookItem = btn.closest(".bookItem");

        if(!confirm("Er du sikker på du vil fjerne boken?")) {
            return;
        }

        const response = await fetch("bookshelf.php" ,{
            method: "POST",
            headers: { "Content-Type": "application/json" },

            body: JSON.stringify({
                action: "remove",
                id: btn.dataset.id
            })
        });

        const result = await response.json();
        alert(result.message);

        if(result.success) {
            bookItem.remove();
        }    

    });
});


</script>    

</html>