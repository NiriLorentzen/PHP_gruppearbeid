<?php 
require_once __DIR__ . '/sessionStart.php';
require_once __DIR__ . '/../classes/ChatManager.php';
require_once __DIR__ . '/DB/db.inc.php';

//Clearer chatten
$chatManager = new ChatManager($pdo);
$chatManager->clearChat();

//Unsetter nødvendige session variabler
unset($_SESSION['userID']);
unset($_SESSION['fornavn']);
unset($_SESSION['etternavn']);
unset($_SESSION['email']);
unset($_SESSION['roleID']);
unset($_SESSION['loggedIn']);

//Redirecter til logIn siden, med LoggedOut warning slik at beskjed blir gitt til brukeren.
header("Location: ../logIn.php?warning=loggedOut");
exit;
?>