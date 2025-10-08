<?php 

//Api call to google books too fetch book information based on recommendation query
    function getBookRecommendation($bookQuery) {
        $apiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($bookQuery);

//Gets json info from the api. Checks if failed and returns error if false
        $response = file_get_contents($apiUrl);
        if($response === FALSE) {
            return ["error" => "Kan ikke hente data fra Google Books"];
        }

//Decodes json        
        $bookData = json_decode($response, true); 

//Sets data for each recommended book
        $recommendations = []; 

        if (isset($bookData['items'])) {
        foreach ($bookData['items'] as $item) {
            $volumeInfo = $item['volumeInfo'];

            $recommendations[] = [
                "title" => $volumeInfo['title'] ?? 'Ukjent tittel',
                "authors" => $volumeInfo['authors'][0] ?? 'Ukjent forfatter',
                "description" => $volumeInfo['description'] ?? 'Ingen beskrivelse',
                "pageCount" => $volumeInfo['pageCount'] ?? 'Ukjent mengde sider',                
                "thumbnail" => $volumeInfo['imageLinks']['thumbnail'] ?? null
            ];
        }
    }

        return $recommendations;
    }
    
    header('Content-Type: application/json');
    echo json_encode(getBookRecommendation("science fiction"));
?>
