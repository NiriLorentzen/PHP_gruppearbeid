<?php 
    //set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
    ini_set('default_charset', 'UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    require_once __DIR__ . '/api/booksAPI.php';
    require_once __DIR__ . '/scripts/sessionStart.php';
    require_once __DIR__ . '/scripts/DB/db.inc.php';
    require_once __DIR__ . '/scripts/printChatlog.php';
    require_once __DIR__ . '/scripts/checkLoginStatus.php';    
    require_once __DIR__ . '/classes/ChatManager.php';

    include __DIR__ . '/scripts/navbar.php';


    $chatManager = new ChatManager($pdo);

    //for chat-velger funksjon
    $oldChats = [];

    $canSaveBook = true; //Settes til true på sider der lagre bok knappen skal dukke opp, når man tar i bruk BookCard template

    if(isset($_SESSION['userID'])) {        
        $oldChats[] = $chatManager->getUserChats($_SESSION['userID']);        
    }

    // Håndter knapper og chat-laster (POST)
    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Hvis en acti handler for knapper
        if(isset($_POST['action'])) {

            switch ($_POST['action']) {

                case 'newChat':
                    $chatManager->clearChat();
                    header("Location: userChats.php");
                    exit;

                case 'deleteChat':
                    $chatManager->clearChatDB();
                    exit;

                case 'saveChat':
                    $chatManager->saveChat();
                    exit;

                case 'clearRecs':
                    $chatManager->clearRecommendations();
                    exit;
            }
        }
        // Hvis lagret chat (hentet fra db) ble lastet (ingen action, men chatlog og chatid)
        elseif(isset($_POST['chatlog']) && isset($_POST['chatid'])) {
            $chat_array = explode("spm/svar", $_POST['chatlog']);
            $_SESSION['active-chatlog'] = $chat_array;
            $_SESSION['active-chatlog-id'] = $_POST['chatid'];

            unset($_SESSION["recommendations_found"]);
            unset($_SESSION["recommendations_given"]);

            header("Location: userChats.php");
            exit;
        }
    }

    // Oppretter om det ikke er en fra før av
    if(!isset($_SESSION["recommendations_found"])) {
        $_SESSION["recommendations_found"] = array(); 
    } 

    $geminirecommendations = $_SESSION["recommendations_found"];
?>



<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat side</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <script src="scripts/JS/buttons.js" defer></script>
</head>
<body>
    <?php if(checkLoggedIn()): ?>
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
    <?php endif; ?>
    <div class="gemini-tekst-bokser">
        <div>
            <h2>Snakk med bibliotekaren her!</h2>
            <div class="chatbox" id="chatbox">
                <?php if(isset($_SESSION["chat-errors"]) && !empty($_SESSION["chat-errors"])): ?>
                    <ul><strong>Chat-feil:</strong>
                    <?php foreach($_SESSION["chat-errors"] as $error):?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                    <?php unset($_SESSION["chat-errors"]); ?>
                    </ul>
                <?php else: ?>
                    <?php printchatlog(); ?>
                <?php endif; ?>
                
            </div>
            <input type="text" id="prompt" placeholder="Spør et spørsmål..." style="width:400px;">
            <button id="sendBtn">Send</button>
        

            <form method="post">
                <input type="hidden" name="action" value="newChat">
                <button type="submit" id="newChatBtn">Ny chat</button>
            </form>

        <?php if(checkLoggedIn()): ?>  
            <form method="post" onsubmit="return confirm('Er du sikker?');">
                <input type="hidden" name="action" value="deleteChat">
                <button type="submit" id="deleteChatBtn">Slett chat</button>
            </form>            
            <form method="post">
                <input type="hidden" name="action" value="saveChat">
                <button type="submit">Lagre denne chatten</button>
            </form>
        <?php endif; ?>

        </div>
        <div>
            <h2>Bokanbefalinger fått:</h2>
            <form method="post">
                <input type="hidden" name="action" value="clearRecs">
                <button type="submit">Tøm anbefalinger</button>
            </form>

            <div id="chatboxAnbefalinger" class="chatbox" value="">
                <?php if(!empty($geminirecommendations)): //utskrift av googlebooksapi-resultatet fra gemini-svaret, i riktig template?>
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
    });
    </script>
</body>
</html>