<?php
    class chat_search {

        private PDO $pdo;
        public function __construct(PDO $pdo){
            $this->pdo = $pdo;
        }

        function find_chat(int $chatid){
            //$chatid = 1;

            //søke i DB
            $q = $this->pdo->prepare(
                "SELECT * FROM chatlog WHERE chatid = :chatid");
            $q->execute([":chatid" => $chatid]);
            $chat = $q->fetchAll(PDO::FETCH_ASSOC);
            //print_r($chat);
            if(empty($chat)){
            } else{
                $chat_array = explode("spm/svar", $chat[0]["chatlog"]);
                $_SESSION['active-chatlog'] = $chat_array;
                return true;
            }
        }
    }
?>