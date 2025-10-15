<?php
//Henter gemini-api-key ifra config.php
//dette gjøres slikt at config.php kan være i gitignore, 
//for å minske sjansen at api nøkkelen blir lagt ut på github med uhell
require_once __DIR__ . '/../scripts/config.php';

//starter opp en session
session_start();

// Bytter navnet på api-nøkkelen, for bedre oversikt
$apiKey = $Gemini_API_key;

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

// Henter JSON fra JS fetch, henter input 
$input = json_decode(file_get_contents("php://input"), true);
$initialprompt = $input['prompt'] ?? 'Hello Gemini!';

//Legger til en start på gemini-prompten, som gir rammer for hvordan gemini skal svare og hva som er relevant for den å svare på
$promptmaker = "Imagine you're a formal librarian expert at work where your job is to reccomend and find books tailered to the visitors of your library called The BookFinder. You're respons is only supposed to be about the subject of books or book preference. If the visitor mentions a specific subject they want books from then give them books reccomendations as a list of 5. Only give follow-up questions if really needed. A visitor enters the library and starts a conversation with you, heres the correspondence: ";

// Oppretter en chatsamtale om det ikke er en fra før av
if (!isset($_SESSION["chatsamtale"])) {
    $_SESSION["chatsamtale"] = array($promptmaker); //chatsamtalen er en array som blir appenda til for hver respons/input
} 

// Legger til siste delen av samtalen
$_SESSION['chatsamtale'][] = $initialprompt;

//her bestemmes prompten som blir sendt til gemini
//her tas inn hele 'chatsamtale' og imploder arrayet slikt at gemini forstår samtalen, og det sendes som en lang string
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => implode("Question/Response: ", $_SESSION['chatsamtale'])]
            ]
        ]
    ]
];

//Gemini api-kalling
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "X-goog-api-key: $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

// Print gemini respons, sjekker først om det har kommet en respons
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    //legger til gemini respons i 'chatsamtale'
    $_SESSION['chatsamtale'][] = $result['candidates'][0]['content']['parts'][0]['text'];
    
    $første_element = True;
    foreach($_SESSION['chatsamtale'] as $chatdel_index => $chatdel) {
        if($første_element) { //første element er alltid gemini start-prompten, "du er bibliotektar som ... osv", skal ikke vises til bruker
            $første_element = False;
        } elseif($chatdel_index % 2) { //tar annenhver, gjør brukerspørsmål blå og gemini svar grå
            echo "<p class='chat-element' style='background-color: lightblue; align-self: flex-end; '>" . nl2br(htmlspecialchars($chatdel)) . "</p>";
        } else {
            echo "<p class='chat-element' style='background-color: lightgrey; align-self: flex-start; >" . nl2br(htmlspecialchars($chatdel)) . "</p>";
        }
    }
} else { //hvis det er en feil, print alt for debug
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
?>