<?php
    include 'scripts/navbar.php';
    require_once 'scripts/sessionStart.php';
    require_once __DIR__ . '/scripts/DB/db.inc.php';
    require_once __DIR__ . '/scripts/sanitizeInputs.inc.php';

    //henter gjemt informasjon om mail utsending (innlogging info)
    require_once __DIR__ . '/scripts/config.php';
    $avsender = $avsender_config;
    $passord_phpmailer = $passord_phpmailer_config;



    //importer og hent frem phpmailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once "libs/PHPMailer-master/src/PHPMailer.php";
    require_once "libs/PHPMailer-master/src/SMTP.php";
    require_once "libs/PHPMailer-master/src/Exception.php";

    $mail = new PHPMailer(true);

    //sende lenke 
    if(($_SERVER["REQUEST_METHOD"] === "POST") && (isset($_POST["mail"]))){
        $mail_input = sanitizeInputs($_POST["mail"]);

        //søker om mail allerede eksisterer i DB
        $stmt = $pdo->prepare("SELECT email, userID FROM users WHERE users.email = :email");
        $stmt->execute([":email"=>$mail_input]);
        $respons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //print_r($respons); eksempel: Array ( [0] => Array ( [email] => ***@example.com [userID] => 3 ) )
        //dersom mail finnes i DB
        if (!(empty($respons))){
            $brukermail = $respons[0]["email"];
            $userid = $respons[0]["userID"];
            $token = (string)bin2hex(random_bytes(32)); //tilfeldig generert token
            $resetlink = "http://localhost/PHP_gruppearbeid/forgotten_password.php?token=" . $token;
            //sender mail
            try {
                // SMTP oppsett
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $avsender;
                $mail->Password = 'vgqwsmfuhzgiaapk'; // "passordet" for smtp sitt bruk av avsendermailen i google
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Avsender og mottaker
                $mail->setFrom('bareforinnlevering10iphp@gmail.com', 'BookFinder'); // php prosjektnavnet til gruppa
                $mail->addAddress($brukermail);

                // Innhold, med lenke
                $mail->isHTML(true);
                $mail->Subject = 'Tilbakestill passord';
                $mail->Body = "Klikk her for å tilbakestille passord: <a href='$resetlink'>$resetlink</a>";

                $mail->send();
                echo 'Resetting lenke sendt på mail!';
            } catch (Exception $e) {
                echo "Kunne ikke sende e-post: {$mail->ErrorInfo}";
            }

            $now = date('Y-m-d');

            //lager en ny rad i DB for å validere token seinere
            $stmt = $pdo->prepare("INSERT INTO forgotten_password (UserID, reset_token, expiration) VALUES (:UserID, :reset_token, :expiration)");
            $stmt->execute([
                ":UserID" => $userid,
                ":reset_token" => $token,
                ":expiration" => $now
                ]);
            $respons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    
    //passord gjenoppretting
    if (($_SERVER["REQUEST_METHOD"] === "POST") && (isset($_POST["passord"])) && isset($_GET['token'])) {
        //hasher nytt passord
        $new_password = password_hash($_POST["passord"], PASSWORD_DEFAULT);

        //henter ut token
        $token = $_GET['token'];

        // Hent UserID fra token
        $stmt = $pdo->prepare("SELECT UserID FROM forgotten_password WHERE reset_token = :reset_token");
        $stmt->execute([':reset_token' => $token]);
        $brukerid = $stmt->fetch();

        //hvis brukerid-en er i DB
        if (isset($brukerid)){
            // Oppdater passordet
            $stmt = $pdo->prepare("UPDATE users SET password_hash = :new_password WHERE UserID = :user_id");
            $stmt->execute([
                'new_password' => $new_password,
                'user_id' => $brukerid['UserID']
            ]);
            
            // Sletter alle tokens registrert på brukeren
            $stmt = $pdo->prepare("DELETE FROM forgotten_password WHERE UserID = :user_id");
            $stmt->execute(['user_id' => $brukerid['UserID']]);

            echo "Passord resatt!";
        } else {
            echo "Noe gikk galt, passord ikke resatt!";
        }
    }



?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gjennoppretting</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
    <h2>Passord gjennoppretting</h2>
    <?php if (isset($_GET['token'])): ?>
        <form action="" method="POST">
            <label for="passord">Nytt passord:</label>
            <input type="text" id="passord" name="passord"><br>
            <input type="submit" value="Reset passord">
        </form>
    <?php else: ?>
    <form action="" method="POST">
            <label for="mail">Mail:</label>
            <input type="text" id="mail" name="mail"><br>
            <input type="submit" value="Reset passord">
    </form> 
    <?php endif; ?>

    <p>Har du ikke konto? <a href="registerUser.php">Registrer deg her</a>.</p>

</body>
</html>
