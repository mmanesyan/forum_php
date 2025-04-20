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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . $avatar);
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, avatar = ? WHERE id = ?");
    $success = $stmt->execute([$name, $email, $avatar, $_SESSION['user_id']]);
    
    if ($success) {
        $user = $userObj->getById($_SESSION['user_id']); 
        $message = "Տվյալները հաջողությամբ թարմացվեցին։";
    } else {
        $error = "Email-ը հնարավոր է արդեն օգտագործվում է։";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Իմ Պրոֆիլը</title>
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
            max-width: 550px;
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
            margin-bottom: 20px;
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
        
        .avatar-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .avatar:hover {
            transform: scale(1.05);
        }
        
        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: var(--gray-color);
            margin: 0 auto;
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
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
        }
        
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Noto Sans Armenian', sans-serif;
            color: var(--dark-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
    .file-upload {
        position: relative;
        display: inline-block;
        width: 100%;
        height: 50px;
        border-radius: 10px;
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        margin: 0 0 20px;
    }

        
        .file-upload:hover {
            background-color: #e9ecef;
            border-color: var(--primary-color);
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-upload-label {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            display: inline-flex;
            align-items: center;
            color: var(--gray-color);
            font-size: 14px;
            text-align: center; 
        }
        
        .file-upload-label i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .file-name {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-left: 5px;
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
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }
        
        button:active {
            transform: translateY(-1px);
        }
        
        .links-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        
        .link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .link:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .link i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .link-logout {
            color: var(--danger-color);
        }
        
        .link-logout i {
            color: var(--danger-color);
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
            
            .avatar, .avatar-placeholder {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Վերադառնալ վահանակ
        </a>
        
        <h2>Իմ Պրոֆիլը</h2>
        
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

        <div class="avatar-container">
            <?php if ($user['avatar']): ?>
                <img src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Անուն" required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Էլ. հասցե" required>
            </div>
            
            <div class="form-group">
                <label for="avatar-upload" class="file-upload">
                    <input type="file" name="avatar" id="avatar-upload" accept="image/*">
                    <span class="file-upload-label">
                        <div class="fas fa-cloud-upload-alt"></div> 
                        <p style="margin: 3px;">Ընտրել նկար</p>
                        <span class="file-name"></span>
                    </span>
                </label>
            </div>
            
            <button type="submit">
                <i class="fas fa-save"></i> Թարմացնել
            </button>
        </form>

        <div class="links-container">
            <a href="add_question.php" class="link">
                <i class="fas fa-plus-circle"></i> Ավելացնել հարց
            </a>
            <a href="questions.php" class="link">
                <i class="fas fa-book"></i> Բոլոր հարցերը
            </a>
            <a href="logout.php" class="link link-logout">
                <i class="fas fa-sign-out-alt"></i> Ելք
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('avatar-upload');
            const fileName = document.querySelector('.file-name');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileName.textContent = this.files[0].name;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatarContainer = document.querySelector('.avatar-container');
                    
                        avatarContainer.innerHTML = '';
                    
                        const newAvatar = document.createElement('img');
                        newAvatar.src = e.target.result;
                        newAvatar.className = 'avatar';
                        newAvatar.style.opacity = '0';
                        avatarContainer.appendChild(newAvatar);
                        
                        setTimeout(() => {
                            newAvatar.style.opacity = '1';
                        }, 50);
                    };
                    reader.readAsDataURL(this.files[0]);
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
            
            const links = document.querySelectorAll('.link');
            links.forEach((link, index) => {
                link.style.opacity = '0';
                link.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    link.style.opacity = '1';
                    link.style.transform = 'translateX(0)';
                }, 600 + (index * 100));
            });
        });
    </script>
</body>
</html>