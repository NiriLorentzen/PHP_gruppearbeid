<?php 

//Sjekker om bruker er innlogget, tar ikke stilling til rolle. Kan utvides om flere roller blir introdusert
function checkLoggedIn() {
    if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
        header("Location: logIn.php?warning=notLoggedIn");
        exit;
    }
}

//Sjekker om bruker har rollen admin, hvis ikke sendes de til innlogging.php med beskjed.
function checkAdmin() {
    if(!isset($_SESSION['roleID']) || $_SESSION['roleID'] != 1) {
        header("Location: logIn.php?warning=wrongPrivileges");
        exit;
    }
}
?>