<?php 
include 'scripts/navbar.php';

$brukerinfo = [];

if(isset($_SESSION['userID']))


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
                <th>brukerid</th>
                <th>fornavn</th>
                <th>etternavn</th>
                <th>email</th>
                <?php if($_SESSION['roleID'] == 1): ?>
                    <th>Brukertype</th>
                <?php endif; ?>
            </tr>
            <tr>
                <td><?php echo $_SESSION['userID'] ?></td>
                <td><?php echo $_SESSION['fornavn'] ?></td>
                <td><?php echo $_SESSION['etternavn'] ?></td>
                <td><?php echo $_SESSION['email'] ?></td>
                <?php if($_SESSION['roleID'] == 1): ?>
                    <td>Admin</td>
                <?php endif; ?>
            </tr>
        </table>
    <?php endif; ?>
</body>
</html>