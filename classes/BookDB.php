<?php

require_once __DIR__ . '/../scripts/DB/db.inc.php';

class BookDB {
    
    private PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Setter boken inn i databasen dersom den ikke finnes fra før. Blir oppdatert om den gjør det. 
    public function insertBook($data) {        
        $q = $this->pdo->prepare( 
            "INSERT INTO books (bookID, title, authors, description, page_count, thumbnail) 
            VALUES (:bookID, :title, :authors, :description, :pageCount, :thumbnail)
            ON DUPLICATE KEY UPDATE 
                title       = VALUES(title),
                authors     = VALUES(authors),
                description = VALUES(description),
                page_count  = VALUES(page_count),
                thumbnail   = VALUES(thumbnail)"
        );
        $q->bindParam(':bookID', $data['bookID']); 
        $q->bindParam(':title', $data['title']);
        $q->bindParam(':authors', $data['authors']);
        $q->bindParam(':description', $data['description']);
        $q->bindParam(':pageCount', $data['pageCount']);
        $q->bindParam(':thumbnail', $data['thumbnail']);
        $q->execute();
    }

    //Knytter en bok til en bruker i user_books tabellen. Samme bruker kan ikke ha samme bok flere ganger.
    public function userAddBook($userID, $bookID) {
        $q = $this->pdo->prepare(
            "INSERT IGNORE INTO user_books(bookID, userID)
            VALUES(:bookID, :userID)"
        );    
        $q->bindparam(':userID', $userID);
        $q->bindparam(':bookID', $bookID);
        $q->execute();
    }

    //Fjerner en bok fra en brukers bokhylle. (Boken fjernes ikke fra den generelle books tabellen)
    public function userRemoveBook($userID, $bookID) {
        $q = $this->pdo->prepare(
            "DELETE FROM user_books 
            WHERE userID = :userID AND bookID = :bookID"
        );    
        $q->bindparam(':userID', $userID);
        $q->bindparam(':bookID', $bookID);
        $q->execute();
    }

    //Henter alle bøker knyttet til en bruker som Books objekter.
    public function userFetchAllBooks($userID) {
        $q = $this->pdo->prepare(
            "SELECT b.bookID, b.title, b.authors, b.description, b.page_count, b.thumbnail FROM books b
            INNER JOIN user_books ub ON  b.bookID = ub.bookID
            WHERE ub.userID = :userID"
        );
        $q->bindparam(':userID', $userID);   
        $q->execute();
        
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $bookObjects = [];

        foreach($rows as $row) {

            $data = [
                'bookID'      => $row['bookID'],
                'title'       => $row['title'],
                'authors'     => $row['authors'],
                'description' => $row['description'],
                'pageCount'   => $row['page_count'],                
                'thumbnail'   => $row['thumbnail']
            ];

            $bookObjects[] = new Books($data);
        }

        return $bookObjects;
    }
    
}
?>