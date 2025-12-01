<?php 

class Books {

    private $bookID;
    private $title;
    private $authors;
    private $description;
    private $pageCount;
    private $thumbnail;
    private $haveRead;

    public function __construct(array $data =[])
    {      
        $this->setBookId($data['bookID'] ?? null);
        $this->setTitle($data['title'] ?? 'Ukjent tittel');
        $this->setAuthors($data['authors'] ?? 'Ukjent forfatter');
        $this->setDescription($data['description'] ?? 'Ingen beskrivelse');
        $this->setPageCount(!empty($data['pageCount']) ? $data['pageCount'] : 'Ukjent side antall');
        $this->setThumbnail($data['thumbnail'] ?? null);        
    }
    
    public function setBookId($newBookID) {
        $this->bookID = $newBookID;
    }

    public function getBookId() {
        return $this->bookID;
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