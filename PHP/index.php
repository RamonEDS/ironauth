<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKYREC-Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="sualogo.png" type="image/png">
    <style>
        :root {
            --primary-color: #1a73e8;
            --primary-accent: #34c759;
            --danger-color: #ff4444;
            --background-dark: #1e1e2f;
            --text-light: #ffffff;
            --input-bg: #2a2a3d;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body.login-page {
            background: linear-gradient(135deg, #1e1e2f, #121223);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

      
        #particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            width: 205px;
            margin-bottom: 16px;
            transition: transform 0.3s ease;
        }

        .login-logo:hover {
            transform: scale(1.1);
        }

        .login-header h2 {
            color: var(--text-light);
            font-size: 24px;
            margin-bottom: 5px;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 18px;
        }

        .login-input {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border: none;
            border-radius: 10px;
            background: var(--input-bg);
            color: var(--text-light);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background: rgba(255, 255, 255, 0.1);
        }

        .login-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: var(--text-light);
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            background: #1557b0;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .login-button:active {
            transform: scale(0.95);
        }

       
        .login-button .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 10px;
            height: 10px;
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(20);
                opacity: 0;
            }
        }

        .save-credentials {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 10px;
        }

        .save-credentials input {
            cursor: pointer;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #1557b0;
        }

        .toast-message {
            border-radius: 8px;
            font-size: 14px;
            padding: 12px;
        }
    </style>
</head>
<body class="login-page">
    <canvas id="particles"></canvas>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="logo2.png" alt="Logo Predator" class="login-logo">
                <h2>SKYREC Dashboard</h2>
                <p>Acesse o painel administrativo</p>
            </div>
            
            <form id="loginForm" class="login-form">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="admin-username" placeholder="Username" autocomplete="off" class="login-input">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="admin-password" placeholder="Password" autocomplete="off" class="login-input">
                </div>
                <div class="save-credentials">
                    <input type="checkbox" id="save-credentials" checked>
                    <label for="save-credentials">Salvar credenciais</label>
                </div>
                <button type="button" id="loginButton" class="login-button">
                    <span>Entrar</span>
                    <i class="fas fa-arrow-right button-icon"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <p>Problemas para acessar? <a href="https://discord.gg/hzinvendas">Contate o desenvolvedor</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Particle Background
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 0.5 - 0.25;
                this.speedY = Math.random() * 0.5 - 0.25;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.size > 0.2) this.size -= 0.01;
            }
            draw() {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function initParticles() {
            for (let i = 0; i < 50; i++) {
                particles.push(new Particle());
            }
        }

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach((particle, index) => {
                particle.update();
                particle.draw();
                if (particle.size <= 0.2) {
                    particles.splice(index, 1);
                    particles.push(new Particle());
                }
            });
            requestAnimationFrame(animateParticles);
        }

        initParticles();
        animateParticles();

        
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        
        document.getElementById('loginButton').addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
            loginAdmin();
        });

      
        function showToast(message, success = true) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: success ? "var(--primary-accent)" : "var(--danger-color)",
                stopOnFocus: true,
                className: "toast-message",
                offset: {
                    x: 20,
                    y: 70
                }
            }).showToast();
        }

       
        document.addEventListener('DOMContentLoaded', () => {
            const savedUsername = localStorage.getItem('admin-username');
            const savedPassword = localStorage.getItem('admin-password');
            if (savedUsername && savedPassword) {
                document.getElementById('admin-username').value = savedUsername;
                document.getElementById('admin-password').value = savedPassword;
            }
        });

       
        async function loginAdmin() {
            const username = document.getElementById('admin-username').value;
            const password = document.getElementById('admin-password').value;
            const saveCredentials = document.getElementById('save-credentials').checked;
            const button = document.getElementById('loginButton');

            if (!username || !password) {
                showToast('Por favor, preencha todos os campos!', false);
                return;
            }

            
            if (saveCredentials) {
                localStorage.setItem('admin-username', username);
                localStorage.setItem('admin-password', password);
            } else {
                localStorage.removeItem('admin-username');
                localStorage.removeItem('admin-password');
            }

           
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            try {
                const response = await fetch('admin_login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const data = await response.json();
                
                showToast(data.message, data.success);
                
                if (data.success) {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    button.innerHTML = '<span>Entrar</span><i class="fas fa-arrow-right button-icon"></i>';
                    button.disabled = false;
                }
            } catch (error) {
                showToast('Erro ao conectar ao servidor!', false);
                button.innerHTML = '<span>Entrar</span><i class="fas fa-arrow-right button-icon"></i>';
                button.disabled = false;
            }
        }

      
        document.getElementById('loginForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginButton').click();
            }
        });
    </script>
</body>
</html>