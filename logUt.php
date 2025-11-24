<?php 
    include 'scripts/navbar.php';

    unset($_SESSION['userID']);
    unset($_SESSION['fornavn']);
    unset($_SESSION['etternavn']);
    unset($_SESSION['email']);
    unset($_SESSION['roleID']);
    unset($_SESSION['loggedIn']);
?>

<html>
    <header>
        <link rel="stylesheet" href="css/stylesheet.css">
    </header>
    <body>
        <?php if(isset($_SESSION['userID'])): ?>
            <p>Utlogging feilet!</p>
        <?php endif; ?>
        <?php if(!isset($_SESSION['userID'])): ?>
            <strong><p>Du er nå logget ut!</p></strong>
            <meta http-equiv="refresh" content="3;url=index.php">
        <?php endif; ?>
    </body>
</html>