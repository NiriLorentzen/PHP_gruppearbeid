<?php 
include __DIR__ . '/scripts/navbar.php';
require_once __DIR__ . '/scripts/checkLoginStatus.php';
mustBeLoggedIn();
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brukerside</title>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
    <h1>Brukerside</h1>
    <?php if(isset($_SESSION['userID'])): ?>
        <table>
            <tr>
                <th>BrukerID</th>
                <th>Fornavn</th>
                <th>Etternavn</th>
                <th>Email</th>
                <?php if(checkAdmin()): //ikke vits å vise brukertype til en normal bruker?>
                    <th>Brukertype</th>
                <?php endif; ?>
            </tr>
            <tr>
                <td><?php echo $_SESSION['userID'] ?></td>
                <td><?php echo $_SESSION['fornavn'] ?></td>
                <td><?php echo $_SESSION['etternavn'] ?></td>
                <td><?php echo $_SESSION['email'] ?></td>
                <?php if(checkAdmin()): ?>
                    <td>Admin</td>
                <?php endif; ?>
            </tr>
        </table><br>
        <form action="Scripts/userDelete.php" method="post" onsubmit="return confirm('Er du sikker på at du vil slette brukeren?');">
            <button type="submit">Slett bruker</button>
        </form>
    <?php endif; ?>
</body>
</html>