<?php
    //set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
    ini_set('default_charset', 'UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    //starter opp en sesjon, om en ikke er fra før av
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    include __DIR__ . '/DB/db.inc.php';



    if(isset($_SESSION['userID']) && isset($_SESSION['active-chatlog']) && (!empty($_SESSION['active-chatlog']))){
        //sjekke om chatlogs har blitt henta ut
        //sjekke om denne chatten er et av de som evt. har blitt henta ut
        //dersom dette er en ny chat, lagre som dette:

        //fjerner " ifra chatloggen, dette vil være et problem for sql-en, den vil avslutte når enn den ser "
        $chatlog = str_replace('"', '', implode("spm/svar", $_SESSION['active-chatlog']));

        //print_r($chatlog);

        //dersom det ikke er en ny chat, men er en chat hentet ifra DB
        if(isset($_SESSION['active-chatlog-id'])){
            //finn chatlog i DB
            $chatid = $_SESSION['active-chatlog-id'];
            $stmt = $pdo->prepare("SELECT * FROM chatlog WHERE chatid = :chatid");
            $stmt->execute([
                ":chatid" => $chatid
            ]);
            $result = $stmt->fetchAll();
            //print_r($result);

            if(isset($result))
                $chatidDB = $result[0]["chatid"];

                // Oppdater passordet
                $stmt = $pdo->prepare("UPDATE chatlog SET chatlog = :chatlog WHERE chatid = :chatid");
                $stmt->execute([
                    ':chatlog' => $chatlog,
                    ":chatid" => $chatidDB
                ]);
        } else{
            $stmt = $pdo->prepare("INSERT INTO chatlog (chatlog, userid) VALUES (:chatlog, :userid)");
            $stmt->execute([
                ":chatlog" => $chatlog,
                ":userid" => $_SESSION['userID']
            ]);
        }

        //redirect
        header("location: ..\user_chats.php");

    } else {
        echo "feil oppstod ved lagringsforsøket!";
    }


?>