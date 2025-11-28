<?php 
require_once 'api/booksAPI.php';
require_once 'scripts/sessionStart.php';

include 'scripts/navbar.php';

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

<script>
window.addEventListener('DOMContentLoaded', () => {
    saveBookBtn();
    geminiChatSendBtn();    
});
</script>
</body>
</html>