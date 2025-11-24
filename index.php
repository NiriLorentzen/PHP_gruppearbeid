<?php 
require_once 'api/booksAPI.php';
require_once 'scripts/print_chatlog.php';

include 'scripts/navbar.php';

//starter opp en session 
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Oppretter om det ikke er en fra før av
if (!isset($_SESSION["recommendations_found"])) {
    $_SESSION["recommendations_found"] = array(); 
} 

$recommendations = [];
$geminirecommendations = $_SESSION["recommendations_found"];
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
        <label for="bookRec">Bok database!:</label><br>
        <input type="text" id="bookRec" placeholder="Søk med navn, forfatter" name="bookRec" value="<?= htmlspecialchars($_POST['bookRec'] ?? '') ?>"><br>
        <button type="submit">Søk</button>
    </form>


    <?php  if(!empty($recommendations)): ?>
    <?php foreach ($recommendations as $book): ?>
            <?php include __DIR__ . '/templates/bookCard.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>


    <div class="gemini-tekst-bokser">
        <div>
            <h2>Bibliotekar chat! (gemini)</h2>
            <div id="chatbox" class="chatbox" value=""><?php require_once __DIR__ . '/Scripts/print_chatlog.php'; printchatlog(); ?>
            </div>
            <input type="text" id="prompt" placeholder="Spør et spørsmål..." style="width:400px;">
            <button id="sendBtn">Send</button><button id="slett_chat">Fjern samtalen</button>
            
            <form action="Scripts/chat_save.php" method="post">
                <button type="submit">Lagre denne chatten</button>
            </form>
        </div>
        <div>
            <h2>Bok anbefalinger fått:</h2>
            <div id="chatboxAnbefalinger" class="chatbox" value="">
            
                <?php if(!empty($geminirecommendations)): ?>
                    <?php foreach ($geminirecommendations as $book): ?>
                            <?php include __DIR__ . '/templates/bookCard.php'; ?>
                        <?php endforeach; ?>
                <?php endif; ?>



            </div>
        </div>
    </div>

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

            const response = await fetch("api/handleBookshelf.php", {
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