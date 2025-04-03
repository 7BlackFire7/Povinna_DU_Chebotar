<?php

class QnA {
    private $pdo;

    // Constructor for connecting to a database
    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Chyba pripojenia: " . $e->getMessage();
        }
    }

    // Method for getting questions and answers
    public function getQnA() {
        try {
            $stmt = $this->pdo->query("SELECT question, answer FROM qna");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Chyba načítania otázok: " . $e->getMessage();
            return [];
        }
    }

    // Method for adding question and answer with uniqueness check
    public function addQnA($question, $answer) {
        try {
            // Checking for the existence of a record
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM qna WHERE question = :question AND answer = :answer");
            $stmt->execute([':question' => $question, ':answer' => $answer]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                return "Otázka a odpoveď už existujú.";
            }

            // Insert a new entry if there is none
            $stmt = $this->pdo->prepare("INSERT INTO qna (question, answer) VALUES (:question, :answer)");
            $stmt->execute([':question' => $question, ':answer' => $answer]);
            return "Otázka a odpoveď boli úspešne pridané.";
        } catch (PDOException $e) {
            return "Chyba pri pridávaní: " . $e->getMessage();
        }
    }
}

?>
