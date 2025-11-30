<?php
    class chat_search {

        private PDO $pdo;
        public function __construct(PDO $pdo){
            $this->pdo = $pdo;
        }

        function find_chat(int $chatid){
            //søke i DB etter ID i parameter
            $q = $this->pdo->prepare(
                "SELECT * FROM chatlog WHERE chatid = :chatid");
            $q->execute([":chatid" => $chatid]);
            $chat = $q->fetchAll(PDO::FETCH_ASSOC);

            //dersom det er en chat i DB med den id-en
            if(empty($chat)){
                //gjør ingenting
            } else{
                //chatlog lagres som string, så dette blir gjort om til array for utskrift-funksjonen
                $chat_array = explode("spm/svar", $chat[0]["chatlog"]);
                //lagres i sesjon
                $_SESSION['active-chatlog'] = $chat_array;
                return true; //for å hjelpe med utskrift
            }
        }
    }
?>