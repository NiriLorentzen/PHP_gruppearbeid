<?php 

    class Books {

        private $title;
        private $authors;
        private $description;
        private $pageCount;
        private $thumbnail;
        private $haveRead;


        public function __construct($title, $author, $description)
        {
            $this->setTitle($title);
            $this->setAuthors($author);
            $this->setDescription($description);
            $this->haveRead = false;
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

//Endrer og getter til HaveRead        
        public function changeHaveRead() {
            $this->haveRead = !$this->haveRead;
        }
        public function getHaveRead() {
            return $this->haveRead;
        }
    }
    

?>