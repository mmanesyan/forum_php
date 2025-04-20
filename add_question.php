<?php 
require 'config.php'; 
require 'classes/Question.php';  

session_start(); 
if (!isset($_SESSION['user_id'])) {     
    header('Location: login.php');     
    exit; 
}  

$q = new Question($pdo);  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $title = $_POST['title'];     
    $body = $_POST['body'];      
    
    if ($q->add($_SESSION['user_id'], $title, $body)) {         
        $message = "Հարցը հաջողությամբ ավելացվեց։";     
    } else {         
        $error = "Սխալ տեղի ունեցավ հարցը ավելացնելիս։";     
    } 
} 
?>  

<!DOCTYPE html> 
<html lang="hy"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ավելացնել հարց</title>     
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 700px;
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
            text-align: center;
            width: 100%;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.5s ease-out;
        }
        
        .message-success {
            background-color: rgba(42, 157, 143, 0.15);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .message-error {
            background-color: rgba(230, 57, 70, 0.15);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 18px;
            color: var(--gray-color);
        }
        
        .form-group.textarea-group i {
            top: 15px;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Noto Sans Armenian', sans-serif;
            color: var(--dark-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            resize: vertical;
        }
        
        textarea {
            min-height: 180px;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .char-counter {
            margin-top: 5px;
            font-size: 13px;
            color: var(--gray-color);
            text-align: right;
            transition: color 0.3s ease;
        }
        
        .char-counter.limit-near {
            color: var(--warning-color);
        }
        
        .char-counter.limit-reached {
            color: var(--danger-color);
            font-weight: 500;
        }
        
        button {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
            font-family: 'Noto Sans Armenian', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        button i {
            margin-right: 10px;
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }
        
        button:active {
            transform: translateY(-1px);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .btn-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-top: 10px;
            background-color: #f1f3f5;
        }
        
        .btn-link:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }
        
        .btn-link i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .footer-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
            align-items: flex-start;
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
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            textarea {
                min-height: 150px;
            }
        }
    </style>
</head> 
<body>     
    <div class="container">     
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Վերադառնալ վահանակ
        </a>
    
        <h2>Ավելացնել նոր հարց</h2>
        
        <?php if (isset($message)): ?>
            <div class="message message-success">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message message-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
          
        <form method="POST" id="question-form">
            <div class="form-group">
                <i class="fas fa-heading"></i>
                <input type="text" name="title" id="title-input" placeholder="Վերնագիր" required>
                <div class="char-counter" id="title-counter">0/100</div>
            </div>
            
            <div class="form-group textarea-group">
                <i class="fas fa-question-circle"></i>
                <textarea name="body" id="body-input" placeholder="Հարցի նկարագրությունը" required></textarea>
                <div class="char-counter" id="body-counter">0/2000</div>
            </div>
            
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Ավելացնել
            </button>
        </form>
        
        <div class="footer-actions">
            <a href="questions.php" class="btn-link">
                <i class="fas fa-book"></i> Դիտել բոլոր հարցերը
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('title-input');
            const titleCounter = document.getElementById('title-counter');
            const titleMaxLength = 100;
            
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                titleCounter.textContent = length + '/' + titleMaxLength;
                
                if (length > titleMaxLength * 0.8 && length <= titleMaxLength) {
                    titleCounter.className = 'char-counter limit-near';
                } else if (length > titleMaxLength) {
                    titleCounter.className = 'char-counter limit-reached';
                } else {
                    titleCounter.className = 'char-counter';
                }
            });
            const bodyInput = document.getElementById('body-input');
            const bodyCounter = document.getElementById('body-counter');
            const bodyMaxLength = 2000;
            
            bodyInput.addEventListener('input', function() {
                const length = this.value.length;
                bodyCounter.textContent = length + '/' + bodyMaxLength;
                
                if (length > bodyMaxLength * 0.8 && length <= bodyMaxLength) {
                    bodyCounter.className = 'char-counter limit-near';
                } else if (length > bodyMaxLength) {
                    bodyCounter.className = 'char-counter limit-reached';
                } else {
                    bodyCounter.className = 'char-counter';
                }
            });
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, 200 + (index * 100));
            });
            
            const button = document.querySelector('button');
            button.style.opacity = '0';
            button.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                button.style.opacity = '1';
                button.style.transform = 'translateY(0)';
            }, 500);
            
            bodyInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            const form = document.getElementById('question-form');
            form.addEventListener('submit', function(e) {
                const titleLength = titleInput.value.length;
                const bodyLength = bodyInput.value.length;
                
                if (titleLength > titleMaxLength || bodyLength > bodyMaxLength) {
                    e.preventDefault();
                    
                    if (titleLength > titleMaxLength) {
                        titleCounter.className = 'char-counter limit-reached';
                        titleInput.focus();
                    }
                    
                    if (bodyLength > bodyMaxLength) {
                        bodyCounter.className = 'char-counter limit-reached';
                        if (titleLength <= titleMaxLength) {
                            bodyInput.focus();
                        }
                    }
                }
            });
        });
    </script>
</body> 
</html>