<?php
class Question {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($user_id, $title, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO questions (user_id, title, body) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $title, $body]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT q.*, u.name, u.avatar
            FROM questions q
            JOIN users u ON q.user_id = u.id
            ORDER BY q.created_at DESC
        ");
        return $stmt->fetchAll();
    }
}
?>
