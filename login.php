<?php
require 'config.php';
require 'classes/User.php';

session_start();
$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $loggedInUser = $user->login($email, $password);
    if ($loggedInUser) {
        $_SESSION['user_id'] = $loggedInUser['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Սխալ մուտքագրված տվյալներ։";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Մուտք</title>
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
            max-width: 400px;
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
        
        .register-link {
            display: block;
            margin-top: 1.5rem;
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .register-link:hover {
            color: var(--secondary-color);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--secondary-color);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #6c757d;
        }
        
        .divider-line {
            flex-grow: 1;
            height: 1px;
            background-color: #e0e0e0;
        }
        
        .divider-text {
            padding: 0 1rem;
            font-size: 0.9rem;
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
        <h2>Մուտք</h2>
        
        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
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
            
            <button type="submit" class="btn">Մուտք գործել</button>
            
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">կամ</span>
                <div class="divider-line"></div>
            </div>
            
            <a href="register.php" class="register-link">Դեռ գրանցվա՞ծ չես։ Գրանցվիր հիմա</a>
        </form>
    </div>
</body>
</html>