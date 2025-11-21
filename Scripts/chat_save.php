<?php
    //starter opp en sesjon, om en ikke er fra før av
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    include __DIR__ . '/DB/db.inc.php';



    if(isset($_SESSION['userID']) && isset($_SESSION['chatlog']) && (!empty($_SESSION['chatlog']))){
        //sjekke om chatlogs har blitt henta ut
        //sjekke om denne chatten er et av de som evt. har blitt henta ut
        //dersom dette er en ny chat, lagre som dette:

        $chatlog = implode("spm/svar", $_SESSION['chatlog']);

        $stmt = $pdo->prepare("INSERT INTO chatlog (chatlog, userid) VALUES (:chatlog, :userid)");
        $stmt->execute([
            ":chatlog" => $chatlog,
            ":userid" => $_SESSION['userID']
        ]);

        //redirect
        header("location: ..\index.php");

    } else {
        echo "feil oppstod ved lagringsforsøket!";
    }


?>