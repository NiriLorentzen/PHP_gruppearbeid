<?php
    function findrecommendation($response){
        if (!session_status() == PHP_SESSION_ACTIVE){
            session_start();
        }
        // Oppretter om det ikke er en fra før av
        if (!isset($_SESSION["recommendations_given"])) {
            $_SESSION["recommendations_given"] = array(); //chatsamtalen er en array som blir appenda til for hver respons/input
        } 

        //oppretter et mønster
        $pattern = '/\d+\.\s*\*{0,2}["“”]?(.*?)["“”]?\*{0,2}\s+av\s+([^:]+):/';
        
        //bruker mønsteret for å hente ut mulig bok informasjon ifra gemini-respons
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
        }
        
        print_r($_SESSION["recommendations_given"]);
    }
?>