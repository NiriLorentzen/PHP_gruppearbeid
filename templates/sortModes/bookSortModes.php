<?php

/*Sorteringsmetoder for bøker i bokhyllen.
Bruker strcasecmp for å sammenligne strings, svarer med -1, 0 eller 1 om en er større enn den andre.
Tar i bruk ascii-verdier og ignorerer case ved sammenligning. */
$bookSortModes = [

    'title_asc' => function($bookA, $bookB) {
        return strcasecmp($bookA->getTitle(), $bookB->getTitle());
    },

    'title_desc' => function($bookA, $bookB) {
        return strcasecmp($bookB->getTitle(), $bookA->getTitle());
    },

    'author_asc' => function($bookA, $bookB) {
        return strcasecmp($bookA->getAuthors(), $bookB->getAuthors());
    },

    'author_desc' => function($bookA, $bookB) {
        return strcasecmp($bookB->getAuthors(), $bookA->getAuthors());
    },

    'pages_asc' => function($bookA, $bookB) {
        return ((int)$bookA->getPageCount()) <=> ((int)$bookB->getPageCount());
    },

    'pages_desc' => function($bookA, $bookB) {
        return ((int)$bookB->getPageCount()) <=> ((int)$bookA->getPageCount());
    }
];

