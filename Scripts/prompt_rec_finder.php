<?php
    function findrecommendation($response){
        session_start();
        // Oppretter om det ikke er en fra før av
        if (!isset($_SESSION["recommendations_given"])) {
            $_SESSION["recommendations_given"] = array(); //chatsamtalen er en array som blir appenda til for hver respons/input
        } 
        $pattern = '/\d+\.\s*\*{0,2}["“”]?(.*?)["“”]?\*{0,2}\s+av\s+([^:]+):/';
        preg_match_all($pattern, $response, $matches);
        $books = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $books[] = [
                'title'  => trim($matches[1][$i]),
                'author' => trim($matches[2][$i])
            ];
        }
        if (isset($books)){
            foreach($books as $bok){
                $_SESSION["recommendations_given"][] = $bok;
            }
        }
        print_r($_SESSION["recommendations_given"]);
    }
?>