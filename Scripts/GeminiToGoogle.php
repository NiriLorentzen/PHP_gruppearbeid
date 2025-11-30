<?php

    require_once __DIR__ . '/../api/booksAPI.php';
    require_once __DIR__ . '/../classes/Books.php';

    function geminiToGoogle(){
        /*
        Denne funksjonen skal ta i bruk informasjonen funnet av promptRecFinder, som er lagret i recommendations_found i sesjonen. 
        Denne informasjonen ligger i array med mulig bok anbefalinger ifra gemini. 
        Bok informasjonen skal fores inn i googlebooksapi og resultatet skal vises til brukeren. 
        Svaret ifra googlebooksapi er det i DB-en til google som passer tittel og forfatter navn best, noen ganger blir uansett resultatet ifra api-en ikke helt lik selv om promptRecFinder finner riktig gemini anbefaling, pga. diverse grunner (gemini problemer, lignende navn, oppfunnede bøker, osv.). 
        */
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        //sett session-variabel hvis en ikke er satt fra før
        if (!isset($_SESSION["recommendations_found"])) {
            $_SESSION["recommendations_found"] = array(); 
        } 

        //hent informasjonen funnet av preg_match_all i promptRecFinder, altså de bøkene som trolig er anbefalt av gemini, er ikke absolutt alltid riktig
        $GeminiRecommendations = $_SESSION["recommendations_given"] ?? [];
        
        // tøm anbefalingene gitt, slikt at vi ikke søker på de samme anbefalingene flere ganger
        $_SESSION["recommendations_given"] = array();

        //ny instans av klassen som har metoden vi trenger for søk 
        $api = new GoogleBooksApi();

        // Loop gjennom alle bøkene (eks. ['title' => 'Mistborn', 'author' => 'Sanderson'])
        foreach($GeminiRecommendations as $individual_book){
            // sjekk at array ikke mangler noe eller er tom
            if (isset($individual_book['title']) && isset($individual_book['author'])) {
                $title = $individual_book['title'];
                $author = $individual_book['author'];

                // søk query for googlebooksapi
                $recommendation_query = $title . " " . $author;
                
                // søker
                $recommendations = $api->fetchBooks($recommendation_query);

                //hvis vi har fått svar
                if (!empty($recommendations)) {
                    //tar bare det første og dermed mest relevante svaret
                    $_SESSION["recommendations_found"][] = $recommendations[0];
                }
            }
        }
    }
?>