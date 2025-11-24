<?php

include __DIR__ . '/scripts/DB/db.inc.php';

class BookDB {
    
    private PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function insertBook($data) {

        $q = $this->pdo->prepare( //insert ignore sørger for at flere brukere kan sette inn samme bok, og at bøkene oppdaterer seg i database om mulig
            "INSERT IGNORE INTO books(bookID, book_name, author, book_description, page_count) 
            VALUES(:bookID, :book_name, :author, book_description, page_count)"
        );
        $q->bindParam(':bookID', $data['bookID']); 
        $q->bindParam(':book_name', $data['book_name']);
        $q->bindParam(':author', $data['author']);
        $q->bindParam(':book_description', $data['book_description']);
        $q->bindParam(':page_count', $data['page_count']);
        $q->execute();
    }

    public function userAddBook($userID, $bookID) {
        $q = $this->pdo->prepare(
            "INSERT IGNORE INTO user_books(bookID, userID)
            VALUES(:bookID, :userID)"
        );    
        $q->bindparam(':userID', $userID);
        $q->bindparam(':bookID', $bookID);
        $q->execute();
    }

    public function userRemoveBook($userID, $bookID) {
        $q = $this->pdo->prepare(
            "DELETE FROM user_books 
            WHERE userID AND bookID(:userID, :bookID)"
        );    
        $q->bindparam(':userID', $userID);
        $q->bindparam(':bookID', $bookID);
        $q->execute();
    }
}
?>