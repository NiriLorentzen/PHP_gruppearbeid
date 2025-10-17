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


<script>

/*
//Henter tekst lagt inn i bookForm, sender søket til booksAPI.php. Leser svaret som json
        document.getElementById("bookForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const query = document.getElementById("bookRec").value;
            const response = await fetch("api/booksAPI.php?q=" + encodeURIComponent(query));
            const books = await response.json();
//Fjerner gamle resultater
            const resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = "";
//Feilmeldinger
            if (books.error) {
                resultsDiv.innerHTML = "<p>" + books.error + "</p>";
                return;
            }

//Lager en div for hver bok anbefalning med tittel, forfatter, side antall og bok beskrivelse
            books.forEach(book => {
                const div = document.createElement("div");
                div.className = "book";
                div.innerHTML = `
                    ${book.thumbnail ? `<img src="${book.thumbnail}" alt="Bokomslag">` : ""}
                    <div>
                        <h3>${book.title}</h3>
                        <p><strong>Forfatter:</strong> ${book.authors}</p>
                        <p><strong>Antall Sider:</strong> ${book.pageCount}</p>                        
                        <p>${book.description}</p>
                        <button type="button" class="saveBookBtn">Putt boken i hyllen</button>
                    </div>
                `;
                resultsDiv.appendChild(div);
//Lagrer bok når "Putt boken i hyllen" knappen blir trykket på
                div.querySelector(".saveBookBtn").addEventListener("click", async () => {
                    const response = await fetch("bookshelf.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(book)
                    });
                    const result = await response.json();
                    alert(result.message);
                });
            });
        });

*/
</script>
</html>
