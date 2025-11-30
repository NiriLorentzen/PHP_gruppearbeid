<?php 
//Vasker input-data og gjør det til UTF-8
function sanitizeInputs($data) 
{
    $data = trim($data);    
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>