<?php
    include 'scripts/navbar.php';
    require_once 'scripts/sessionStart.php';
    require_once __DIR__ . '/scripts/DB/db.inc.php';
    require_once __DIR__ . '/scripts/sanitizeInputs.inc.php';
    require_once __DIR__ . '/scripts/validation.inc.php';

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
        
        //dersom mail finnes i DB
        //dersom mail IKKE finnes i mail får ikke brukeren vite dette av sikkerhetsmessige hensyn
        if (!(empty($respons))){
            $brukermail = $respons[0]["email"];
            $userid = $respons[0]["userID"];
            $token = (string)bin2hex(random_bytes(32)); //tilfeldig generert token
            $resetlink = "http://localhost/PHP_gruppearbeid/forgottenPassword.php?token=" . $token;
            //sender mail
            try {
                // SMTP oppsett
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $avsender;
                $mail->Password = $passord_phpmailer; //$passord_phpmailer; // "passordet" for smtp sitt bruk av avsendermailen i google
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Avsender og mottaker
                $mail->setFrom($avsender, 'BookFinder'); // php prosjektnavnet til gruppa
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

            $now = date('Y-m-d H:i:sa'); //nåværende dato og tid

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
    //leter etter token i lenke og at det er et passord satt i input-felt
    if (($_SERVER["REQUEST_METHOD"] === "POST") && (isset($_POST["passord"])) && isset($_GET['token'])) {
        //sjekker at passord har god nok standard, nok bokstaver, stor bokstav, siffer osv.
        $result = validatePassword($_POST["passord"]);

        //hvis passordet er for svakt
        if(!$result['valid']) {
            print_r($result['message']);
        } else{
            //hasher nytt passord
            $new_password = password_hash($_POST["passord"], PASSWORD_DEFAULT);

            //henter ut token
            $token = $_GET['token'];

            // Hent UserID fra token
            $stmt = $pdo->prepare("SELECT userid, expiration FROM forgotten_password WHERE reset_token = :reset_token");
            $stmt->execute([':reset_token' => $token]);
            $brukerid = $stmt->fetch();

            //hvis brukerid-en er i DB
            if (!empty($brukerid)){
                $now = time(); //nåværende tid i unix

                //henter tiden når reset token ble lagd, i samme format som $now
                $reset_creation_time = strtotime($brukerid['expiration']);

                //setter utløpstiden for token, altså en time etter opprettelse
                $expiration = $reset_creation_time + 3600;

                //dersom det har gått mer enn en time siden reset token ble lagd
                if ($now >= $expiration){
                    echo "Passord gjennopprettingslenken er utløpt, prøv på nytt!";
                } else {
                    // Oppdater passordet
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = :new_password WHERE UserID = :user_id");
                    $stmt->execute([
                        'new_password' => $new_password,
                        'user_id' => $brukerid['userid']
                    ]);
                    echo "Passord resatt!";
                }
                
                // Sletter alle tokens registrert på brukeren, dette slettes uansett om utløpstiden er valid eller ikke
                $stmt = $pdo->prepare("DELETE FROM forgotten_password WHERE UserID = :user_id");
                $stmt->execute(['user_id' => $brukerid['userid']]);
            } else {
                //enten så finnes ikke token i DB eller så er den ikke koblet til noe bruker
                echo "Noe gikk galt, passord ikke resatt!";
            }
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
    <?php if (isset($_GET['token'])): //dersom det er en token i lenken ?>
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
