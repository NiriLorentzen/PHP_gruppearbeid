<?php
    function findrecommendation($response){
        $patterns = [
            '/**"?([^"]+)"?**\s+by\s+([^.]+)./i', 
        ];
    }

    function extractBooks($text) {
    $pattern = '/**"?([^"]+)"?**\s+by\s+([^.]+)./i';
    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

    $books = [];
    foreach ($matches as $match) {
        $books[] = [
            'title' => trim($match[1]),
            'author' => trim($match[2])
        ];
    }
    return $books;
}
?>