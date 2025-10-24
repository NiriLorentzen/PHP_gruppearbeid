<?php
    session_start();
    unset($_SESSION["chatlog"]);
    echo "Chat reset.";
?>