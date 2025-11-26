<?php
    //tømmer chatlog i sesjon, bare lokalt altså, i praksis lager den en ny og tom chat
    unset($_SESSION["recommendations_found"]);
    unset($_SESSION["recommendations_given"]);

    //header("location: ../user_chats.php");
?>