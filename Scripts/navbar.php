<?php
    //starter opp en session 
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Automatically detect the base path
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = str_replace('/Script', '', $scriptPath);

    echo "<div class='navbar'>";    
    echo '<a href="' . $baseUrl . '/index.php"><img src="' . $baseUrl . '/Images/book.png"></a>';
    if(isset($_SESSION['userID'])){
        echo "<a href='" . $baseUrl . "/logUt.php'>Logg ut</a>";
        echo "<a href='" . $baseUrl . "/bookshelf.php'>Din bokhylle</a>";
        echo "<a href='" . $baseUrl . "/userPage.php'>Din side</a>";
    } else {
        echo '<a href="' . $baseUrl . '/registerUser.php">Registrer bruker</a>';
        echo "<a href='" . $baseUrl . "/logIn.php'>Logg inn</a>";
    }
    echo "</div>";
?>