<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/user.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $student_id = trim($_POST['student_id']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($full_name) || empty($email) || empty($student_id) || empty($phone) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (!preg_match('/^01[0-9]{9}$/', $phone)) {
        $error = 'Phone number must be 11 digits starting with 01';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $db = get_db_connection();
        $user = new User($db);
        $result = $user->register($full_name, $email, $student_id, $phone, $password);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php if ($success): ?>
        <div class="toast toast-success" id="successToast">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Registration successful! Redirecting to login...
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
                <h1>Create your account</h1>
                <p class="auth-subtitle">Please enter your details to register</p>
            </div>
            
            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="example@email.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" 
                           id="student_id" 
                           name="student_id" 
                           placeholder="Enter your student ID"
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           placeholder="01XXXXXXXXX"
                           pattern="01[0-9]{9}"
                           maxlength="11"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                           required>
                    <small class="form-hint">11 digits starting with 01</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Minimum 6 characters"
                               minlength="6"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="eye-open">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <small class="form-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-input">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Re-enter your password"
                               minlength="6"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="eye-open">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-text">Create Account</span>
                    <span class="btn-loader" style="display: none;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" fill="none" opacity="0.3"/>
                            <path d="M10 2a8 8 0 018 8" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                        </svg>
                        Creating...
                    </span>
                </button>
            </form>
            
            <p class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </p>
        </div>
    </div>

    <script>
        <?php if ($success): ?>
        // Auto hide toast and redirect after 2 seconds
        setTimeout(function() {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 300);
            }
        }, 2000);
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

        // Phone number validation
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Form submission with loading state
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'flex';
        });

        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
