<?php

    require_once __DIR__ . '/../api/booksAPI.php';

    //får inn en individuell anbefaling ifra gemini i forfatter, tittel format
     //eks: [title] => Mistborn: The Final Empire [author] => Brandon Sanderson 
    function GeminiTilGoogle(){
        // Oppretter om det ikke er en fra før av
        if (!isset($_SESSION["Recommendations_found"])) {
            $_SESSION["Recommendations_found"] = array(); 
        } 

        $GeminiRecommendations = $_SESSION["recommendations_given"];
        foreach($GeminiRecommendations as $title => $author)
            $recommendation = $title . $author;
            $api = new GoogleBooksApi();
            $recommendations = $api->fetchBooks($recommendation);


        $_SESSION["Recommendations_found"][] = $recommendations;
    }


?>