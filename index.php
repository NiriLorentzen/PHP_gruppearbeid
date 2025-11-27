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
$canSaveBook = true; //Settes til true på sider der lagre bok knappen skal dukke opp, når man tar i bruk BookCard template

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
    <script src="scripts/JS/main.js" defer></script>
    <script src="scripts/JS/buttons.js" defer></script>
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
window.addEventListener('DOMContentLoaded', () => {
    saveBookBtn();
    geminiChatSendBtn();
    deleteChatBtn();
});
</script>
</body>
</html>