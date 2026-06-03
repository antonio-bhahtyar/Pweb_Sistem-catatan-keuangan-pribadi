// assets/js/auth.js - Login & Register page utilities

document.addEventListener('DOMContentLoaded', function() {

    // === Password Toggle ===
    var toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var pwd = document.getElementById('password');
            var icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    }

    // === Konfirmasi Password (Register) ===
    var password2 = document.getElementById('password2');
    if (password2) {
        password2.addEventListener('input', function() {
            var pwd = document.getElementById('password');
            var msg = document.getElementById('passwordMatchMsg');
            if (!msg) return;
            if (pwd.value === password2.value) {
                msg.textContent = 'Password cocok';
                msg.className = 'text-success small';
            } else {
                msg.textContent = 'Password tidak cocok';
                msg.className = 'text-danger small';
            }
        });
    }

    // === Password Strength Indicator ===
    var passwordInput = document.getElementById('password');
    var strengthBar = document.getElementById('passwordStrength');
    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function() {
            var val = passwordInput.value;
            var strength = 0;
            if (val.length >= 6) strength++;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            var colors = ['#dc3545', '#dc3545', '#ffc107', '#198754', '#198754'];
            var texts = ['Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            var idx = Math.min(strength, 4);

            strengthBar.style.width = ((idx + 1) * 20) + '%';
            strengthBar.style.backgroundColor = colors[idx];
            strengthBar.textContent = texts[idx];
        });
    }
});
