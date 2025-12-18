// ===== CAPTCHA SYSTEM =====
let captchaAnswer = 0;

function generateCaptcha() {
    const operations = [
        { symbol: '+', func: (a, b) => a + b, range: [1, 30] },
        { symbol: '-', func: (a, b) => a - b, range: [10, 30] },
        { symbol: '×', func: (a, b) => a * b, range: [1, 12] }
    ];
    
    const operation = operations[Math.floor(Math.random() * operations.length)];
    let num1 = Math.floor(Math.random() * (operation.range[1] - operation.range[0] + 1)) + operation.range[0];
    let num2 = Math.floor(Math.random() * (operation.range[1] - operation.range[0] + 1)) + operation.range[0];
    
    // For subtraction, ensure positive result
    if (operation.symbol === '-' && num1 < num2) {
        [num1, num2] = [num2, num1];
    }
    
    captchaAnswer = operation.func(num1, num2);
    
    const questionElement = document.getElementById('captchaQuestion');
    if (questionElement) {
        // Use textContent so it always renders (no HTML dependency)
        questionElement.textContent = `${num1} ${operation.symbol} ${num2} = ?`;
    }
    
    const captchaInput = document.getElementById('captcha');
    if (captchaInput) {
        captchaInput.value = '';
    }
}

function validateCaptcha() {
    const captchaInput = document.getElementById('captcha');
    if (!captchaInput || !captchaInput.value) return false;
    const userAnswer = parseInt(captchaInput.value);
    return !isNaN(userAnswer) && userAnswer === captchaAnswer;
}

// ===== PASSWORD VISIBILITY TOGGLE =====
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const wrapper = input.closest('.input-wrapper');
    const icon = wrapper ? wrapper.querySelector('.toggle-password i') : null;
    
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

// ===== PASSWORD STRENGTH CHECKER =====
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

function updatePasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    if (!passwordInput || !strengthFill || !strengthText) return;
    
    const password = passwordInput.value;
    const strength = checkPasswordStrength(password);
    
    strengthFill.classList.remove('weak', 'medium', 'strong');
    strengthText.classList.remove('weak', 'medium', 'strong');
    
    if (password.length === 0) {
        strengthFill.style.width = '0%';
        strengthText.textContent = 'Password strength';
        return;
    }
    
    if (strength <= 2) {
        strengthFill.style.width = '33%';
        strengthFill.classList.add('weak');
        strengthText.classList.add('weak');
        strengthText.textContent = 'Weak password';
    } else if (strength <= 4) {
        strengthFill.style.width = '66%';
        strengthFill.classList.add('medium');
        strengthText.classList.add('medium');
        strengthText.textContent = 'Medium password';
    } else {
        strengthFill.style.width = '100%';
        strengthFill.classList.add('strong');
        strengthText.classList.add('strong');
        strengthText.textContent = 'Strong password';
    }
}

// ===== EMAIL VALIDATION =====
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// ===== API CONFIGURATION =====
const API_URL = (location && location.origin && location.origin.startsWith('http'))
    ? `${location.origin}/api`
    : 'http://localhost:3000/api';

async function checkBackendHealth() {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 2500);
    try {
        const res = await fetch(`${API_URL}/health`, { signal: controller.signal });
        clearTimeout(timeout);
        if (!res.ok) return false;
        const data = await res.json().catch(() => null);
        return !!data && data.status === 'OK';
    } catch {
        clearTimeout(timeout);
        return false;
    }
}

function warnIfFileProtocol() {
    if (location && location.protocol === 'file:') {
        showErrorMessage('Please open via http://localhost:3000 so backend works properly.');
    }
}

// ===== UI HELPERS =====
function showLoading(button) {
    button.classList.add('loading');
    button.disabled = true;
}

function hideLoading(button) {
    button.classList.remove('loading');
    button.disabled = false;
}

function showSuccessMessage(message) {
    const form = document.querySelector('.auth-form');
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    form.insertBefore(successDiv, form.firstChild);
    setTimeout(() => successDiv.remove(), 3000);
}

function showErrorMessage(message) {
    const form = document.querySelector('.auth-form');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message-banner';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    form.insertBefore(errorDiv, form.firstChild);
    setTimeout(() => errorDiv.remove(), 4000);
}

