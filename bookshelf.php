<?php 

    session_start();
    require_once "classes/Books.php";

    if(!isset($_SESSION['bookshelf'])) {
        $_SESSION['bookshelf'] = [];
    }

    if ($_SERVER ['REQUEST_METHOD' === 'POST']) {
        $data = json_decode(file_get_contents("php://input"), true);
    

    //ALT UNDER DETTE FELTET ER TEST AI GENERERT KODE

        if ($data && isset($data['title'])) {
            $book = new Books(
                $data['title'],
                $data['authors'] ?? 'Ukjent forfatter',
                $data['description'] ?? 'Ingen beskrivelse'
            );

            // Sett eventuelle ekstra felter
            if (isset($data['pageCount'])) $book->setPageCount($data['pageCount']);
            if (isset($data['thumbnail'])) $book->setThumbnail($data['thumbnail']);

            // Legg i session
            $_SESSION['bookshelf'][] = $book;

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Bok lagt til bokhyllen!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Ugyldige bokdata']);
            exit;
        }
    }

// Hvis brukeren besøker bookshelf.php direkte – vis bokhylle
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Bokhylle</title>
</head>
<body>
    <h1>Her er din bokhylle</h1>

    <?php if (empty($_SESSION['bookshelf'])): ?>
        <p>Bokhyllen din er tom.</p>
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
?>


<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bokhylle</title>
</head>
<body>
    <h1>Her er din bokhylle</h1>
    
</body>
</html>