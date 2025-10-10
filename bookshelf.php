<?php 

    class Books {

        private $title;
        private $authors;
        private $description;
        private $pageCount;
        private $thumbnail;


        public function __construct($title, $author, $description)
        {
            $this->setTitle($title);
            $this->setAuthors($author);
            $this->setDescription($description);
        }

//Setter og Getter til en boks tittel
        public function setTitle($newTitle) {
            $this->title = $newTitle;
        }
        public function getTitle() {
            return $this->title;
        }
//Setter og Getter til en boks forfatter
        public function setAuthors($newAuthors) {
            $this->authors = $newAuthors;
        }
        public function getAuthors() {
            return $this->authors;
        }
//Setter og Getter til en boks beskrivelse
        public function setDescription($newDescription) {
            $this->description = $newDescription;
        }
        public function getDescription() {
            return $this->description;
        }
//Setter og Getter til en boks antall sider
        public function setPageCount($newPageCount) {
            $this->pageCount = $newPageCount;
        }
        public function getPageCount() {
            return $this->pageCount;
        }
//Setter og Getter til en boks thumbnail
        public function setThumbnail($newThumbnail) {
            $this->thumbnail = $newThumbnail;
        }
        public function getThumbnail() {
            return $this->thumbnail;
        }
    }

    
?>


<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bokhylle</title>
</head>
<body>
    
</body>
</html>