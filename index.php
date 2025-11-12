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
        <link rel="stylesheet" href="css/stylesheet.css">
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
                    data-id="<?= htmlspecialchars($book->getBookId()) ?>"
                    data-title="<?= htmlspecialchars($book->getTitle()) ?>"
                    data-authors="<?= htmlspecialchars($book->getAuthors()) ?>"
                    data-description="<?= htmlspecialchars($book->getDescription()) ?>"
                    data-page-count="<?= htmlspecialchars($book->getPageCount()) ?>"
                    data-thumbnail="<?= htmlspecialchars($book->getThumbnail()) ?>">

                    <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                    <?php if ($book->getThumbnail()): ?>
                        <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="Omslag">
                    <?php endif; ?>
                    
                    <p><strong>Bok ID:</strong> <?= htmlspecialchars($book->getBookId()) ?></p>
                    <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
                    <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
                    <p><?= htmlspecialchars($book->getDescription()) ?></p>                
                    
                    <button type="button" class="saveBookBtn">Putt boken i hyllen</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>


        <h2>Ask Gemini</h2>
        <div id="chatbox">
        </div>
        <input type="text" id="prompt" placeholder="Ask something..." style="width:400px;">
        <button id="sendBtn">Send</button><button id="slett_chat">Fjern samtalen</button>

    <script>

    //Lagrer bok når "Putt boken i hyllen" knappen blir trykket på
    document.querySelectorAll(".saveBookBtn").forEach(btn => {
        btn.addEventListener("click", async () => {
            const parent = btn.closest(".book");
            const book = {
                id: parent.dataset.id,
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

    //gemini 
    document.getElementById('sendBtn').addEventListener('click', async () => {
        //vise brukeren at knappen er trykket
        document.getElementById('chatbox').innerHTML = `<p">Snakker med bibliotekaren...<br><br>Dette kan ta noen sekunder:D</p>`;
        
        const prompt = document.getElementById('prompt').value;

        const response = await fetch('api/geminiAPI.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt })
        });

        const data = await response.text(); 
        document.getElementById('chatbox').innerHTML = data;
    });


    document.getElementById('slett_chat').addEventListener('click', async () => {
        if (!confirm("Are you sure you want to reset the chat?")) return; //åpner et vindu i nettleseren, hvor man trykker for å fortsette eller avbryte

        const response = await fetch('Scripts/clear_chat.php');
        const text = await response.text();
        document.getElementById('chatbox').innerHTML = `<p style="color:red;">${text}</p>`;
    });

    </script>
    </html>
