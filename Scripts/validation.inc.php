<?php

//Validerer Epost via FILTER_VALIDATE_EMAIL, returnerer beskjed om gyldig eller ei.
function validateEmail($email) {
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => true, 'melding' =>  "Epost-adressen er gyldig."];
    } else {
        return ['valid' => false, 'melding' => "Epost-adressen er ugyldig: Den må ha formatet navn@domene.no"];
    }
}

//KANSKJE UNØDVENDIG
//Validerer norske telefon nummere, ser bort fra landskode., må være 8 tall langt. Må starte med 4 eller 9
function validatePhone($tlfNum) {
    $errors = [];
    $tlfNum = preg_replace('/\s+|-/', '', $tlfNum);
    
    
    if(!preg_match('/^[0-9]{8}$/', $tlfNum)) {
        $errors[] = "Telefonnummeret må være 8 sifere";
    }

    if(!preg_match('/^[49]/', $tlfNum)) {
        $errors[] = "Telefonnummeret må starte med 4 eller 9";
    }

    //Om error matrisen er tom returnerer gyldig, om ikke returnerer ugyldig med aktuelle feilmeldinger. 
    if(empty($errors)) {
        return ['valid' => true, 'melding' => "Telefonnummeret er gyldig."];
    } else {
        return ['valid' => false, 'melding' => "Telefonnummeret er ugyldig: " . implode(", ", $errors) . "."];
    }
}


//Validerer passord med krav på minimum 9 tegn, en stor bokstav, ett spesialtegn og to tall
function validatePassword($password) {
    $errors = [];

    //Legger til error i tilhørende matrise om ikke inneholder minimum 9 tegn
    if(strlen($password) < 9){
        $errors[] = "Passordet må være minimum 9 tegn";
    }
    //Legger til error i tilhørende matrise om ikke inneholder en stor bokstav inkludert ÆØÅ
    if(!preg_match('/[A-ZÆØÅ]/u', $password)) {
        $errors[] = "Passordet må ha minst en stor bokstav";
    } 
    //Legger til error i tilhørende matrise om ikke inneholder ett spesialtegn
    if(!preg_match('/[\W_]/u', $password)) {
        $errors[] = "Passordet må ha minst ett spesialtegn";
    }
    //Legger til error i tilhørende matrise om ikke inneholder minst to tall
    $numCount = preg_match_all('/\d/u', $password);
    if($numCount < 2) {
        $errors[] = "Passordet må ha minst to tall";
    }

    //Returnerer gyldig om error matrisen er tom, eller ugyldig med error beskjedene om den ikke er tom  
    if (empty($errors)) {            
        return ['valid' => true];
    } else {            
        return ['valid' => false, 'melding' => "Passordet har mangler: " . implode(", ", $errors) . "."];
    }        

}

?>