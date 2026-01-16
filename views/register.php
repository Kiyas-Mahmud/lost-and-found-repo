<?php
// Load authentication controller
require_once __DIR__ . '/../controllers/auth.php';

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new AuthController();
    $result = $controller->register(
        trim($_POST['full_name']),
        trim($_POST['email']),
        trim($_POST['student_id']),
        trim($_POST['phone']),
        $_POST['password'],
        $_POST['confirm_password']
    );
    
    if ($result['success']) {
        $success = $result['message'];
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
            
            <form method="POST" action="" id="registerForm" novalidate>
                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                           required>
                    <span class="error-message" id="full_name-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="example@email.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                    <span class="error-message" id="email-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="student_id">Student ID <span class="required">*</span></label>
                    <input type="text" 
                           id="student_id" 
                           name="student_id" 
                           placeholder="Enter your student ID"
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" 
                           required>
                    <span class="error-message" id="student_id-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           placeholder="01XXXXXXXXX"
                           pattern="01[0-9]{9}"
                           maxlength="11"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                           required>
                    <small class="form-hint">11 digits starting with 01</small>
                    <span class="error-message" id="phone-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
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
                    <span class="error-message" id="password-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
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
                    <span class="error-message" id="confirm_password-error"></span>
                    <span class="success-message" id="confirm_password-success"></span>
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

        // Get form elements
        const registerForm = document.getElementById('registerForm');
        const fullNameInput = document.getElementById('full_name');
        const emailInput = document.getElementById('email');
        const studentIdInput = document.getElementById('student_id');
        const phoneInput = document.getElementById('phone');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        // Phone number validation - only allow digits
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.trim()) {
                clearError('phone');
            }
        });

        // Real-time validation for all fields
        fullNameInput.addEventListener('blur', function() {
            validateFullName();
        });

        fullNameInput.addEventListener('input', function() {
            if (this.value.trim()) {
                clearError('full_name');
            }
        });

        emailInput.addEventListener('blur', function() {
            validateEmail();
        });

        emailInput.addEventListener('input', function() {
            if (this.value.trim()) {
                clearError('email');
            }
        });

        studentIdInput.addEventListener('blur', function() {
            validateStudentId();
        });

        studentIdInput.addEventListener('input', function() {
            if (this.value.trim()) {
                clearError('student_id');
            }
        });

        phoneInput.addEventListener('blur', function() {
            validatePhone();
        });

        passwordInput.addEventListener('blur', function() {
            validatePassword();
        });

        passwordInput.addEventListener('input', function() {
            if (this.value) {
                clearError('password');
                // Check password match if confirm password is filled
                if (confirmPasswordInput.value) {
                    validatePasswordMatch();
                }
            }
        });

        // Real-time password match validation
        confirmPasswordInput.addEventListener('input', function() {
            validatePasswordMatch();
        });

        confirmPasswordInput.addEventListener('blur', function() {
            validateConfirmPassword();
        });

        // Form submission validation
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isFullNameValid = validateFullName();
            const isEmailValid = validateEmail();
            const isStudentIdValid = validateStudentId();
            const isPhoneValid = validatePhone();
            const isPasswordValid = validatePassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            const isPasswordMatch = validatePasswordMatch();

            if (isFullNameValid && isEmailValid && isStudentIdValid && 
                isPhoneValid && isPasswordValid && isConfirmPasswordValid && isPasswordMatch) {
                
                // Show loading state
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.querySelector('.btn-text').style.display = 'none';
                btn.querySelector('.btn-loader').style.display = 'flex';
                
                // Submit the form
                this.submit();
            }
        });

        // Validation functions
        function validateFullName() {
            const value = fullNameInput.value.trim();
            
            if (!value) {
                showError('full_name', 'Full name is required');
                return false;
            }

            if (value.length < 2) {
                showError('full_name', 'Full name must be at least 2 characters');
                return false;
            }

            clearError('full_name');
            return true;
        }

        function validateEmail() {
            const value = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!value) {
                showError('email', 'Email is required');
                return false;
            }

            if (!emailRegex.test(value)) {
                showError('email', 'Please enter a valid email address');
                return false;
            }

            clearError('email');
            return true;
        }

        function validateStudentId() {
            const value = studentIdInput.value.trim();

            if (!value) {
                showError('student_id', 'Student ID is required');
                return false;
            }

            if (value.length < 3) {
                showError('student_id', 'Student ID must be at least 3 characters');
                return false;
            }

            clearError('student_id');
            return true;
        }

        function validatePhone() {
            const value = phoneInput.value.trim();
            const phoneRegex = /^01[0-9]{9}$/;

            if (!value) {
                showError('phone', 'Phone number is required');
                return false;
            }

            if (!phoneRegex.test(value)) {
                showError('phone', 'Phone must be 11 digits starting with 01');
                return false;
            }

            clearError('phone');
            return true;
        }

        function validatePassword() {
            const value = passwordInput.value;

            if (!value) {
                showError('password', 'Password is required');
                return false;
            }

            if (value.length < 6) {
                showError('password', 'Password must be at least 6 characters');
                return false;
            }

            clearError('password');
            return true;
        }

        function validateConfirmPassword() {
            const value = confirmPasswordInput.value;

            if (!value) {
                showError('confirm_password', 'Please confirm your password');
                clearSuccess('confirm_password');
                return false;
            }

            return validatePasswordMatch();
        }

        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (!confirmPassword) {
                clearError('confirm_password');
                clearSuccess('confirm_password');
                return false;
            }

            if (password !== confirmPassword) {
                showError('confirm_password', 'Passwords do not match');
                clearSuccess('confirm_password');
                return false;
            }

            clearError('confirm_password');
            showSuccess('confirm_password', 'Passwords match');
            return true;
        }

        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            input.classList.add('input-error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function clearError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            input.classList.remove('input-error');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }

        function showSuccess(fieldId, message) {
            const successElement = document.getElementById(fieldId + '-success');
            if (successElement) {
                successElement.textContent = message;
                successElement.style.display = 'block';
            }
        }

        function clearSuccess(fieldId) {
            const successElement = document.getElementById(fieldId + '-success');
            if (successElement) {
                successElement.textContent = '';
                successElement.style.display = 'none';
            }
        }
    </script>
</body>
</html>
