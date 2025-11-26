<?php 

//Sjekker om bruker er innlogget
function checkLoggedIn() {
    return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
}

//Bruker checkLoggedIn til å finne innlogging status, hvis ikke sendes de til innlogging.php med beskjed.
function mustBeLoggedIn() {
    if(!checkLoggedIn()) {
        header("Location: logIn.php?warning=notLoggedIn");        
        exit;
    }
}

//Sjekker om bruker har rollen admin
function checkAdmin() {
    return isset($_SESSION['roleID']) && $_SESSION['roleID'] == 1;
}


//Bruker checkAdmin til å finne om bruker er admin, hvis ikke sendes de til innlogging.php med beskjed.
function mustBeAdmin() {
    if(!checkAdmin()) {
        header("Location: logIn.php?warning=wrongPrivileges");
        exit;
    }
}
?>