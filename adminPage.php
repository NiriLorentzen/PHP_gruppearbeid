<?php 
require_once __DIR__ . '/scripts/printChatlog.php';
require_once __DIR__ . '/scripts/checkLoginStatus.php';
require_once __DIR__ . '/scripts/sessionStart.php';
require_once __DIR__ . '/scripts/DB/db.inc.php';
require_once __DIR__ . '/classes/ChatManager.php';

include __DIR__ . '/scripts/navbar.php';

//sjekker om det er en innlogget admin, ellers blir man videresendt til innlogging (uten at resten av koden her blir kjørt)
mustBeAdmin();

//henter alle brukere
$q = $pdo->prepare(
    "SELECT * FROM users");
$q->execute();
$users = $q->fetchAll(PDO::FETCH_ASSOC);

//rollenavn og chats
foreach($users as $user){
    //finner rollenavnet til brukeren
    $userid = $user["userID"];
    $q = $pdo->prepare(
        "SELECT roles.name FROM roles LEFT JOIN user_roles ur ON ur.roleID = roles.roleID WHERE ur.userID = :userid");
    $q->execute([":userid" => $userid]);
    $user_role = $q->fetchAll(PDO::FETCH_ASSOC);

    //lagrer rollenavnet i roles med userID som nøkkel
    //alle brukere MÅ ha en rolle for å eksistere i DB, så det er ikke noe mer sjekk her
    $roles[$userid] = $user_role[0]["name"];

    //leter etter brukeren sine chats
    $q = $pdo->prepare(
        "SELECT chatid FROM chatlog WHERE chatlog.userID = :userid");
    $q->execute([":userid" => $userid]);
    $chats = $q->fetchAll(PDO::FETCH_ASSOC);

    //hvis brukeren har noen lagrede chats
    if (!empty($chats)){
        //lagrer id-ene i array
        foreach($chats as $chat){
            $user_chats[$userid][] = $chat["chatid"];
        }
        //samler array-et i en streng
        $user_chats[$userid] = implode(", ", $user_chats[$userid]);
    } else {
        $user_chats[$userid] = "ingen chats";
    }

}

//chatsøk, skal bruke chatid til å hente ut chatloggen
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $chatID = $_POST['chatid'];
    $search = new ChatManager($pdo);
    if ($search->findChat($chatID)){
        $chat_funnet = true;
    } else{
        echo "chat ikke funnet";
    }
}

?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">    
    <title>BookFinder</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
    <h1>Adminside</h1>
    <table>
        <tbody>
            <tr>
                <th>UserID</th>
                <th>Fornavn</th>
                <th>Etternavn</th>
                <th>Mail</th>
                <th>Rolle</th>
                <th>Chats (ID)</th>
            </tr>
            <?php foreach($users as $user):?>
                <tr>
                    <td><?php echo $user["userID"] ?></td>
                    <td><?php echo $user["first_name"] ?></td>
                    <td><?php echo $user["last_name"] ?></td>
                    <td><?php echo $user["email"] ?></td>
                    <td><?php echo $roles[$user["userID"]] ?></td>
                    <td><?php echo $user_chats[$user["userID"]] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Finn en chatlog</strong></p>
    <?php if(isset($chat_funnet) && $chat_funnet):?>
        <div class="chatbox" id="chatbox"><?php printchatlog(); ?></div>
        <?php unset($_SESSION['active-chatlog']); //chatloggen skal bare vises en gang og skal ikke være synlig hvis man bytter side ?>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="chatid">Chat-ID:</label>
        <input type="text" id="chatid" name="chatid">
        <button type="submit">Søk</button>
    </form>
</body>
</html>