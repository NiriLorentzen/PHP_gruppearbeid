<?php
require_once __DIR__ . '/../Scripts/sessionStart.php';
require_once __DIR__ . '/../Scripts/DB/db.inc.php';

/*
    Klasse for å håndtere alt om chatlogger utenom printing. Lagring, sletting og henting ifra DB og session
    Brukes i chatPage.php og adminPage.php
*/

class ChatManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    //Lagrer chatlog i DB, enten som ny chat eller oppdatering av eksisterende
    public function saveChat() {
        //set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
        ini_set('default_charset', 'UTF-8');
        header('Content-Type: text/html; charset=utf-8');

        //Sjekker om chatloggen ikke er valid
        if(!$this->chatSessionIsValid()) {
            echo "En feil oppstod ved lagring av chatten.";
            return;
        }

        //fjerner " ifra chatloggen for å ikke ødelgge DB queries.
        $chatlog = str_replace('"', '', implode("spm/svar", $_SESSION['active-chatlog']));

        //Dersom det ikke er en ny chat, men er en chat hentet ifra DB
        if(isset($_SESSION['active-chatlog-id'])){
            $this->updateExistingChatDB($chatlog);
        } else{
            $this->createNewChatDB($chatlog);
        }

        //Laster siden på nytt for å oppdatere endringer
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;    

    }

    //Tømmer chatlog i sesjon. I praksis lager den en ny og tom chat
    public function clearChat() {
        
        unset($_SESSION["active-chatlog"]);
        unset($_SESSION["active-chatlog-id"]);
        unset($_SESSION["recommendations_given"]);
        unset($_SESSION["recommendations_found"]);

    }

    //Sletter chatlog rad i DB og bruker clearChat() for å tømme chatloggen i sesjon
    public function clearChatDB() {
        //finne og slette chatlog rad i DB
        if(isset($_SESSION['active-chatlog-id'])){
            $chatid = (int)$_SESSION['active-chatlog-id'];
            $q = $this->pdo->prepare(
                "DELETE FROM chatlog WHERE chatlog.chatid = :chatid");
            $q->bindParam(':chatid', $chatid);
            $q->execute();
            $logs = $q->fetchAll(PDO::FETCH_ASSOC);
        } else{
            echo "chat id ikke funnet, chatten ble ikke slettet fra din profil.";
        }


        //tømmer chatlog i sesjon, bare lokalt, i praksis lager den en ny og tom chat
        $this->clearChat();

        echo "Chat reset.";

        //Laster siden på nytt for å oppdatere endringer
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    //Tømmer recommendations i sesjon, bare lokalt.
    public function clearRecommendations() {
        
        
        unset($_SESSION["recommendations_found"]);
        unset($_SESSION["recommendations_given"]);

        //Laster siden på nytt for å oppdatere endringer
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    //Henter alle lagrede chats til en bruker fra DB
    public function getUserChats($userID) {
        $q = $this->pdo->prepare("SELECT * FROM chatlog clog WHERE clog.userid = :userID");        
        $q->execute([":userID" => $userID]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    //Finner en chat i DB basert på chatID (er kun ment for admin-brukere)
    public function findChat(int $chatID){
        //Søker i DB etter ID i parameter
        $q = $this->pdo->prepare(
            "SELECT * FROM chatlog WHERE chatid = :chatid");
        $q->execute([":chatid" => $chatID]);
        $chat = $q->fetchAll(PDO::FETCH_ASSOC);
        
        //Dersom det er en chat i DB med den id-en
        if(!empty($chat)){     
            //Chatlog lagres som string, så dette blir gjort om til array for utskrift-funksjonen  
            $chatArray = explode("spm/svar", $chat[0]["chatlog"]);
            //Lagres i sesjon
            $_SESSION['active-chatlog'] = $chatArray;
            return true; //Hjelper med utskrift
        }
    }

    //Sjekker at det er en bruker med ID, og om chatloggen i sesjon er gyldig
    private function chatSessionIsValid() {
        return isset($_SESSION['userID']) && isset($_SESSION['active-chatlog']) && (!empty($_SESSION['active-chatlog']));
    }

    //Lagrer en chat til chatlog tabell i DB
    private function createNewChatDB($chatlog) {
        $q = $this->pdo->prepare("INSERT INTO chatlog (chatlog, userid) VALUES (:chatlog, :userid)");
        $q->execute([
            ":chatlog" => $chatlog,
            ":userid" => $_SESSION['userID']
        ]);
    }

    //Oppdaterer en eksisterende chat i DB
    private function updateExistingChatDB( $chatlog) {
        $chatid = $_SESSION['active-chatlog-id'];
        $q = $this->pdo->prepare("SELECT chatid FROM chatlog WHERE chatid = :chatid");
        $q->execute([":chatid" => $chatid]);
        $result = $q->fetchAll();

        //Om chatid'en finnes i DB, oppdaterer den denne chatloggen
        if(!empty($result)) {
            $chatidDB = $result[0]["chatid"];
            $q = $this->pdo->prepare("UPDATE chatlog SET chatlog = :chatlog WHERE chatid = :chatid");
            $q->execute([
                ':chatlog' => $chatlog,
                ":chatid" => $chatidDB
            ]);
        }
    }
}
?>