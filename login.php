<?php
// login.php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            loginUser($user);
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0F172A;
            --accent: #06B6D4;
            --bg: #F8FAFC;
            --text: #334155;
            --border: #e2e8f0;
            --white: #ffffff;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            animation: slideUp 0.5s ease-out forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-family: 'Poppins', sans-serif;
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        /* Floating Label CSS */
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 1rem;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background-color: transparent;
            border: 2px solid var(--border);
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
        }

        .form-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            transition: all 0.3s ease;
            pointer-events: none;
            background: var(--white);
            padding: 0 0.25rem;
        }

        .form-control:focus ~ .form-label,
        .form-control:not(:placeholder-shown) ~ .form-label {
            top: 0;
            font-size: 0.85rem;
            color: var(--accent);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--accent);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--accent);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: #0891b2;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.4);
        }

        /* Ripple effect */
        .btn-login::after {
            content: "";
            background: rgba(255, 255, 255, 0.3);
            display: block;
            position: absolute;
            border-radius: 50%;
            padding-top: 240%;
            padding-left: 240%;
            margin-top: -120%;
            margin-left: -120%;
            opacity: 0;
            transition: all 0.8s;
        }
        .btn-login:active::after {
            padding-top: 0;
            padding-left: 0;
            margin-top: 0;
            margin-left: 0;
            opacity: 1;
            transition: 0s;
        }

        .error-message {
            background: #fee2e2;
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .back-home {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .back-home:hover {
            color: var(--accent);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Sign in to access the Dashboard</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="text" id="username" name="username" class="form-control" placeholder=" " required autocomplete="off">
                <label for="username" class="form-label">Username</label>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" class="form-control" placeholder=" " required>
                <label for="password" class="form-label">Password</label>
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </form>
    </div>

    <script>
        // Password Visibility Toggle
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            // Toggle type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
