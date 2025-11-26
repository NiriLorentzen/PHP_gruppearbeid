<?php

//Validerer Epost via FILTER_VALIDATE_EMAIL, returnerer beskjed om gyldig eller ei.
function validateEmail($email) {
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => true, 'message' =>  "Epost-adressen er gyldig."];
    } else {
        return ['valid' => false, 'message' => "Epost-adressen er ugyldig: Den må ha formatet navn@domene.no"];
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
    if(empty($errors)) {            
        return ['valid' => true];
    } else {            
        return ['valid' => false, 'message' => "Passordet har mangler: " . implode(", ", $errors) . "."];
    }        

}

?>