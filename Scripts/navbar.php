<?php
    require_once __DIR__ . '/checkLoginStatus.php';
    include __DIR__ . '/sessionStart.php';   

    // automatisk finner filbanen, nettlesere er strenge på dette så måtte gjøre det grundig
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = str_replace('/Script', '', $scriptPath);

    echo "<div class='navbar'>";    
    echo '<a href="' . $baseUrl . '/index.php"><img src="' . $baseUrl . '/Images/book.png"></a>';
    echo "<a href='" . $baseUrl . "/userChats.php'>Chatside</a>";
    echo "<a href='" . $baseUrl . "/bookDatabase.php'>Bokdatabase</a>"; 
    if(checkLoggedIn()){               
        echo "<a href='" . $baseUrl . "/bookshelf.php'>Din bokhylle</a>";
        echo "<a href='" . $baseUrl . "/userPage.php'>Din side</a>";   
        echo "<a href='" . $baseUrl . "/Scripts/logOut.php' onclick=\"return confirm('Er du sikker på du vil logge ut?')\">Logg ut</a>";    
        if(checkAdmin()){
            echo "<a href='" . $baseUrl . "/adminPage.php'>Adminside</a>";
        }
    } else {
        echo '<a href="' . $baseUrl . '/registerUser.php">Registrer bruker</a>';
        echo "<a href='" . $baseUrl . "/logIn.php'>Logg inn</a>";
    }
    echo "</div>";
?>