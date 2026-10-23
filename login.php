<?php
require_once 'includes/functions.php';

$error = '';
$success = '';
$logoutMessage = '';


if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');
    
    if (login($username, $password)) {
        $success = 'Login berhasil! Mengalihkan ke dashboard...';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}


if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #8B5CF6;
            --secondary-purple: #A855F7;
            --light-purple: #C4B5FD;
            --dark-purple: #6D28D9;
            --primary-teal: #06B6D4;
            --secondary-teal: #0891B2;
            --light-teal: #67E8F9;
            --dark-teal: #0E7490;
            --gradient-main: linear-gradient(135deg, #8B5CF6 0%, #06B6D4 100%);
            --gradient-secondary: linear-gradient(135deg, #A855F7 0%, #0891B2 100%);
            --gradient-light: linear-gradient(135deg, #C4B5FD 0%, #67E8F9 100%);
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--gradient-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            margin: 20px;
            position: relative;
            z-index: 2;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            width: 100%;
        }

        .login-header {
            background: var(--gradient-main);
            color: white;
            padding: 3rem 2.5rem;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="30" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="70" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="80" r="2.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: sparkle 3s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .login-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .login-subtitle {
            opacity: 0.9;
            font-size: 1rem;
            position: relative;
            z-index: 2;
            font-weight: 400;
        }

        .login-body {
            padding: 3rem 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--white);
            height: auto;
        }

        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
            background: var(--white);
        }

        .form-floating > label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .btn-login {
            background: var(--gradient-main);
            border: none;
            color: white;
            font-weight: 700;
            padding: 1rem;
            border-radius: 16px;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            text-transform: none;
            letter-spacing: 0;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(139, 92, 246, 0.4);
            background: var(--gradient-secondary);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .demo-credentials {
            background: var(--gradient-light);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        .demo-title {
            font-weight: 700;
            color: var(--dark-purple);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .demo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .demo-label {
            color: var(--dark-purple);
            font-weight: 500;
        }

        .demo-value {
            background: var(--white);
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            color: var(--primary-purple);
            font-weight: 600;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .demo-value:hover {
            background: var(--gray-50);
            border-color: var(--primary-purple);
        }

        .clock-container {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--gray-50);
            border-radius: 16px;
            border: 1px solid var(--gray-200);
        }

        .clock-title {
            font-size: 0.9rem;
            color: var(--primary-purple);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .clock-time {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-purple);
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            margin-bottom: 0.5rem;
        }

        .clock-date {
            font-size: 1rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .alert {
            border-radius: 16px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }

        .alert-info {
            background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
            color: white;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: var(--dark-purple);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 10px;
            }
            
            .login-card {
                border-radius: 24px;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .login-icon {
                font-size: 3rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .clock-time {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h1 class="login-title">Admin Login</h1>
                <p class="login-subtitle">Perpustakaan Digital Cerdas</p>
            </div>
            
            <div class="login-body">
                
                <?php if ($logoutMessage): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= $logoutMessage ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                        <label for="username">
                            <i class="fas fa-user me-2"></i>Username
                        </label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Masuk
                    </button>
                </form>
                
                <div class="back-link">
                    <a href="index.php">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time clock
        function updateClock() {
            const now = new Date();
            
            // Format time
            const timeOptions = {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            
            // Format date
            const dateOptions = {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            const timeString = now.toLocaleTimeString('id-ID', timeOptions);
            const dateString = now.toLocaleDateString('id-ID', dateOptions);
            
            document.getElementById('currentTime').textContent = timeString;
            document.getElementById('currentDate').textContent = dateString;
        }
        
        // Update clock immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);
        
        // Auto-fill demo credentials on click
        document.querySelectorAll('.demo-value').forEach(element => {
            element.addEventListener('click', function() {
                const value = this.textContent;
                const parent = this.closest('.demo-item');
                const label = parent.querySelector('.demo-label').textContent;
                
                if (label.includes('Username')) {
                    document.getElementById('username').value = value;
                    document.getElementById('username').focus();
                } else if (label.includes('Password')) {
                    document.getElementById('password').value = value;
                    document.getElementById('password').focus();
                }
            });
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Harap isi username dan password!');
                return false;
            }
       
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
