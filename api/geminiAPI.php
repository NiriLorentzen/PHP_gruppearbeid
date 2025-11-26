<?php
//set encoding for at gemini-respons skal fungere riktig, at php ikke skal fjerne deler av den i $session
ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=utf-8');


//Henter gemini-api-key ifra config.php
//dette gjøres slikt at config.php kan være i gitignore, 
//for å minske sjansen at api nøkkelen blir lagt ut på github med uhell
require_once __DIR__ . '/../scripts/config.php';

//henter en enkel input rens funksjon
require_once __DIR__ . '/../Scripts/sanitizeInputs.inc.php';

//henter en funksjon som skal finne og hente bokanbefalingene i svaret til gemini api
require_once __DIR__ . '/../Scripts/prompt_rec_finder.php';

//hente utskriftsmetoden til chatlog
require_once __DIR__ . '/../Scripts/print_chatlog.php';

//starter opp en session 
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Bytter navnet på api-nøkkelen, for bedre oversikt
$apiKey = $Gemini_API_key;

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

// Henter JSON fra JS fetch, henter input 
$input = json_decode(file_get_contents("php://input"), true);
$initialprompt = $input['prompt'] ?? 'Hello Gemini!';
$initialprompt = sanitizeInputs($initialprompt);

//Legger til en start på gemini-prompten, som gir rammer for hvordan gemini skal svare og hva som er relevant for den å svare på
$promptmaker = "Se for deg at du er en formell bibliotekar ekspert på jobb, hvor din arbeidsoppgave er å anbefale og finne bøker skreddersydd til de besøkende hos biblioteket ditt som heter ‘The BookFinder’. Dine svar skal bare om bøker eller bok preferanse. Vær utfyllende om beskrivelsen av bøkene du anbefaler. Om den besøkende nevner en spesifik sjanger de har lyst på, så gir du dem bok anbefalinger i en liste av 5 bøker. Bøkene du anbefaler kan være hva som helst, blant annet skjønnlitterære eller dokumentariske bøker. Bare gi oppfølgingsspørsmål om det er absolutt nødvendig. En person kommer inn i biblioteket og starter en samtale med deg, her er samtalen: ";

// Oppretter en chatsamtale om det ikke er en fra før av
if (!isset($_SESSION['active-chatlog'])) {
    $_SESSION['active-chatlog'] = array($promptmaker); //chatsamtalen er en array som blir appenda til for hver respons/input
} 

// Legger til siste delen av samtalen
$_SESSION['active-chatlog'][] = $initialprompt;

//her bestemmes prompten som blir sendt til gemini
//her tas inn hele 'active-chatlog' og imploder arrayet slikt at gemini forstår samtalen, og det sendes som en lang string
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => implode("Question/Response: ", $_SESSION['active-chatlog'])]
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

$result = json_decode($response, true);
    //print_r($result);   
    //echo $result['candidates'][0]['content']['parts'][0]['text'];

// Print gemini respons, sjekker først om det har kommet en respons
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
//if (false) {
    //legger til gemini respons i 'chatsamtale'
    $text = $result['candidates'][0]['content']['parts'][0]['text'];
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    $_SESSION['active-chatlog'][] = $text;

    //echo "$text<br><br>";
    findrecommendation($text);

    printchatlog();

    //$_SESSION['active-chatlog'][] = $result['candidates'][0]['content']['parts'][0]['text'];
    
} else { //hvis det er en feil, print alt for debug
    echo "<pre>";
    echo ("<strong>FEIL OPPSTÅTT! melding kunne ikke sendes</strong><br>");
    print_r($result);
    echo "</pre>";

    //fjerne spørsmålet brukeren sendte ifra chatsamtalen, slikt at samtalen ikke har samme spørsmål flere ganger og spørsmål/svar rekkefølgen stemmer
    $last_question_index = count($_SESSION['active-chatlog']) - 1;
    unset($_SESSION['active-chatlog'][$last_question_index]);
}
?>