<?php 
require_once __DIR__ . '/../classes/Books.php';

class GoogleBooksApi {

    //Basis linken til googleBooksAPI
    private $baseApiUrl = "https://www.googleapis.com/books/v1/volumes?q=";
   
    //Henter bøker fra Google Books API basert på en søkestreng
    public function fetchBooks($query) {

        //Om den ikke får en valid response kaster den en feil
        $response = file_get_contents($this->baseApiUrl . urlencode($query));
        if($response === FALSE) {
            throw new Exception("Kan ikke hente data fra Google Books");
        exit;
        }


        $bookData = json_decode($response, true);
        $books = [];

        //Lager Books objekter utifra API data
        if(isset($bookData['items'])) {
            foreach($bookData['items'] as $item) {
                $volumeInfo = $item['volumeInfo'];

                $books[] = new Books([
                    "bookID" => $item['id'] ?? null,                    
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
