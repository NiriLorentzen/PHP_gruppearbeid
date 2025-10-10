<?php
// Replace with your Gemini API key
$apiKey = '';

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

// Get JSON data from JS fetch
$input = json_decode(file_get_contents("php://input"), true);
$prompt = $input['prompt'] ?? 'Hello Gemini!';
$promptmaker = "Imagine you're a formal librarian expert at work where your job is to reccomend and find books tailered to the visitors of your library called The BookFinder. You're respons is only supposed to be about the subject of books or book preference. If the visitor mentions a specific subject they want books from then give them books reccomendations as a list of 5. Only give follow-up questions if really needed. A visitor enters the library and asks you the following: ";
$prompt = $promptmaker . $prompt;
echo $prompt;
//prompt = "Reccomend me a few good science fiction books and tell me their review and why you reccomend them.";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

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

// Print Gemini response
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo "<h3>Gemini says:</h3>";
    echo "<p>" . nl2br(htmlspecialchars($result['candidates'][0]['content']['parts'][0]['text'])) . "</p>";
} else {
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
?>