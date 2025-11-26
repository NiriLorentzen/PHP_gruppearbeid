<?php 
require_once "classes/Books.php";
require_once "classes/BookDB.php";
require_once 'scripts/DB/db.inc.php';
require_once 'scripts/checkLoginStatus.php';

include 'scripts/navbar.php';


mustBeLoggedIn();

$usersBooks = [];
if(isset($_SESSION['userID'])) {
    $bookDB = new BookDB($pdo);
    $usersBooks = $bookDB->userFetchAllBooks($_SESSION['userID']);
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
    
    <?php if(empty($usersBooks)): ?>
        <h2>Bokhyllen din er tom.</h2>
    <?php else: ?>
        <?php foreach($usersBooks as $book): ?>
            <div class="bookItem"style="border:1px solid #ccc; padding:10px; margin:10px;">

                <h3><?= htmlspecialchars($book->getTitle()) ?></h3>                
                <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
                <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
                <p><?= htmlspecialchars($book->getDescription()) ?></p>
                <p><strong>Bok ID:</strong> <?= htmlspecialchars($book->getBookId()) ?></p>

                <?php if($book->getThumbnail()): //HAR IKKE LAGT TIL THUMBNAIL I DATABASE ENDA ?>
                    <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="bokomslag">
                <?php endif; ?>

                <button type="button" class="removeBookBtn" data-id="<?= $book->getBookId() ?>">Fjern boken fra hyllen</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<script>
document.querySelectorAll(".removeBookBtn").forEach(btn => {
    btn.addEventListener("click", async () => {
                
        const bookItem = btn.closest(".bookItem");
        const bookId = btn.dataset.id;

        if(!confirm("Er du sikker på du vil fjerne boken?")) {
            return;
        }

        const response = await fetch("api/handleBookshelf.php" ,{
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "remove",
                bookID: bookId
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
</body> 
</html>