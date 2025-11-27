<?php 
    //set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
    ini_set('default_charset', 'UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    require_once 'api/booksAPI.php';
    require_once 'scripts/sessionStart.php';
    require_once __DIR__ . '/scripts/DB/db.inc.php';
    require_once 'scripts/print_chatlog.php';
    require_once 'scripts/checkLoginStatus.php';
    

    //for chat-velger funksjon
    $oldChats = [];
    $canSaveBook = true; //Settes til true på sider der lagre bok knappen skal dukke opp, når man tar i bruk BookCard template

    if(isset($_SESSION['userID'])){
        $q = $pdo->prepare(
            "SELECT * FROM chatlog clog WHERE clog.userid = :userID");
        $q->bindParam(':userID', $_SESSION['userID']);
        $q->execute();
        $logs = $q->fetchAll(PDO::FETCH_ASSOC);
        $oldChats[] = $logs;
        //print_r($oldChats);
    }

    //eneste koden her som bruker POST er lagrede chatter knappen, VIKTIG at ingen andre har post, trengs det må fremgangsmåten endres på
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //print_r($_POST);
        //echo "test";
        $chat_array = explode("spm/svar", $_POST['chatlog']);
        $_SESSION['active-chatlog'] = $chat_array;
        $_SESSION['active-chatlog-id'] = $_POST['chatid'];

        //include __DIR__ . '/scripts/clear_recommendation.php';
        //printchatlog();
        //header("Refresh:0");
        unset($_SESSION["recommendations_found"]);
        unset($_SESSION["recommendations_given"]);
    }

    // Oppretter om det ikke er en fra før av
    if(!isset($_SESSION["recommendations_found"])) {
        $_SESSION["recommendations_found"] = array(); 
    } 

    $geminirecommendations = $_SESSION["recommendations_found"];

    include 'scripts/navbar.php';

    if(isset($_SESSION["chat-errors"])){
        //print_r($_SESSION["chat-errors"]);
    }

?>



<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatlogs</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <script src="scripts/JS/buttons.js" defer></script>
</head>
<body>
    <?php foreach($oldChats as $chat):?>
        <div class="chats-menu"> <p>Dine lagrede chatter:</p>
            <?php foreach($chat as $chatlog): ?>
                <form method="post">
                    <input type="hidden" id="chatid" name="chatid" value="<?php echo $chatlog["chatid"] ?>">
                    <button type="submit" id="chatlog" name="chatlog" value="<?php echo $chatlog["chatlog"] ?>"><?php echo $chatlog["chatid"] ?></button>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <div class="gemini-tekst-bokser">
        <div>
            <h2>Bibliotekar chat! (gemini)</h2>
            <div class="chatbox" id="chatbox">
                <?php printchatlog(); ?>
            </div>
            <input type="text" id="prompt" placeholder="Spør et spørsmål..." style="width:400px;">
            <button id="sendBtn">Send</button><button id="ny_chat">Ny chat</button>
            <button id="slett_chat">Slett chat</button>
            
            <form action="Scripts/chat_save.php">
                <button type="submit">Lagre denne chatten</button>
            </form>
        </div>
        <div>
            <h2>Bok anbefalinger fått:</h2>
            <form action="Scripts/clear_recommendation.php">
                <button type="submit">Tøm anbefalinger</button>
            </form>
            <div id="chatboxAnbefalinger" class="chatbox" value="">
                <?php if(!empty($geminirecommendations)): ?>
                    <?php foreach ($geminirecommendations as $book): ?>
                            <?php include __DIR__ . '/templates/bookCard.php'; ?>
                        <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>

    window.addEventListener('DOMContentLoaded', () => {
        saveBookBtn();
        geminiChatSendBtn();
        deleteChatBtn();
    });  
   

    document.getElementById('ny_chat').addEventListener('click', async () => {
        const response = await fetch('Scripts/clear_chat.php');
        const text = await response.text();
        document.getElementById('chatbox').innerHTML = `<p style="color:red;">Ny chat!</p>`;
    });

    document.getElementById('slett_chat').addEventListener('click', async () => {
        if (!confirm("Are you sure you want to reset the chat?")) return; //åpner et vindu i nettleseren, hvor man trykker for å fortsette eller avbryte

        const response = await fetch('Scripts/clear_chat_DB.php');
        const text = await response.text();
        document.getElementById('chatbox').innerHTML = `<p style="color:red;">${text}</p>`;
    });
    </script>
</body>
</html>