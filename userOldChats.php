<?php 
    session_start();
    include __DIR__ . '/scripts/DB/db.inc.php';
    require_once 'scripts/print_chatlog.php';

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
    //echo "hei";

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
    <div class="chatbox">
    <?php foreach($oldChats as $chat):?>
        <?php foreach($chat as $chatlog): ?>
            <a>Chat id: <?php echo $chatlog["chatid"] ?></a>
            <?php $_SESSION['chatlog'] = explode("spm/svar", $chatlog["chatlog"]); printchatlog(); ?>
            <br><br>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </div>
</body>
</html>