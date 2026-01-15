<?php
// Load authentication controller
require_once __DIR__ . '/../controllers/auth.php';

$error = '';
$registered = isset($_GET['registered']) ? true : false;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new AuthController();
    $result = $controller->login(
        trim($_POST['login']),
        $_POST['password']
    );
    
    if ($result['success']) {
        header('Location: ' . $result['redirect']);
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php if ($registered): ?>
        <div class="toast toast-success" id="successToast">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Registration successful! Please login with your credentials.
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="toast toast-error" id="errorToast">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="auth-box">
            <div class="auth-header">
                <div class="auth-logo">L&F</div>
                <h1>Welcome back</h1>
                <p class="auth-subtitle">Please login to your account</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="login">Email or Student ID</label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           placeholder="Enter your email or student ID"
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" 
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="eye-open">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Sign In</span>
                </button>
            </form>
            
            <p class="auth-footer">
                Don't have an account? <a href="register.php">Create account</a>
            </p>
        </div>
    </div>

    <script>
        <?php if ($registered): ?>
        // Auto hide success toast after 5 seconds
        setTimeout(function() {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }
        }, 5000);
        <?php endif; ?>
        
        <?php if ($error): ?>
        // Auto hide error toast after 5 seconds
        setTimeout(function() {
            const toast = document.getElementById('errorToast');
            if (toast) {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }
        }, 5000);
        <?php endif; ?>

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.parentElement.querySelector('.toggle-password');
            
            if (field.type === 'password') {
                field.type = 'text';
                button.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg>';
            } else {
                field.type = 'password';
                button.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>';
            }
        }
    </script>
</body>
</html>
