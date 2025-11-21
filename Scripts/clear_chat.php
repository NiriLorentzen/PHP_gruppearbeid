<?php
    //starter opp en sesjon, om en ikke er fra før av
    if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    }

    //tømmer chatlog i sesjon
    unset($_SESSION["chatlog"]);
    unset($_SESSION["recommendations_given"]);
    unset($_SESSION["recommendations_found"]);

    echo "Chat reset.";
?>