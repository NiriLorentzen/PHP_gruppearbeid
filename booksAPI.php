<?php 

if (!isset($_GET['q'])) {
    echo json_encode(["error" => "Ingen søk oppgitt"]);
    exit;
}

//Api kall til googlebooks api for å hente ifnromasjon basert på  brukers søkeord
$bookQuery = $_GET['q'];
$apiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($bookQuery);

//Henter json info om bøker fra api. Sjekker om det feilet og gir error om FALSE
    $response = file_get_contents($apiUrl);
    if($response === FALSE) {
        echo json_encode(["error" => "Kan ikke hente data fra Google Books"]);
        exit;
    }

//Decoder json        
    $bookData = json_decode($response, true); 
    
//Setter informasjonen til hver anbefalte bok    
    $recommendations = []; 
    
    if (isset($bookData['items'])) {
        foreach ($bookData['items'] as $item) {
            $volumeInfo = $item['volumeInfo'];

            $recommendations[] = [
                "title" => $volumeInfo['title'] ?? 'Ukjent tittel',
                "authors" => $volumeInfo['authors'][0] ?? 'Ukjent forfatter',
                "description" => $volumeInfo['description'] ?? 'Ingen beskrivelse',
                "pageCount" => $volumeInfo['pageCount'] ?? 'Ukjent side antall',                
                "thumbnail" => $volumeInfo['imageLinks']['thumbnail'] ?? null
            ];
        }
    }

//Returnerer recommendation json
    header('Content-Type: application/json');
    echo json_encode($recommendations);
?>
