<?php 
require_once 'api/booksAPI.php';
require_once 'scripts/print_chatlog.php';
require_once 'scripts/checkLoginStatus.php';
require_once 'Scripts/sessionStart.php';

include 'scripts/navbar.php';


// Oppretter om det ikke er en fra før av
if(!isset($_SESSION["recommendations_found"])) {
    $_SESSION["recommendations_found"] = array(); 
} 

$recommendations = [];
$geminirecommendations = $_SESSION["recommendations_found"];
$error = "";
$canSaveBook = true;

if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bookRec'])) {
    try {
        $api = new GoogleBooksApi();       
        $recommendations = $api->fetchBooks($_POST['bookRec']);
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


    <?php if(!empty($recommendations)): ?>
        <?php foreach ($recommendations as $book): ?>
            <div>
                <?php include __DIR__ . '/templates/bookCard.php'; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!checkLoggedIn()): ?>
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
    <?php else: ?>
        <p>Snakke med bibliotekar? Det kan du gjøre <a href="http://localhost/PHP_gruppearbeid/user_chats.php">her</a> .</p>
    <?php endif; ?>
    <script>

    //Lagrer bok når "Putt boken i hyllen" knappen blir trykket på
    document.querySelectorAll(".saveBookBtn").forEach(btn => {
        btn.addEventListener("click", async () => {
            const parent = btn.closest(".book");
            const book = {
                bookID: parent.dataset.bookId,
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

           
            const responseText = await response.text();        

            try {
                const result = JSON.parse(responseText);
                alert(result.message);
            } catch (e) {
                alert("Serveren returnerte ikke gyldig JSON. Sjekk konsollen.");
            }

            
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