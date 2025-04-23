<?php 
require 'config.php'; 
require 'classes/User.php'; 
require 'classes/Question.php'; 
require 'classes/Comment.php';  

session_start();  

$userObj     = new User($pdo); 
$questionObj = new Question($pdo); 
$commentObj  = new Comment($pdo);  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $question_id = $_POST['question_id'] ?? null;     
    $body        = $_POST['body']        ?? null;     
    $user_id     = $_SESSION['user_id']  ?? null;      
    
    if ($question_id && $body && $user_id && !empty(trim($body))) {         
        $commentObj->add($question_id, $user_id, trim($body));         
        header('Location: questions.php');         
        exit;     
    } 
}  

$questions = $questionObj->getAll(); 
?>  

<!DOCTYPE html> 
<html lang="hy"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Բոլոր Հարցերը</title>     
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Armenian:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --danger-color: #e63946;
            --success-color: #2a9d8f;
            --warning-color: #f4a261;
            --gray-color: #adb5bd;
            --gray-light: #e9ecef;
            --gray-lighter: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Noto Sans Armenian', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--dark-color);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 900px;
            margin: auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 28px;
            position: relative;
            display: inline-block;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: white;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 8px;
            background-color: var(--success-color);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        
        .back-link:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .login-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: white;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 8px;
            background-color: var(--primary-color);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        
        .login-link:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }
        
        .login-link i {
            margin-right: 8px;
        }
        
        .login-message {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .login-message span {
            margin-left: 10px;
            color: var(--gray-color);
        }
        
        .question {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            animation: slideUp 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .question:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }
        
        .question h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 20px;
        }
        
        .question-body {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .question-meta {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 14px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .question-meta i {
            margin-right: 6px;
        }
        
        .question-meta span {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        
        .comments-section {
            margin-left: 15px;
            padding-left: 15px;
            border-left: 2px solid var(--gray-light);
        }
        
        .comments-section h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .comments-section h4 i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .comment {
            background-color: var(--gray-lighter);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            border-left: 3px solid var(--accent-color);
        }
        
        .comment:hover {
            background-color: #f1f3f5;
        }
        
        .comment:last-child {
            margin-bottom: 20px;
        }
        
        .comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .comment-author {
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }
        
        .comment-author i {
            margin-right: 6px;
            color: var(--accent-color);
        }
        
        .comment-time {
            color: var(--gray-color);
            font-size: 12px;
        }
        
        .comment-body {
            line-height: 1.5;
            color: var(--dark-color);
        }
        
        .no-comments {
            padding: 15px;
            color: var(--gray-color);
            text-align: center;
            border: 1px dashed var(--gray-light);
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .comment-form {
            margin-top: 15px;
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        textarea {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--gray-light);
            resize: vertical;
            font-family: 'Noto Sans Armenian', sans-serif;
            font-size: 15px;
            min-height: 80px;
            color: var(--dark-color);
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .submit-button {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-family: 'Noto Sans Armenian', sans-serif;
        }
        
        .submit-button i {
            margin-right: 8px;
        }
        
        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--gray-color);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: var(--primary-color);
            opacity: 0.5;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            .question {
                padding: 20px;
            }
            
            .comments-section {
                margin-left: 5px;
                padding-left: 10px;
            }
        }
    </style>
</head> 
<body>   
    <div class="container">     
        <h2><i class="fas fa-book"></i> Բոլոր Հարցերը</h2>      
        
        <?php if (isset($_SESSION['user_id'])): ?>       
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Վերադառնալ վահանակ
            </a>     
        <?php else: ?>       
            <div class="login-message">
                <a href="login.php" class="login-link">
                    <i class="fas fa-sign-in-alt"></i> Մուտք գործել
                </a>
                <span>՝ մեկնաբանելու համար</span>
            </div>     
        <?php endif; ?>      
        
        <?php if (empty($questions)): ?>
            <div class="empty-state">
                <i class="fas fa-question-circle"></i>
                <p>Հարցեր դեռ չկան։</p>
            </div>
        <?php endif; ?>
        
        <?php foreach ($questions as $q): ?>       
            <div class="question">         
                <h3><?= htmlspecialchars($q['title']) ?></h3>         
                <p class="question-body"><?= nl2br(htmlspecialchars($q['body'])) ?></p>         
                <div class="question-meta">
                    <span>
                        <i class="fas fa-user"></i> <?= htmlspecialchars($q['name']) ?>
                    </span>
                    <span>
                        <i class="far fa-clock"></i> <?= $q['created_at'] ?>
                    </span>
                </div>              
                <div class="comments-section">           
                    <h4><i class="fas fa-comments"></i> Մեկնաբանություններ</h4>           
                    <?php             
                        $comments = $commentObj->getByQuestionId($q['id']);             
                        if ($comments):               
                            foreach ($comments as $c):           
                    ?>             
                    <div class="comment">               
                        <div class="comment-header">
                            <div class="comment-author">
                                <i class="fas fa-user-circle"></i>
                                <?= htmlspecialchars($c['name']) ?>
                            </div>
                            <div class="comment-time">
                                <i class="far fa-clock"></i>
                                <?= $c['created_at'] ?>
                            </div>
                        </div>
                        <div class="comment-body">
                            <?= nl2br(htmlspecialchars($c['content'])) ?>
                        </div>
                    </div>           
                    <?php               
                            endforeach;             
                        else:               
                    ?>
                    <div class="no-comments">
                        <i class="far fa-comment-alt"></i> Դեռ մեկնաբանություն չկա։
                    </div>
                    <?php             
                        endif;           
                    ?>            
                        
                    <?php if (isset($_SESSION['user_id'])): ?>             
                        <form method="POST" class="comment-form">               
                            <input type="hidden" name="question_id" value="<?= $q['id'] ?>">               
                            <textarea name="body" placeholder="Գրեք մեկնաբանություն..." required></textarea>               
                            <button type="submit" class="submit-button">
                                <i class="fas fa-paper-plane"></i> Մեկնաբանել
                            </button>             
                        </form>           
                    <?php endif; ?>         
                </div>       
            </div>     
        <?php endforeach; ?>   
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.question');
            
            questions.forEach((question, index) => {
                question.style.opacity = '0';
                question.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    question.style.opacity = '1';
                    question.style.transform = 'translateY(0)';
                }, 100 + (index * 150));
            });
            const textareas = document.querySelectorAll('textarea');
            
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        });
    </script>
</body> 
</html>