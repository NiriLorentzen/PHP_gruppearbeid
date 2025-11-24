<?php 
session_start();
include __DIR__ . '/scripts/DB/db.inc.php';


if(!$input) {
    //GJØR EN ERROR KODE ELLERNO HER
    exit;
}

try {
    $pdo->beginTransaction();

    $qIns = $pdo->prepare( //insert ignore sørger for at flere brukere kan sette inn samme bok, og at bøkene oppdaterer seg i database om mulig
        "INSERT IGNORE INTO books(bookID, book_name, author, book_description, page_count) 
        VALUES(:bookID, :book_name, :author, book_description, page_count)"
    );
    $qInsBook->bindParam(':bookID'); //HVOR KOMMER DATAEN FRA
    $qIns->bindParam(':book_name');
    $qIns->bindParam(':author');
    $qIns->bindParam(':book_description');
    $qIns->bindParam(':page_count');
    $qIns->execute();

    $bookID = $pdo->lastInsertId();

    $qInsUserBook = $pdo->prepare(
        "INSERT INTO user_books(bookID, userID)
        VALUES(:bookID, :userID)"
    );
    $qInsUserBook->bindparam(':bookID', $bookID);
    $qInsUserBook->bindparam(':userID', ); //PUTT INN USER ID


    $pdo->commit();

} catch(PDOException $e) {
    $pdo->rollBack();

    error_log('Tid: ' . date("Y-m-d H:i:s") . ' Database error: ' . $e->getMessage());           
    die("Beklager, det oppstod en feil ved lagring av data."); //Gis til bruker uten info om logikk.

}


?>