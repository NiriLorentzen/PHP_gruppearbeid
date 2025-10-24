<?php 

require_once 'api/booksAPI.php';

$recommendations = [];
$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bookRec'])) {
    try {
        $api = new GoogleBooksApi();
        //Denne er mer midlertidig, fjerner filler ord.
        $cleanQuery = $api->cleanQuery($_POST['bookRec']);
        $recommendations = $api->fetchBooks($cleanQuery);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">    
    <title>BookFinder</title>
     <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #results { margin-top: 20px; }
        .book { border: 1px solid #ccc; padding: 10px; margin: 10px 0; display: flex; gap: 10px; }
        img { height: 100px; }
    </style>
    <script src="main.js" defer></script>
</head>
<body>
    <h1>BookFinder</h1>


    <form method="POST" action="">
        <label for="bookRec">Spør om bøker!:</label><br>
        <input type="text" id="bookRec" name="bookRec" value="<?= htmlspecialchars($_POST['bookRec'] ?? '') ?>"><br>
        <button type="submit">Søk</button>
    </form>


    <?php  if(!empty($recommendations)): ?>
    <?php foreach ($recommendations as $book): ?>
            <div class="book"
                data-title="<?= htmlspecialchars($book->getTitle()) ?>"
                data-authors="<?= htmlspecialchars($book->getAuthors()) ?>"
                data-description="<?= htmlspecialchars($book->getDescription()) ?>"
                data-pageCount="<?= htmlspecialchars($book->getPageCount()) ?>"
                data-thumbnail="<?= htmlspecialchars($book->getThumbnail()) ?>">

                <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                <?php if ($book->getThumbnail()): ?>
                    <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="Omslag">
                <?php endif; ?>
                <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
                <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
                <p><?= htmlspecialchars($book->getDescription()) ?></p>                
                
                <button type="button" class="saveBookBtn">Putt boken i hyllen</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


<script>

//Lagrer bok når "Putt boken i hyllen" knappen blir trykket på
document.querySelectorAll(".saveBookBtn").forEach(btn => {
    btn.addEventListener("click", async () => {
        const parent = btn.closest(".book");
        const book = {
            title: parent.dataset.title,
            authors: parent.dataset.authors,
            description: parent.dataset.description,
            pageCount: parent.dataset.pageCount,
            thumbnail: parent.dataset.thumbnail
        };

        const response = await fetch("bookshelf.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(book)
        });

        const result = await response.json();
        alert(result.message);
    });
});

</script>
</html>
