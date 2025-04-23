<?php
require 'config.php';
require 'classes/User.php';
require 'classes/Question.php';
require 'classes/Comment.php';

session_start();
$user = new User($pdo);
$question = new Question($pdo);
$comment = new Comment($pdo);

$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn) {
    $currentUser = $user->getUserById($_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question']) && $isLoggedIn) {
    $title = $_POST['title'];
    $body = $_POST['body'];
    
    if ($question->add($_SESSION['user_id'], $title, $body)) {
        $successMessage = "Հարցը հաջողությամբ հրապարակվեց։";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($successMessage));
        exit;
    } else {
        $error = "Հարցը չի հաջողվել հրապարակել։";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && $isLoggedIn) {
    $content = $_POST['content'];
    $questionId = $_POST['question_id'];
    $parentId = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
    
    if ($comment->add($questionId, $_SESSION['user_id'], $content, $parentId)) {
        $successMessage = "Մեկնաբանությունը հաջողությամբ հրապարակվեց։";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?question=' . $questionId . '&success=' . urlencode($successMessage));
        exit;
    } else {
        $error = "Մեկնաբանությունը չի հաջողվել։";
    }
}

$questions = $question->getAll();

$currentQuestion = null;
$questionComments = [];
if (isset($_GET['question']) && is_numeric($_GET['question'])) {
    foreach ($questions as $q) {
        if ($q['id'] == $_GET['question']) {
            $currentQuestion = $q;
            break;
        }
    }
    
    if ($currentQuestion) {
        $questionComments = $comment->getByQuestionId($_GET['question']);
    }
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : null;
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Հարցեր և մեկնաբանություններ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --error-color: #ef476f;
            --success-color: #06d6a0;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-color: #e0e0e0;
            --input-bg: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --comment-bg: #f1f3f9;
            --reply-bg: #e9ecef;
            --header-bg: #3a0ca3;
            --question-bg: #e6f7ff;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding-bottom: 2rem;
        }
        
        header {
            background-color: var(--header-bg);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }
        
        .nav-links a:hover {
            opacity: 0.8;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e0e0e0;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .page-title {
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
            font-size: 2rem;
            font-weight: 600;
        }
        
        .error-message {
            color: var(--error-color);
            background-color: rgba(239, 71, 111, 0.1);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-message i {
            margin-right: 0.5rem;
        }
        
        .success-message {
            color: var(--success-color);
            background-color: rgba(6, 214, 160, 0.1);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-message i {
            margin-right: 0.5rem;
        }
        
        .form-container {
            background-color: var(--input-bg);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .form {
            display: flex;
            flex-direction: column;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-start;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .login-message {
            text-align: center;
            padding: 1.5rem;
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
        }
        
        .login-message a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-message a:hover {
            color: var(--secondary-color);
        }
        
        .tab-container {
            margin-bottom: 2rem;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .tab {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: #6c757d;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .questions-list {
            list-style: none;
        }
        
        .question-item {
            background-color: var(--question-bg);
            padding: 1.2rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .question-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        .question-author {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e0e0e0;
        }
        
        .author-info {
            display: flex;
            flex-direction: column;
        }
        
        .author-name {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .question-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .question-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--secondary-color);
        }
        
        .question-body {
            margin-bottom: 1rem;
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }
        
        .question-actions {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .question-action {
            font-size: 0.9rem;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        .question-action:hover {
            color: var(--primary-color);
        }
        
        .question-detail {
            background-color: var(--question-bg);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        
        .comments-container {
            margin-top: 2rem;
        }
        
        .comments-list {
            list-style: none;
        }
        
        .comment {
            background-color: var(--comment-bg);
            padding: 1.2rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        .comment-author {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .comment-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .comment-content {
            margin-bottom: 1rem;
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }
        
        .comment-actions {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .comment-action {
            font-size: 0.9rem;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: color 0.3s ease;
        }
        
        .comment-action:hover {
            color: var(--primary-color);
        }
        
        .reply-form {
            margin-top: 1rem;
            margin-left: 3rem;
            display: none;
        }
        
        .reply-textarea {
            min-height: 80px;
        }
        
        .replies {
            margin-top: 1rem;
            margin-left: 3rem;
        }
        
        .reply {
            background-color: var(--reply-bg);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }
        
        .no-content {
            text-align: center;
            padding: 2rem;
            background-color: var(--input-bg);
            border-radius: var(--border-radius);
            color: #6c757d;
            font-style: italic;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .nav-links {
                justify-content: center;
            }
            
            .replies {
                margin-left: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">Հարցուպատասխան</div>
            <nav class="nav-links">
                <a href="index.php">Գլխավոր</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">Իմ էջը</a>
                    <a href="logout.php">Դուրս գալ</a>
                <?php else: ?>
                    <a href="login.php">Մուտք</a>
                    <a href="register.php">Գրանցվել</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($successMessage)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($currentQuestion): ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Բոլոր հարցերը
            </a>
            
            <div class="question-detail">
                <div class="question-header">
                    <div class="question-author">
                        <?php if (!empty($currentQuestion['avatar'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($currentQuestion['avatar']); ?>" alt="Avatar" class="author-avatar">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                        <?php endif; ?>
                        
                        <div class="author-info">
                            <span class="author-name"><?php echo htmlspecialchars($currentQuestion['name']); ?></span>
                            <span class="question-date"><?php echo date('d.m.Y H:i', strtotime($currentQuestion['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <h2 class="question-title"><?php echo htmlspecialchars($currentQuestion['title']); ?></h2>
                
                <div class="question-body">
                    <?php echo nl2br(htmlspecialchars($currentQuestion['body'])); ?>
                </div>
            </div>
            
            <?php if ($isLoggedIn): ?>
                <div class="form-container">
                    <h3>Ձեր պատասխանը</h3>
                    <form method="POST" class="form">
                        <input type="hidden" name="question_id" value="<?php echo $currentQuestion['id']; ?>">
                        <div class="form-group">
                            <textarea name="content" class="form-control" placeholder="Գրեք ձեր պատասխանը այստեղ..." required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn">Հրապարակել պատասխանը</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-message">
                    <p>Պատասխանելու համար անհրաժեշտ է <a href="login.php">մուտք գործել</a> կամ <a href="register.php">գրանցվել</a>։</p>
                </div>
            <?php endif; ?>
            
            <div class="comments-container">
                <h3>Պատասխաններ (<?php echo count($questionComments); ?>)</h3>
                
                <?php if (empty($questionComments)): ?>
                    <div class="no-content">
                        <p>Այս հարցին դեռևս պատասխաններ չկան։ Եղեք առաջինը, ով կպատասխանի։</p>
                    </div>
                <?php else: ?>
                    <ul class="comments-list">
                        <?php foreach ($questionComments as $commentItem): ?>
                            <?php if ($commentItem['parent_id'] === null):  ?>
                                <li class="comment">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <?php if (!empty($commentItem['avatar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($commentItem['avatar']); ?>" alt="Avatar" class="author-avatar">
                                            <?php else: ?>
                                                <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                            <?php endif; ?>
                                            
                                            <div class="author-info">
                                                <span class="author-name"><?php echo htmlspecialchars($commentItem['name']); ?></span>
                                                <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($commentItem['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($commentItem['content'])); ?>
                                    </div>
                                    
                                    <div class="comment-actions">
                                        <?php if ($isLoggedIn): ?>
                                            <a href="#" class="comment-action reply-toggle" data-comment-id="<?php echo $commentItem['id']; ?>">
                                                <i class="fas fa-reply"></i> Պատասխանել
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($isLoggedIn): ?>
                                        <div id="reply-form-<?php echo $commentItem['id']; ?>" class="reply-form">
                                            <form method="POST">
                                                <input type="hidden" name="question_id" value="<?php echo $currentQuestion['id']; ?>">
                                                <input type="hidden" name="parent_id" value="<?php echo $commentItem['id']; ?>">
                                                <div class="form-group">
                                                    <textarea name="content" class="form-control reply-textarea" placeholder="Ձեր պատասխանը..." required></textarea>
                                                </div>
                                                <div style="display: flex; gap: 10px;">
                                                    <button type="submit" name="submit_comment" class="btn">Ուղարկել</button>
                                                    <button type="button" class="btn btn-secondary cancel-reply" data-comment-id="<?php echo $commentItem['id']; ?>">Չեղարկել</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $replies = array_filter($questionComments, function($reply) use ($commentItem) {
                                        return $reply['parent_id'] === $commentItem['id'];
                                    });
                                    
                                    if (!empty($replies)):
                                    ?>
                                    <div class="replies">
                                        <?php foreach ($replies as $reply): ?>
                                            <div class="reply">
                                                <div class="comment-header">
                                                    <div class="comment-author">
                                                        <?php if (!empty($reply['avatar'])): ?>
                                                            <img src="uploads/<?php echo htmlspecialchars($reply['avatar']); ?>" alt="Avatar" class="author-avatar">
                                                        <?php else: ?>
                                                            <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                                        <?php endif; ?>
                                                        
                                                        <div class="author-info">
                                                            <span class="author-name"><?php echo htmlspecialchars($reply['name']); ?></span>
                                                            <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($reply['created_at'])); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="comment-content">
                                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <h1 class="page-title">Հարցեր և մեկնաբանություններ</h1>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" data-tab="questions">Հարցեր</div>
                    <div class="tab" data-tab="ask-question">Հարց տալ</div>
                </div>
                
                <div id="questions" class="tab-content active">
                    <?php if (empty($questions)): ?>
                        <div class="no-content">
                            <p>Դեռևս հարցեր չկան։ Եղեք առաջինը, ով կտա հարց։</p>
                        </div>
                    <?php else: ?>
                        <ul class="questions-list">
                            <?php foreach ($questions as $questionItem): ?>
                                <li class="question-item" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'] . '?question=' . $questionItem['id']; ?>'">
                                    <div class="question-header">
                                        <div class="question-author">
                                            <?php if (!empty($questionItem['avatar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($questionItem['avatar']); ?>" alt="Avatar" class="author-avatar">
                                            <?php else: ?>
                                                <i class="fas fa-user-circle fa-2x" style="color: #6c757d;"></i>
                                            <?php endif; ?>
                                            
                                            <div class="author-info">
                                                <span class="author-name"><?php echo htmlspecialchars($questionItem['name']); ?></span>
                                                <span class="question-date"><?php echo date('d.m.Y H:i', strtotime($questionItem['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h3 class="question-title"><?php echo htmlspecialchars($questionItem['title']); ?></h3>
                                    
                                    <div class="question-body">
                                        <?php 
                                        $bodyPreview = strlen($questionItem['body']) > 200 ? 
                                            substr($questionItem['body'], 0, 200) . '...' : 
                                            $questionItem['body'];
                                        echo nl2br(htmlspecialchars($bodyPreview)); 
                                        ?>
                                    </div>
                                    
                                    <div class="question-actions">
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?question=' . $questionItem['id']; ?>" class="question-action">
                                            <i class="fas fa-comments"></i> Պատասխանել
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <div id="ask-question" class="tab-content">
                    <?php if ($isLoggedIn): ?>
                        <div class="form-container">
                            <form method="POST" class="form">
                                <div class="form-group">
                                    <label for="title">Հարցի վերնագիրը</label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="Հարցի վերնագիրը..." required>
                                </div>
                                <div class="form-group">
                                    <label for="body">Հարցի մանրամասները</label>
                                    <textarea id="body" name="body" class="form-control" placeholder="Նկարագրեք ձեր հարցը մանրամասն..." required></textarea>
                                </div>
                                <button type="submit" name="submit_question" class="btn">Հրապարակել հարցը</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="login-message">
                            <p>Հարց տալու համար անհրաժեշտ է <a href="login.php">մուտք գործել</a> կամ <a href="register.php">գրանցվել</a>։</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    this.classList.add('active');
                    const tabContent = document.getElementById(this.dataset.tab);
                    if (tabContent) {
                        tabContent.classList.add('active');
                    }
                });
            });
            
            const replyToggles = document.querySelectorAll('.reply-toggle');
            const cancelButtons = document.querySelectorAll('.cancel-reply');
            
            replyToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    
            
                    document.querySelectorAll('.reply-form').forEach(form => {
                        if (form !== replyForm) {
                            form.style.display = 'none';
                        }
                    });
                    
                    replyForm.style.display = replyForm.style.display === 'block' ? 'none' : 'block';
                    
                    if (replyForm.style.display === 'block') {
                        replyForm.querySelector('textarea').focus();
                    }
                });
            });
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const replyForm = document.getElementById(`reply-form-${commentId}`);
                    replyForm.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>