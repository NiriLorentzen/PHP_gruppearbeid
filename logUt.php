<?php 
    require_once 'scripts/sessionStart.php';
    require_once __DIR__ . '/classes/ChatManager.php';

    //chatmanager må ha pdo for å kjøre, selv om det ikke brukes av clearChat()
    require_once __DIR__ . '/scripts/DB/db.inc.php';


    unset($_SESSION['userID']);
    unset($_SESSION['fornavn']);
    unset($_SESSION['etternavn']);
    unset($_SESSION['email']);
    unset($_SESSION['roleID']);
    unset($_SESSION['loggedIn']);

    $chatManager = new ChatManager($pdo);
    $chatManager->clearChat();

    include 'scripts/navbar.php';
?>

<html>
    <header>
        <link rel="stylesheet" href="css/stylesheet.css">
    </header>
    <body>
        <?php if(isset($_SESSION['userID'])): ?>
            <p>Utlogging feilet!</p>
        <?php endif; ?>
        <?php if(!isset($_SESSION['userID']) && isset($_SESSION["userDeletedText"]) && $_SESSION["userDeletedText"]): ?>
            <p>Bruker slettet og du er logget ut!</p>
            <?php unset($_SESSION["userDeletedText"]); ?>
            <meta http-equiv="refresh" content="3;url=index.php">
        <?php elseif(!isset($_SESSION['userID'])): ?>
            <strong><p>Du er logget ut!</p></strong>
            <meta http-equiv="refresh" content="3;url=index.php">
        <?php endif; ?>
    </body>
</html>