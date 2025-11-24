<?php 
require_once "classes/Books.php";
session_start();

include 'scripts/navbar.php';
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

<script>
document.querySelectorAll(".removeBookBtn").forEach(btn => {
    btn.addEventListener("click", async () => {
                
        const bookItem = btn.closest(".bookItem");

        if(!confirm("Er du sikker på du vil fjerne boken?")) {
            return;
        }

        const response = await fetch("api/handleBookshelf.php" ,{
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
</body> 
</html>