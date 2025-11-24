<?php
    include __DIR__ . '/DB/db.inc.php';
    session_start();

    if(isset($_SESSION['userID'])){
        //sletter bruker med sql statement, ved bruk av userID
        //her er session userid-en allerede sjekket opp mot DB så mer testing trengs egentlig ikke
        $q = $pdo->prepare(
            "DELETE FROM users WHERE userid = :userID;");
        $q->bindParam(':userID', $_SESSION['userID']);
        $q->execute();
        
        //logger ut brukeren fra session
        $_SESSION["userDeletedText"] = true;
        header("location: ../logUt.php");
    }
?>