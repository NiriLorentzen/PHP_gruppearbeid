<?php 
    //starter opp en session 
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }


    unset($_SESSION['userID']);
    unset($_SESSION['fornavn']);
    unset($_SESSION['etternavn']);
    unset($_SESSION['email']);
    unset($_SESSION['roleID']);
    unset($_SESSION['loggedIn']);

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
        <?php elseif(!isset($_SESSION['userID'])): ?>
            <strong><p>Du er logget ut!</p></strong>
            <meta http-equiv="refresh" content="3;url=index.php">
        <?php endif; ?>
    </body>
</html>