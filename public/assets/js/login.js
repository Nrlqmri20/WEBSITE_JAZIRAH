// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const toggleIcon = this;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
});

// Loading animation on form submit
document.getElementById('loginForm').addEventListener('submit', function () {
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.classList.add('btn-loading');
    loginBtn.disabled = true;
});

// Auto focus pada input username
document.querySelector('input[name="username"]').focus();
