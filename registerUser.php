<?php 
include __DIR__ . '/scripts/DB/db.inc.php';
include __DIR__ . '/scripts/validation.inc.php';
include __DIR__ . '/scripts/sanitizeInputs.inc.php';
include 'scripts/navbar.php';

const USER_ROLE = 2;
$userData = [];
$error = [];
$success = false;
    
    
//Fyller $userData matrisen
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $userData['firstName'] = sanitizeInputs($_POST['fname'] ?? '');
    $userData['lastName'] = sanitizeInputs($_POST['lname'] ?? '');    
    $userData['email'] = sanitizeInputs($_POST['email'] ?? '');     
    $userData['password'] = $_POST['password'] ?? '';
    $userData['regDate'] = date('Y-m-d');

    //Obligatoriske felt som ikke kan være tomme.
    $reqField = ['firstName', 'lastName', 'email', 'password'];
        foreach($reqField as $field) {
            if(empty($userData[$field])) {
                $fieldNames = ['firstName' => 'Fornavn', 'lastName' => 'Etternavn', 'email' => 'Epost', 'password' => 'Passord'];
                $error[$field] = $fieldNames[$field] . " feltet må være fylt ut.";
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
                "INSERT INTO users(first_name, last_name, email, password_hash, reg_date)
                VALUES(:fname, :lname, :email, :passwordHash, :regDate)"
            );
             
            //Knytter SQL-parameterene med PHP userData matrisen med passende nøkler
            $q->bindParam(':fname', $userData['firstName']);
            $q->bindParam(':lname', $userData['lastName']);
            $q->bindParam(':email', $userData['email']);           
            $q->bindParam(':passwordHash', $passwordHash);
            $q->bindParam(':regDate', $userData['regDate']);
            $q->execute();

            $userID = $pdo->lastInsertId();

            $roleID = USER_ROLE;
            $qRole = $pdo->prepare("INSERT INTO user_roles(userID, roleID) VALUES (:userID, :roleID)");
            $qRole->bindParam(':userID', $userID);
            $qRole->bindParam(':roleID', $roleID);
            $qRole->execute();
         
            $pdo->commit(); 
            $success = true;

        } catch(PDOException $e) {
            $pdo->rollBack();

            error_log('Tid: ' . date("Y-m-d H:i:s") . ' Database error: ' . $e->getMessage());           
            die("Beklager, det oppstod en feil ved lagring av data.");  //Gis til bruker uten info om logikk.
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
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>

<?php if($success): ?>
    <div>
        <h1>Velkommen, <?= sanitizeInputs($userData['firstName']) ?>!</h1>
        <p>Du har registrert en ny konto, med eposten <?= sanitizeInputs($userData['email'])?>.</p>
        <p><a href="logIn.php">Klikk her for å logge inn.</a></p>
    </div>
<?php else: ?>

    <h1>Lag en bruker her</h1> 

    <form method="POST">

    <label for="fname">Fornavn</label><br>
    <input type="text" id="fname" name="fname" value="<?= sanitizeInputs($userData['firstName'] ?? '') ?>"><br>
    <?php if(isset($error['firstName'])) echo "<span class='error'>" . sanitizeInputs($error['firstName']) . "</span><br>"; ?>

    <label for="lname">Etternavn</label><br>
    <input type="text" id="lname" name="lname" value="<?= sanitizeInputs($userData['lastName'] ?? '') ?>"><br>
    <?php if(isset($error['lastName'])) echo "<span class='error'>" . sanitizeInputs($error['lastName']) . "</span><br>"; ?>

    <label for="email">Epost</label><br>
    <input type="text" id="email" name="email" value="<?= sanitizeInputs($userData['email'] ?? '') ?>"><br>
    <?php if(isset($error['email'])) echo "<span class='error'>" . sanitizeInputs($error['email']) . "</span><br>"; ?>

    <label for="password">Passord</label><br>
    <input type="password" id="password" name="password"><br>
    <?php if(isset($error['password'])) echo "<span class='error'>" . $error['password'] . "</span><br>"; ?>

        <button type="submit">Registrer</button> 

    </form> 

    <hr>
    <p>Har du allerede en konto? <a href="logIn.php">Logg inn her</a>.</p>
<?php endif; ?>

</body>
</html>