<?php
require 'config.php';
require 'classes/User.php';

session_start();
$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $avatarName = '';
    if ($_FILES['avatar']['name']) {
        $avatarName = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . $avatarName);
    }

    if ($user->register($name, $email, $password, $avatarName)) {
        header('Location: login.php');
        exit;
    } else {
        $error = "Գրանցումը ձախողվեց։ Email-ը հնարավոր է արդեն օգտագործվում է։";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Գրանցում</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --error-color: #ef476f;
            --success-color: #06d6a0;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --input-bg: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            background-color: var(--input-bg);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 1.2rem;
            text-align: center;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem;
            background-color: #f1f3f9;
            border: 2px dashed #d1d9e6;
            border-radius: var(--border-radius);
            cursor: pointer;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            background-color: #e9ecef;
        }
        
        .file-input-label i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-color);
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
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
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
        
        .login-link {
            display: block;
            margin-top: 1.5rem;
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .login-link:hover {
            color: var(--secondary-color);
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 1.5rem;
            }
            
            h2 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Գրանցում</h2>
        
        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Անուն</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ձեր անունը" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Էլ․ հասցե</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@mail.com" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Գաղտնաբառ</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Գաղտնաբառ" required>
                </div>
            </div>
            
            <div class="file-input-container">
                <label class="file-input-label" for="avatar">
                    <i class="fas fa-image"></i>
                    <span>Ընտրել նկար</span>
                </label>
                <input type="file" id="avatar" name="avatar" class="file-input" accept="image/*">
                <div class="file-name" id="file-name">Ոչ մի ֆայլ ընտրված չէ</div>
            </div>
            
            <button type="submit" class="btn">Գրանցվել</button>
        </form>
        
        <a href="login.php" class="login-link">Արդեն գրանցվա՞ծ ես։ Մուտք գործել</a>
    </div>
    
    <script>
        document.getElementById('avatar').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Ոչ մի ֆայլ ընտրված չէ';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>