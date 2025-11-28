    
function saveBookBtn() {    
    //Lagrer bok når "Putt boken i hyllen" knappen blir trykket på
    document.querySelectorAll(".saveBookBtn").forEach(btn => {
        btn.addEventListener("click", async () => {
            const parent = btn.closest(".book");
            const book = {
                bookID: parent.dataset.bookId,
                title: parent.dataset.title,
                authors: parent.dataset.authors,
                description: parent.dataset.description,
                pageCount: parent.dataset.pageCount,
                thumbnail: parent.dataset.thumbnail
            };

            const response = await fetch("api/handleBookshelf.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(book)
            });

        
            const responseText = await response.text();        

            try {
                const result = JSON.parse(responseText);
                alert(result.message);
            } catch (e) {
                alert("Serveren returnerte ikke gyldig JSON. Sjekk konsollen.");
            }

            
        });
    });
}

function geminiChatSendBtn() {
    //gemini 
    document.getElementById('sendBtn').addEventListener('click', async () => {
        //vise brukeren at knappen er trykket
        document.getElementById('chatbox').innerHTML = `<p">Snakker med bibliotekaren...<br><br>Dette kan ta noen sekunder:D</p>`;
        
        const prompt = document.getElementById('prompt').value;

        const response = await fetch('api/geminiAPI.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt })
        });

        const data = await response.text(); 
        document.getElementById('chatbox').innerHTML = data;
        // Venter litt for at session skal ha tid til å lagre riktig
        setTimeout(() => {
            window.location.replace(window.location.pathname);
        }, 200);
    });
}