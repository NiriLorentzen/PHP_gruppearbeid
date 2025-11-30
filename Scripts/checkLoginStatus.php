<?php 

//Sjekker om bruker er innlogget, returnerer bool;
function checkLoggedIn() {
    return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
}

/*Bruker checkLoggedIn til å finne innlogging status, hvis ikke sendes de til innlogging.php med beskjed.
Tas i bruk om bruker ikke er logget inn og dermed skal kastes ut av en side de ikke er ment til å kunne besøke */
function mustBeLoggedIn() {
    if(!checkLoggedIn()) {
        header("Location: logIn.php?warning=notLoggedIn");        
        exit;
    }
}

//Sjekker om bruker har rollen admin, returnerer bool;
function checkAdmin() {
    return isset($_SESSION['roleID']) && $_SESSION['roleID'] == 1;
}


/*Bruker checkAdmin til å finne om bruker er admin, hvis ikke sendes de til innlogging.php med beskjed.
Tas i bruk om bruker skal bli kastet ut av en side de ikke er ment til å kunne besøke på grunn av rolle*/
function mustBeAdmin() {
    if(!checkAdmin()) {
        header("Location: logIn.php?warning=wrongPrivileges");
        exit;
    }
}
?>