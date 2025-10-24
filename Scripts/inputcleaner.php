<?php
    function input_cleaner($input) {
        return htmlspecialchars(stripslashes(trim($input)));
    }
?>