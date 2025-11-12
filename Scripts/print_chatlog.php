<?php
    //henter parsedown bibliotek for pen utskrift/formatering
    require_once __DIR__ . '/../libs/Parsedown.php';

    function printchatlog(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $parsedown = new Parsedown();
        if (isset($_SESSION['chatlog'])){
            $first_element = True;
            foreach($_SESSION['chatlog'] as $chatdel_index => $chatdel) {
                if($first_element) { //første element er alltid gemini start-prompten, "du er bibliotektar som ... osv", skal ikke vises til bruker
                    $first_element = False;
                } elseif($chatdel_index % 2) { //tar annenhver, gjør brukerspørsmål blå og gemini svar grå
                    echo "<div class='chat-element' style='background-color: lightblue; align-self: flex-end; '>" . $parsedown->text(nl2br(htmlspecialchars($chatdel))) . "</div>";
                } else {
                    echo "<div class='chat-element' style='background-color: lightgrey; align-self: flex-start; '>" . $parsedown->text(nl2br(htmlspecialchars($chatdel))) . "</div>";
                }
            }
        }
    }

?>