<?php 

class Books {

    private $bookId;
    private $title;
    private $authors;
    private $description;
    private $pageCount;
    private $thumbnail;
    private $haveRead;

    public function __construct(array $data =[])
    {
        //Lager bokID - kanskje endre på dette når vi lager database
        $this->bookId = $data['id'] ?? uniqid('book_', true);
        $this->setTitle($data['title'] ?? 'Ukjent tittel');
        $this->setAuthors($data['authors'] ?? 'Ukjent forfatter');
        $this->setDescription($data['description'] ?? 'Ingen beskrivelse');
        $this->setpageCount($data['pageCount'] ?? null);
        $this->setThumbnail($data['thumbnail'] ?? null);
        $this->haveRead = false;
    }
    
    public function getBookId() {
        return $this->bookId;
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