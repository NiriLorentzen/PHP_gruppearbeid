<?php 

    function getBookRecommendation($bookQuery) {
        $apiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($bookQuery);


        $response = file_get_contents($apiUrl);
        if($response === FALSE) {
            return ["error" => "Kan ikke hente data fra Google Books"];
        }

        $bookData = json_decode($response, true); 

        $recommendations = []; 

        if (isset($bookData['items'])) {
        foreach ($bookData['items'] as $item) {
            $volumeInfo = $item['volumeInfo'];

            $recommendations[] = [
                "title" => $volumeInfo['title'] ?? 'Ukjent tittel',
                "authors" => $volumeInfo['authors'][0] ?? 'Ukjent forfatter',
                "description" => $volumeInfo['description'] ?? 'Ingen beskrivelse',
                "thumbnail" => $volumeInfo['imageLinks']['thumbnail'] ?? null
            ];
        }
    }

        return $recommendations;

    }
    
    header('Content-Type: application/json');
    echo json_encode(getBookRecommendation("science fiction"));
?>
