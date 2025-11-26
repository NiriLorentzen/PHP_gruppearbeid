<?php 
    //set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
    ini_set('default_charset', 'UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    session_start();
    include __DIR__ . '/scripts/DB/db.inc.php';
    require_once 'scripts/print_chatlog.php';

    //for chat-velger funksjon
    $oldChats = [];

    if(isset($_SESSION['userID'])){
        $q = $pdo->prepare(
            "SELECT * FROM chatlog clog WHERE clog.userid = :userID");
        $q->bindParam(':userID', $_SESSION['userID']);
        $q->execute();
        $logs = $q->fetchAll(PDO::FETCH_ASSOC);
        $oldChats[] = $logs;
        //print_r($oldChats);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //print_r($_POST);
        $chat_array = explode("spm/svar", $_POST['chatlog']);
        $_SESSION['active-chatlog'] = $chat_array;
        $_SESSION['active-chatlog-id'] = $_POST['chatid'];
        //printchatlog();
        //header("Refresh:0");
    }

    include 'scripts/navbar.php';

?>



<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatlogs</title>
    <link rel="stylesheet" href="css/stylesheet.css">
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
            <button id="sendBtn">Send</button><button id="slett_chat">Slett chat</button>
            
            <form action="Scripts/chat_save.php" method="post">
                <button type="submit">Lagre denne chatten</button>
            </form>
        </div>
    </div>
    
    <script>
    //gemini 
    document.getElementById('sendBtn').addEventListener('click', async () => {
        //vise brukeren at knappen er trykket
        document.getElementById('chatbox').innerHTML = `<p">Snakker med bibliotekaren...<br><br>Dette kan ta noen sekunder:D</p>`;
        
        const prompt = document.getElementById('prompt').value;

        const response = await fetch('api/geminiAPI.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt })
        });

        const data = await response.text(); 
        document.getElementById('chatbox').innerHTML = data;
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