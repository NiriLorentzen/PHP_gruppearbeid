<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">    
    <title>BookFinder</title>
    <link rel="stylesheet" href="CSS/stylesheet.css">
</head>
<body>
    <h1>BookFinder</h1>


    <form id="bookForm">
        <label for="bookRec">Spør om bøker!:</label><br>
        <input type="text" id="bookRec" name="bookRec"><br>
        <button type="submit">Søk</button>
    </form>

    <div id="results"></div>

    <h2>Ask Gemini</h2>
    <div id="chatbox" style="border:1px solid #ccc; padding:10px; max-width:600px; min-height:200px;display: flex; align-items: center; flex-direction: column;">
    </div>
    <input type="text" id="prompt" placeholder="Ask something..." style="width:400px;">
    <button id="sendBtn">Send</button><button id="slett_chat">Fjern samtalen</button>

<script>
//Henter tekst lagt inn i bookForm, sender søket til booksAPI.php. Leser svaret som json
        document.getElementById("bookForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const query = document.getElementById("bookRec").value;
            const response = await fetch("Api/booksAPI.php?q=" + encodeURIComponent(query));
            const books = await response.json();
//Fjerner gamle resultater
            const resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = "";
//Feilmeldinger
            if (books.error) {
                resultsDiv.innerHTML = "<p>" + books.error + "</p>";
                return;
            }

//Lager en div for hver bok anbefalning med tittel, forfatter, side antall og bok beskrivelse
            books.forEach(book => {
                const div = document.createElement("div");
                div.className = "book";
                div.innerHTML = `
                    ${book.thumbnail ? `<img src="${book.thumbnail}" alt="Bokomslag">` : ""}
                    <div>
                        <h3>${book.title}</h3>
                        <p><strong>Forfatter:</strong> ${book.authors}</p>
                        <p><strong>Antall Sider:</strong> ${book.pageCount}</p>                        
                        <p>${book.description}</p>
                        
                    </div>
                `;
                resultsDiv.appendChild(div);
            });
        });


        //gemini 
        document.getElementById('sendBtn').addEventListener('click', async () => {
        const prompt = document.getElementById('prompt').value;
      
        const response = await fetch('Api/geminiAPI.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt })
        });

        const data = await response.text(); 
        document.getElementById('chatbox').innerHTML = data;
        });


        document.getElementById('slett_chat').addEventListener('click', async () => {
            if (!confirm("Are you sure you want to reset the chat?")) return; //åpner et vindu i nettleseren, hvor man trykker for å fortsette eller avbryte

            const response = await fetch('Scripts/session_destroy.php');
            const text = await response.text();
            document.getElementById('chatbox').innerHTML = `<p style="color:red;">${text}</p>`;
        });
</script>
</html>
