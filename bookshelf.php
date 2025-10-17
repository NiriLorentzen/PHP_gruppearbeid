<?php 

    require_once "classes/Books.php";
    session_start();
    

    if(!isset($_SESSION['bookshelf'])) {
        $_SESSION['bookshelf'] = [];
    }

    if ($_SERVER ['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);    

    // Setter dataen som Books klassen trenger
        if ($data && isset($data['title'])) {
            $book = new Books($data);
                            
            // Legg boken i bookshelf - session
            $_SESSION['bookshelf'][] = $book;


            // Gir melding om det fungerer eller ei
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Bok lagt til bokhyllen!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Ugyldige bokdata']);
            exit;
        }
    }


?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <script src="main.js" defer></script>
    <title>Bokhylle</title>
</head>
<body>
    <h1>Din Bokhylle</h1>

    <?php if (empty($_SESSION['bookshelf'])): ?>
        <h2>Bokhyllen din er tom.</h2>
    <?php else: ?>
        <?php foreach ($_SESSION['bookshelf'] as $book): ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px;">

                <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
                <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
                <p><?= htmlspecialchars($book->getDescription()) ?></p>

                <?php if ($book->getThumbnail()): ?>
                    <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="Omslag">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>