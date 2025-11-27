<?php
    //starter opp en sesjon, om en ikke er fra før av
    if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    }

    //tømmer chatlog i sesjon, bare lokalt altså, i praksis lager den en ny og tom chat
    unset($_SESSION["active-chatlog"]);
    unset($_SESSION["active-chatlog-id"]);
    unset($_SESSION["recommendations_given"]);
    unset($_SESSION["recommendations_found"]);
    unset($_SESSION["recommendations_found"]);

    unset($_SESSION["chat-errors"]);
    //echo "Chat reset.";
?>