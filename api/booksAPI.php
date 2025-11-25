<?php 
require_once __DIR__ . '/../classes/Books.php';

class GoogleBooksApi {

    //Basis linken til googleBooksAPI
    private $baseApiUrl = "https://www.googleapis.com/books/v1/volumes?q=";


    //Midlertidig funksjon for å gjerne vanlige filler ord i en query, returnerer den rensede queryen
    public function cleanQuery($query) {

        //Definerer vanlige "filler" ord.
        $fillerWords = ["kan", "du", "jeg", "vi", "om", "en", "ei", "et", "har", "bok", "bøker", "anbefale", "fortell", "meg", "noen", "som", "handler", "om"];
        $cleanQuery = preg_replace('/\b(' . implode('|', $fillerWords) . ')\b/i', '', $query);

        // Rens opp ekstra mellomrom
        $cleanQuery = trim(preg_replace('/\s+/', ' ', $cleanQuery));
        return $cleanQuery;
    
    }

    public function fetchBooks($query) {

        $response = file_get_contents($this->baseApiUrl . urlencode($query));
        if($response === FALSE) {
            throw new Exception("Kan ikke hente data fra Google Books");
        exit;
        }

        $bookData = json_decode($response, true);
        $books = [];

        if(isset($bookData['items'])) {
            foreach($bookData['items'] as $item) {
                $volumeInfo = $item['volumeInfo'];

                $books[] = new Books([
                    "bookID" => $item['id'] ?? null,
                    //"selfLink" => $item['selfLink'] ?? null, USIKKER OM VI HAR BRUKT FOR SELFLINK
                    "title" => $volumeInfo['title'] ?? 'Ukjent tittel',
                    "authors" => $volumeInfo['authors'][0] ?? 'Ukjent forfatter',
                    "description" => $volumeInfo['description'] ?? 'Ingen beskrivelse',
                    "pageCount" => $volumeInfo['pageCount'] ?? null,                
                    "thumbnail" => $volumeInfo['imageLinks']['thumbnail'] ?? null
                ]);
            }
        }
        
        return $books;
    }   

}
?>
