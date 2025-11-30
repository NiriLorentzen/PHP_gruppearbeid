<?php
    //hente funksjon 
    require_once __DIR__ . '/../Scripts/geminiToGoogle.php';

    function findrecommendation($response){
        if (!session_status() == PHP_SESSION_ACTIVE){
            session_start();
        }

        // Oppretter om det ikke er en fra før av
        if (!isset($_SESSION["recommendations_given"])) {
            $_SESSION["recommendations_given"] = array(); //chatsamtalen er en array som blir appenda til for hver respons/input
        } 

        /*oppretter et mønster
        flere mønster kan brukes for å øke sjansen for å finne bøkene som gemini anbefaler, men her holder vi oss til en enn så lenge
        gemini sin respons er ikke alltid lik, og å gi et mønster til gemini i prompt-en har gitt oss blandet resultat, så dette funker som oftest bra*/
        $pattern = '/\d+\.\s*\*{0,2}["“”]?(.*?)["“”]?\*{0,2}\s+av\s+([^:]+):/';
        
        //bruker mønsteret for å hente ut mulig bok informasjon ifra gemini-respons, dette skal helst være bare: tittel og forfatter
        preg_match_all($pattern, $response, $matches);

        //tomt array for anbefalingene
        $books = [];

        //går gjennom mønster-søk resultatet og legger det i books-array
        for ($i = 0; $i < count($matches[1]); $i++) {
            $books[] = [
                'title'  => trim($matches[1][$i]),
                'author' => trim($matches[2][$i])
            ];
        }

        //dersom det er bokanbefalinger funnet, legges det til i sesjonens 'recommendations_given' array
        if (isset($books)){
            foreach($books as $bok){
                $_SESSION["recommendations_given"][] = $bok;
            }
        } else{
            //for feilsøking
            $_SESSION["chat-errors"][] = "Ingen bok anbefalinger funnet fra gemini-svaret";
        }

        //funksjon i geminitogoogle scriptet som forer mønster-søk resultatet inn til google books
        geminiToGoogle();
    }
?>