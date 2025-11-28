<?php

    require_once __DIR__ . '/../api/booksAPI.php';
    require_once __DIR__ . '/../classes/Books.php';

    function geminiToGoogle(){
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Initialize session array if it doesn't exist
        if (!isset($_SESSION["recommendations_found"])) {
            $_SESSION["recommendations_found"] = array(); 
        } 

        // Get the recommendations parsed from Gemini's response
        $GeminiRecommendations = $_SESSION["recommendations_given"] ?? [];
        
        // IMPORTANT: Clear the "given" list so we don't re-fetch them next time
        $_SESSION["recommendations_given"] = array();

        $api = new GoogleBooksApi();

        // === THIS IS THE CORRECTED LOOP ===
        // Loop through each book array (e.g., ['title' => 'Mistborn', 'author' => 'Sanderson'])
        foreach($GeminiRecommendations as $individual_book){
            
            // Make sure we have the data we need
            if (isset($individual_book['title']) && isset($individual_book['author'])) {
                
                $title = $individual_book['title'];
                $author = $individual_book['author'];

                // Create a search query for the Google Books API
                $recommendation_query = $title . " " . $author;
                
                // $recommendations is an array of Book objects
                $recommendations = $api->fetchBooks($recommendation_query);

                // We only want to add the FIRST and most relevant match
                if (!empty($recommendations)) {
                    // Add the *first* Book object from the results
                    // This matches what your index.php foreach loop expects
                    $_SESSION["recommendations_found"][] = $recommendations[0];
                    //header("refresh: 0;");
                }
            }
        }
    }
?>