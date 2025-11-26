<?php
    session_start();
    include __DIR__ . '/DB/db.inc.php';

    //starter opp en sesjon, om en ikke er fra før av
    if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    }

    //finne og slette chatlog rad i DB
    if(isset($_SESSION['active-chatlog-id'])){
        $chatid = (int)$_SESSION['active-chatlog-id'];
        $q = $pdo->prepare(
            "DELETE FROM chatlog WHERE chatlog.chatid = :chatid");
        $q->bindParam(':chatid', $chatid);
        $q->execute();
        $logs = $q->fetchAll(PDO::FETCH_ASSOC);
    } else{
        echo "chat id ikke funnet, chatten ble ikke slettet fra din profil.";
    }


    //tømmer chatlog i sesjon, bare lokalt altså, i praksis lager den en ny og tom chat
    unset($_SESSION["active-chatlog"]);
    unset($_SESSION["active-chatlog-id"]);
    unset($_SESSION["recommendations_given"]);
    unset($_SESSION["recommendations_found"]);
    unset($_SESSION["recommendations_found"]);

    echo "Chat reset.";

    header("Refresh: 0"); 
?>