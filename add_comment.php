<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $question_id = $_POST['question_id'];
    $comment = htmlspecialchars(trim($_POST['comment']));

    $stmt = $conn->prepare("INSERT INTO comments (question_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $question_id, $user_id, $comment);
    $stmt->execute();
}

header("Location: question.php?id=" . $_POST['question_id']);
exit;