// ===== ADVANCED UI: 3D CARD TILT =====
function initCardTilt() {
    // Only disable on small mobile screens
    if (window.innerWidth < 768) return;
    
    const cards = document.querySelectorAll('[data-tilt-card]');
    
    cards.forEach(card => {
        const maxTilt = 12;
        const scale = 1.03;
        
        // Remove any conflicting CSS transitions
        card.style.transition = 'none';
        card.style.transform = 'perspective(1500px) rotateX(0deg) rotateY(0deg) scale(1)';
        
        const handleMouseMove = (e) => {
            const rect = card.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const mouseX = e.clientX - centerX;
            const mouseY = e.clientY - centerY;
            
            const rotateY = (mouseX / (rect.width / 2)) * maxTilt;
            const rotateX = -(mouseY / (rect.height / 2)) * maxTilt;
            
            card.style.transition = 'transform 0.1s ease-out';
            card.style.transform = `perspective(1500px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale(${scale})`;
        };
        
        const handleMouseLeave = () => {
            card.style.transition = 'transform 0.6s cubic-bezier(0.23, 1, 0.32, 1)';
            card.style.transform = 'perspective(1500px) rotateX(0deg) rotateY(0deg) scale(1)';
        };
        
        const handleMouseEnter = () => {
            card.style.transition = 'transform 0.1s ease-out';
        };
        
        card.addEventListener('mousemove', handleMouseMove);
        card.addEventListener('mouseleave', handleMouseLeave);
        card.addEventListener('mouseenter', handleMouseEnter);
    });
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    initCardTilt();
    warnIfFileProtocol();

    // Backend health check (prevents vague 'Server error' messages)
    checkBackendHealth().then((ok) => {
        if (!ok) {
            showErrorMessage('Backend is OFF. Run `npm start` in the project directory, then open http://localhost:3000');
            document.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.title = 'Start backend server first (npm start)';
            });
        }
    });

    // Password strength listener
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    // Only wire strength UI on the signup page (where the elements exist)
    if (passwordInput && strengthFill && strengthText) {
        passwordInput.addEventListener('input', updatePasswordStrength);
        passwordInput.addEventListener('keyup', updatePasswordStrength);
        passwordInput.addEventListener('focus', updatePasswordStrength);
        updatePasswordStrength();
    }

    // DOB Max Date Setup
    const dobInput = document.getElementById('dob');
    if (dobInput) {
        const today = new Date();
        const thirteenYearsAgo = new Date();
        thirteenYearsAgo.setFullYear(today.getFullYear() - 13);
        dobInput.setAttribute('max', thirteenYearsAgo.toISOString().split('T')[0]);
    }

    // CAPTCHA Setup - more robust initialization
    const captchaElement = document.getElementById('captchaQuestion');
    if (captchaElement) {
        // Generate immediately
        generateCaptcha();
        
        // Also try on window load
        window.addEventListener('load', () => {
            const q = document.getElementById('captchaQuestion');
            if (q && !q.textContent.trim()) {
                generateCaptcha();
            }
        });
        
        // Retry a few times for slow renders
        [100, 300, 600, 1000].forEach(delay => {
            setTimeout(() => {
                const q = document.getElementById('captchaQuestion');
                if (q && !q.textContent.trim()) {
                    generateCaptcha();
                }
            }, delay);
        });
    }

    // Login Form Handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Double-check backend right before submit
            if (!(await checkBackendHealth())) {
                showErrorMessage('Backend is not reachable. Start it with `npm start` first.');
                return;
            }
            
            // Custom human verification (simple checkbox)
            const humanCheck = document.getElementById('humanCheck');
            if (humanCheck && !humanCheck.checked) {
                showErrorMessage('Please confirm you are human (checkbox)');
                return;
            }

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Invalid email format';
                return;
            }

            const submitBtn = this.querySelector('.btn-submit');
            showLoading(submitBtn);

            try {
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, humanVerified: humanCheck ? !!humanCheck.checked : true })
                });
                const data = await response.json();
                hideLoading(submitBtn);

                if (data.success) {
                    localStorage.setItem('legendShopToken', data.token);
                    if (data.user) {
                        localStorage.setItem('legendShopUser', JSON.stringify(data.user));
                    }
                    showSuccessMessage('Login successful! Redirecting...');
                    setTimeout(() => { window.location.href = 'dashboard.html'; }, 1200);
                } else {
                    showErrorMessage(data.message || 'Login failed');
                }
            } catch (err) {
                hideLoading(submitBtn);
                showErrorMessage('Cannot reach backend. Make sure `npm start` is running on http://localhost:3000');
            }
        });
    }

    // Signup Form Handler
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Double-check backend right before submit
            if (!(await checkBackendHealth())) {
                showErrorMessage('Backend is not reachable. Start it with `npm start` first.');
                return;
            }

            // Custom human verification (simple checkbox)
            const humanCheck = document.getElementById('humanCheck');
            if (humanCheck && !humanCheck.checked) {
                showErrorMessage('Please confirm you are human (checkbox)');
                return;
            }

            if (!validateCaptcha()) {
                document.getElementById('captchaError').textContent = 'Incorrect CAPTCHA answer';
                generateCaptcha();
                return;
            }

            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                dateOfBirth: document.getElementById('dob').value
            };

            if (!document.getElementById('terms').checked) {
                showErrorMessage('You must accept the terms');
                return;
            }

            const submitBtn = this.querySelector('.btn-submit');
            showLoading(submitBtn);

            try {
                const response = await fetch(`${API_URL}/signup`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();
                hideLoading(submitBtn);

                if (data.success) {
                    showSuccessMessage('Account created! Redirecting to login...');
                    setTimeout(() => { window.location.href = 'login.html'; }, 2000);
                } else {
                    showErrorMessage(data.message || 'Signup failed');
                    generateCaptcha();
                }
            } catch (err) {
                hideLoading(submitBtn);
                showErrorMessage('Cannot reach backend. Make sure `npm start` is running on http://localhost:3000');
                generateCaptcha();
            }
        });
    }
});

// Handle bfcache (back/forward) restores
window.addEventListener('pageshow', () => {
    if (document.getElementById('captchaQuestion')) {
        const q = document.getElementById('captchaQuestion');
        if (q && !q.textContent.trim()) generateCaptcha();
    }
    if (document.getElementById('strengthFill') && document.getElementById('strengthText')) {
        updatePasswordStrength();
    }
});
