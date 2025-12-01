<?php
require_once __DIR__ . '/scripts/sessionStart.php';
require_once __DIR__ . '/scripts/DB/db.inc.php';
require_once __DIR__ . '/scripts/sanitizeInputs.php';
include __DIR__ . '/scripts/navbar.php';

$logInMessage  = "";
$error = [];
$logInData = [];

$totalBlockedTime = 900; //15 min i sekunder
$totalBlockedMinutes = floor($totalBlockedTime / 60);
$totalTries = 3; //Max antall ganger user kan prøve å logge inn

//Sørger for at innlogget er false hvis det ikke var satt fra før
if(!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

//Teller antall ganger user har forsøkt å logge inn
if(!isset($_SESSION['tries'])) {
    $_SESSION['tries'] = 0;
}

//Håndterer utstenging tid, i 15 min. Resetter tries når tiden har gått.
if(isset($_SESSION['blockedTime'])) {
    $timeSinceBlocked = time() - $_SESSION['blockedTime'];
    $timeLeft = $totalBlockedTime - $timeSinceBlocked; 
    

    if($timeLeft > 0) {   
        $minutesLeft = ceil($timeLeft / 60);               
        $logInMessage  = "<h3>For mange forsøk. Du må vente $minutesLeft minutter før du kan prøve igjen.</h3>";
    } else {
        unset($_SESSION['blockedTime']);
        $_SESSION['tries'] = 0;
    }
}

//Behandler POST av innloggingskjema
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $logInData['email'] = sanitizeInputs($_POST['email'] ?? '');
    $logInData['password'] = $_POST['password'] ?? '';

    //Valider at feltene ikke er tomme
    if(empty($logInData['email'])) {
        $error['email'] = "E-postfeltet må fylles ut.";
    }
    if(empty($logInData['password'])) {
        $error['password'] = "Passordfeltet må fylles ut.";
    }

    //Om error arrayen er tom så prøver vi å logge inn
    if(empty($error)) {
        try {
            //Gjør klar sql-query basert på userens unike usernavn(epost i dette systemet)
            $q = $pdo->prepare(
                "SELECT u.userID, u.first_name, u.last_name, u.password_hash, ur.roleID
                FROM users u
                LEFT JOIN user_roles ur ON u.userID = ur.userID
                WHERE u.email = :email");
            $q->bindParam(':email', $logInData['email']);
            $q->execute();
            $user = $q->fetch(PDO::FETCH_ASSOC);
            
            //Sjekker om user finnes og at det hashede-passordet stemmer med password_verify
            if($user && password_verify($logInData['password'], $user['password_hash'])) {
               
                //Nullstiller innlogging tries
                $_SESSION['tries'] = 0;
                unset($_SESSION['blockedTime']);

                $_SESSION['userID'] = (int)$user['userID'];
                $_SESSION['fornavn'] = $user['first_name'];
                $_SESSION['etternavn'] = $user['last_name'];
                $_SESSION['email'] = $logInData['email'];
                $_SESSION['roleID'] = $user['roleID'];
                $_SESSION['loggedIn'] = true;
                
                //Sender bruker videre til user_chats om vellykket
                header("Location: userChats.php");
                exit;

            } else {
                //Hvis innlogging feiler inkrementeres antall tries
                $_SESSION['tries']++;
                $remainingTries = $totalTries - $_SESSION['tries'];

                //Viser user resterende tries eller låser bruke 
                if($remainingTries > 0) {
                    $logInMessage  = "<p>Feil e-post eller passord. Du har $remainingTries forsøk igjen.</p>";
                } else {
                    $_SESSION['blockedTime'] = time();              
                    $logInMessage  = "<h3>Du har prøvd å logge inn $totalTries ganger. Du har blitt stengt ut i $totalBlockedMinutes minutter.</h3>";
                }

                
            }

        } catch(PDOException $e) {
            error_log('Tid: ' . date("Y-m-d H:i:s") . ' Database error: ' . $e->getMessage());
            $logInMessage  = "<p>En feil oppstod ved innlogging. Prøv igjen senere.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innlogging</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>

    
           
    <?php
    //Viser ulike advarsler fra GET.
    if(isset($_GET['warning'])) {
        $warningCode = $_GET['warning'];       

        switch($warningCode) {
            case 'notLoggedIn':
                echo "<p><b>Du ble sendt hit fordi du må være logget inn for å se siden du forsøkte å besøke </b></p>";
                break;        

            case 'loggedOut':
                echo "<p><b>Du har logget ut.</b></p>";
                break;

            case 'wrongPrivileges':
                echo "<p><b>Du ble sendt hit fordi du ikke har riktig rolle for siden du prøvde å besøke</b></p>";
                break;

            default:
                if($urlMelding) echo "<p><b>$urlMelding</b></p>";
        }
    }
?>

    <h2>Logg inn</h2>

    <?php if($logInMessage) echo "<b>" . $logInMessage . "</b>"; ?>
    
    <?php if($_SESSION['loggedIn']): ?>
        <h2>Du er allerede logget inn</h2>
    <?php endif; ?>

    <form method="POST">

        <label for="email">E-post:</label><br>
        <input type="text" id="email" name="email" value="<?= sanitizeInputs($logInData['email'] ?? '') ?>"><br>
        <?php if(isset($error['email'])) echo sanitizeInputs($error['email']) . "</span><br>"; ?>

        <label for="password">Passord:</label><br>
        <input type="password" id="password" name="password"><br>
        <?php if(isset($error['password'])) echo sanitizeInputs($error['password']) . "</span><br>"; ?>

        <button type="submit" <?= isset($_SESSION['blockedTime']) ? 'disabled' : '' ?>>Logg inn</button>
    </form>
    <p>Glemt passordet? <a href="forgottenPassword.php">Tilbakestill det her</a>.</p>
    <hr>
    <p>Har du ikke konto? <a href="registerUser.php">Registrer deg her</a>.</p>

</body>
</html>
