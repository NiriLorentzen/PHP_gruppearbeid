<?php
    require_once __DIR__ . '/checkLoginStatus.php';
    require_once __DIR__ . '/sessionStart.php';   

    // automatisk finner filbanen, nettlesere er strenge på dette så måtte gjøre det grundig
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = str_replace('/Script', '', $scriptPath);

    echo "<div class='navbar'>";    
    echo '<a href="' . $baseUrl . '/index.php"><img src="' . $baseUrl . '/Images/book.png"></a>';
    if(checkLoggedIn()){
        echo "<a href='" . $baseUrl . "/user_chats.php'>Hovedside</a>";
        echo "<a href='" . $baseUrl . "/bookDatabase.php'>Bokdatabase</a>";        
        echo "<a href='" . $baseUrl . "/bookshelf.php'>Din bokhylle</a>";
        echo "<a href='" . $baseUrl . "/userPage.php'>Din side</a>";   
        echo "<a href='" . $baseUrl . "/logUt.php'>Logg ut</a>";    
        if(checkAdmin()){
            echo "<a href='" . $baseUrl . "/adminPage.php'>Adminside</a>";
        }
    } else {
        echo "<a href='" . $baseUrl . "/user_chats.php'>Hovedside</a>";
        echo '<a href="' . $baseUrl . '/registerUser.php">Registrer bruker</a>';
        echo "<a href='" . $baseUrl . "/logIn.php'>Logg inn</a>";
    }
    echo "</div>";
?>