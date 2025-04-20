<?php
class Comment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($question_id, $user_id, $body) {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (question_id, user_id, body)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$question_id, $user_id, $body]);
    }

    public function getByQuestionId($question_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.id,
                   c.question_id,
                   c.user_id,
                   c.body,
                   c.created_at,
                   u.name,
                   u.avatar
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.question_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$question_id]);
        return $stmt->fetchAll();
    }
}
