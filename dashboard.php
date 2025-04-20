<?php
require 'config.php';
require 'classes/User.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userObj = new User($pdo);
$user = $userObj->getById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Իմ Վահանակը</title>
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
            max-width: 500px;
            text-align: center;
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
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .avatar-container {
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .avatar:hover {
            transform: scale(1.05);
        }
        
        .avatar-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: var(--success-color);
            border-radius: 50%;
            border: 3px solid white;
        }
        
        .menu-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 350px;
            margin: auto;
        }
        
        .btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: white;
            color: var(--dark-color);
            text-decoration: none;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            z-index: -1;
            transition: width 0.4s ease;
        }
        
        .btn:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn:hover::before {
            width: 100%;
        }
        
        .btn i {
            margin-right: 15px;
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover i {
            transform: scale(1.2);
        }
        
        .btn-logout {
            border-left: 4px solid var(--danger-color);
        }
        
        .btn-logout::before {
            background: linear-gradient(90deg, var(--danger-color) 0%, #ff758f 100%);
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
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .avatar {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Բարի գալուստ, <?= htmlspecialchars($user['name']) ?>!</h2>

        <?php if ($user['avatar']): ?>
            <div class="avatar-container">
                <img src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar">
            </div>
        <?php endif; ?>

        <div class="menu-container">
            <a href="profile.php" class="btn">
                <i class="fas fa-user"></i> Իմ Պրոֆիլը
            </a>
            <a href="add_question.php" class="btn">
                <i class="fas fa-plus-circle"></i> Ավելացնել Հարց
            </a>
            <a href="questions.php" class="btn">
                <i class="fas fa-book"></i> Տեսնել Բոլոր Հարցերը
            </a>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Ելք
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            
            buttons.forEach((btn, index) => {
                btn.style.opacity = '0';
                btn.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    btn.style.opacity = '1';
                    btn.style.transform = 'translateY(0)';
                }, 300 + (index * 100));
            });
        });
    </script>
</body>
</html>