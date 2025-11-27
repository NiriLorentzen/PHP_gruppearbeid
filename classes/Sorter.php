<?php 
//Klasse for å sortere alle mulige arrays. Behøver forhåndsdefinerte sorteringsmetoder og en form for å velge hvilken som skal brukes.
//Eksempel kan finnes i bookSortModes.php, Brukes i Bookshelf for sortering av bøker.
class Sorter {

    public static function sort(array $items, string $sortMode, array $modes, $default = null) {
        
        //Om default ikke er satt brukes først sortering modus.
        if($default === null) {
            $default = array_key_first($modes); 
        }

        //I tilfelle bruker endrer getbeskjed (modus) til en som ikke finnes, går til default
        if(!array_key_exists($sortMode, $modes)) {
            $sortMode = $default;
        }
        
        usort($items, $modes[$sortMode]);
        return $items;
    }

}





?>