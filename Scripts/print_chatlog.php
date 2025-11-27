<?php
    //henter parsedown bibliotek for pen utskrift/formatering
    require_once __DIR__ . '/../libs/Parsedown.php';

    function printchatlog(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $parsedown = new Parsedown();
        if (isset($_SESSION['active-chatlog'])){
            $first_element = True;
            foreach($_SESSION['active-chatlog'] as $chatdel_index => $chatdel) {
                // Skip if this element is somehow still an array
                if(is_array($chatdel)) {
                    $_SESSION["chat-errors"][] = "FEIL: Element $chatdel_index er fortsatt et array!";
                    continue;
                }

                if($first_element) { //første element er alltid gemini start-prompten, "du er bibliotektar som ... osv", skal ikke vises til bruker
                    $first_element = False;
                } elseif($chatdel_index % 2) { //tar annenhver, gjør brukerspørsmål blå og gemini svar grå
                    echo "<div class='chat-element' style='background-color: lightblue; align-self: flex-end; '>" . $parsedown->text(nl2br(htmlspecialchars($chatdel))) . "</div>";
                } else {
                    echo "<div class='chat-element' style='background-color: lightgrey; align-self: flex-start; '>" . $parsedown->text(nl2br(htmlspecialchars($chatdel))) . "</div>";
                }
                $_SESSION["chat-errors"][] = "utskrift-element: " . $chatdel;
            }
        }
    }

?>