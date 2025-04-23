<?php
class Comment {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByQuestionId($questionId) {
        $stmt = $this->pdo->prepare("SELECT c.*, u.name, u.avatar 
                                     FROM comments c 
                                     JOIN users u ON c.user_id = u.id 
                                     WHERE c.question_id = ? 
                                     ORDER BY c.created_at ASC");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function add($questionId, $userId, $content, $parentId = null) {
        $sql = "INSERT INTO comments (content, user_id, question_id, parent_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$content, $userId, $questionId, $parentId]);
    }
    
    public function getAllComments() {
        $sql = "SELECT * FROM comments ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCommentsByUserId($userId) {
        $sql = "SELECT * FROM comments WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>