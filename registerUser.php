<?php 

include __DIR__ . '/scripts/DB/db.inc.php';
include __DIR__ . '/scripts/validation.inc.php';
include __DIR__ . '/scripts/wash.inc.php';


$userData = [];
$error = [];
$message = "";
    
    
//Fyller $userData matrisen
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userData['firstName'] = wash($_POST['fname'] ?? '');
    $userData['lastName'] = wash($_POST['lname'] ?? '');    
    $userData['email'] = wash($_POST['email'] ?? '');     
    $userData['password'] = $_POST['password'] ?? '';
    $userData['regDate'] = date('Y-m-d');

    //Obligatoriske felt som ikke kan være tomme.
    $obligFelt = ['firstName', 'lastName', 'email'];
        foreach($obligFelt as $felt) {
            if(empty($userData[$felt])) {
                $error[$felt] = ucfirst($felt) . " feltet må være fylt ut.<br>";
            }
        }
    
    if(strlen($userData['firstName']) > 55) {
        $error['firstName'] = "Fornavnet kan ikke være lenger enn 55 tegn.";
    }
    if(strlen($userData['lastName']) > 55) {
        $error['lastName'] = "Etternavnet kan ikke være lenger enn 55 tegn.";
    }
    
    //Validerer epost format via validerEmail funskjonen
    if(!isset($error['email'])) {
        $result = validateEmail($userData['email']);
        if(!$result['valid']) {
            $error['email'] = $result['message'];
        }
    }    
    
    //Validerer password fomat via validerpassword funksjonen
    if(!isset($error['password'])) {
        $result = validatePassword($userData['password']);
        if(!$result['valid']) {
            $error['password'] = $result['message'];
        }
    }

    //Sjekker om email finnes i databasen
    if(!isset($error['email'])) {
        $q = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $q->bindParam(':email', $userData['email']);
        $q->execute();

        $existingEmail = $q->fetchColumn();

        if ($existingEmail > 0) {
            $error['email'] = "Denne eposten er allerede registrert.";
        }
    }

    //Om error arrayen er tom så legges dataen i databasen
    if(empty($error)) {

        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

        //Forbereder SQL INSERT query til users tabellen.
        try {

            $pdo->beginTransaction();

            $q = $pdo->prepare(
                "INSERT INTO users(firstName, lastName, email, password_Hash, regDate)
                VALUES(:fname, :lname, :email, :passwordHash, :regDate)"
            );
             
            //Knytter SQL-parameterene med PHP userData matrisen med passende nøkler
            $q->bindParam(':fname', $userData['firstName']);
            $q->bindParam(':lname', $userData['lastName']);
            $q->bindParam(':email', $userData['email']);           
            $q->bindParam(':passwordHash', $passwordHash);
            $q->bindParam(':regDate', $userData['regDate']);
            $q->execute();
         
            $pdo->commit(); 

            $message = "<h1>Velkommen " . wash($userData['firstName']) . "!</h1>
            <h2>Du har registrert en ny konto!: </h2>";
            foreach($userData as $key => $value) { 
                if($key !== 'password') {
                    $message .= ucfirst($key) . ": " . wash($value) . "<br>";
                }
            }
        
        } catch(PDOException $e) {
            $pdo->rollBack();

            error_log('Tid: ' . date("Y-m-d H:i:s") . ' Database error: ' . $e->getMessage());           
            die("Beklager, det oppstod en feil ved lagring av data." . $e->getMessage());  //Gis til bruker uten info om logikk
        }            
        
    }
}
?>



<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registeringsside</title>
</head>
<body>

<li><a href="index.php">Tilbake til index</a></li><hr>

<?php if(!$message) echo "<h1>Lag en bruker her</h1>"; else echo "<div class='message'>$message</div><br>"; ?> 

    <form method="POST">

    <label for="fname">Fornavn</label><br>
    <input type="text" id="fname" name="fname" value="<?= wash($userData['firstName'] ?? '') ?>"><br>
    <?php if(isset($error['firstName'])) echo "<span class='error'>" . wash($error['firstName']) . "</span><br>"; ?>

    <label for="lname">Etternavn</label><br>
    <input type="text" id="lname" name="lname" value="<?= wash($userData['lastName'] ?? '') ?>"><br>
    <?php if(isset($error['lastName'])) echo "<span class='error'>" . wash($error['lastName']) . "</span><br>"; ?>

    <label for="email">Epost</label><br>
    <input type="text" id="email" name="email" value="<?= wash($userData['email'] ?? '') ?>"><br>
    <?php if(isset($error['email'])) echo "<span class='error'>" . wash($error['email']) . "</span><br>"; ?>

    <label for="password">Passord</label><br>
    <input type="password" id="password" name="password"><br>
    <?php if(isset($error['password'])) echo "<span class='error'>" . $error['password'] . "</span><br>"; ?>

        <button type="submit">Registrer</button> 

    </form> 

    <hr>
    <p>Har du allerede en konto? <a href="innlogging.php">Logg inn her</a>.</p>

</body>
</html>