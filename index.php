<?php
// Anti-bot protection
session_start();
if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $_SESSION['visit_time'] = time();
}

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com;");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Legend Shop - Premium Shopping Experience">
    <meta name="robots" content="noindex, nofollow">
    <title>👑 Legend Shop - About Us | Premium Shopping Platform</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Anti-Copy Protection CSS */
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        /* High Quality 8K Optimized Styles */
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
            font-smooth: always;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Ultra HD Background */
        .ultra-hd-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: radial-gradient(circle at 20% 50%, rgba(0, 255, 136, 0.15), transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(102, 126, 234, 0.15), transparent 50%),
                        radial-gradient(circle at 40% 20%, rgba(240, 147, 251, 0.15), transparent 50%),
                        linear-gradient(135deg, #0a0a0a, #1a1a2e, #0f3460, #16213e, #0a0a0a);
            background-size: 100% 100%, 100% 100%, 100% 100%, 400% 400%;
            animation: ultraHDShift 20s ease infinite;
        }

        @keyframes ultraHDShift {
            0%, 100% { background-position: 0% 0%, 0% 0%, 0% 0%, 0% 50%; }
            50% { background-position: 100% 100%, 100% 100%, 100% 100%, 100% 50%; }
        }

        /* 3D Particle System */
        #particleCanvas3D {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        /* Animated Gradient Overlay */
        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            background: 
                linear-gradient(45deg, transparent 30%, rgba(0, 255, 136, 0.03) 50%, transparent 70%),
                linear-gradient(-45deg, transparent 30%, rgba(102, 126, 234, 0.03) 50%, transparent 70%);
            background-size: 200% 200%;
            animation: gradientMove 15s ease infinite;
            pointer-events: none;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
        }

        /* Main Container */
        .main-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* About Section */
        .about-section {
            max-width: 1200px;
            width: 100%;
            text-align: center;
            animation: fadeInScale 1.5s cubic-bezier(0.19, 1, 0.22, 1) forwards;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(30px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Premium Badge */
        .premium-badge {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(102, 126, 234, 0.2));
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 50px;
            color: #00ff88;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.4),
                        inset 0 0 20px rgba(0, 255, 136, 0.1);
            animation: glowPulseIntense 3s ease infinite;
        }

        @keyframes glowPulseIntense {
            0%, 100% {
                box-shadow: 0 0 40px rgba(0, 255, 136, 0.4), inset 0 0 20px rgba(0, 255, 136, 0.1);
                transform: translateY(0);
            }
            50% {
                box-shadow: 0 0 80px rgba(0, 255, 136, 0.8), inset 0 0 40px rgba(0, 255, 136, 0.2);
                transform: translateY(-5px);
            }
        }

        /* Legendary Logo */
        .legendary-logo {
            width: 220px;
            height: 220px;
            margin: 0 auto 50px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.3), rgba(102, 126, 234, 0.3));
            border: 4px solid rgba(0, 255, 136, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 100px rgba(0, 255, 136, 0.6),
                        inset 0 0 50px rgba(0, 255, 136, 0.2);
            animation: logoRotateGlow 15s linear infinite;
            position: relative;
            backdrop-filter: blur(30px);
        }

        @keyframes logoRotateGlow {
            0% {
                transform: rotate(0deg);
                box-shadow: 0 0 100px rgba(0, 255, 136, 0.6), inset 0 0 50px rgba(0, 255, 136, 0.2);
            }
            50% {
                box-shadow: 0 0 150px rgba(0, 255, 136, 0.9), inset 0 0 70px rgba(0, 255, 136, 0.3);
            }
            100% {
                transform: rotate(360deg);
                box-shadow: 0 0 100px rgba(0, 255, 136, 0.6), inset 0 0 50px rgba(0, 255, 136, 0.2);
            }
        }

        .legendary-logo::before,
        .legendary-logo::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(0, 255, 136, 0.3);
        }

        .legendary-logo::before {
            width: 240px;
            height: 240px;
            animation: rippleOut 3s ease infinite;
        }

        .legendary-logo::after {
            width: 260px;
            height: 260px;
            animation: rippleOut 3s ease 1.5s infinite;
        }

        @keyframes rippleOut {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .legendary-logo i {
            font-size: 100px;
            background: linear-gradient(135deg, #00ff88, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: iconFloat 4s ease infinite;
            filter: drop-shadow(0 0 20px rgba(0, 255, 136, 0.8));
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }

        /* Main Title */
        .main-title {
            font-size: 84px;
            font-weight: 900;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #00ff88, #667eea, #f093fb, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 300% 300%;
            animation: rainbowFlow 8s ease infinite;
            line-height: 1.1;
            letter-spacing: -3px;
            text-shadow: 0 0 80px rgba(0, 255, 136, 0.5);
            filter: drop-shadow(0 0 30px rgba(0, 255, 136, 0.6));
        }

        @keyframes rainbowFlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Subtitle */
        .subtitle {
            font-size: 36px;
            color: #00ff88;
            margin-bottom: 30px;
            font-weight: 700;
            text-shadow: 0 0 30px rgba(0, 255, 136, 0.8);
            animation: subtitleGlow 4s ease infinite;
        }

        @keyframes subtitleGlow {
            0%, 100% {
                text-shadow: 0 0 30px rgba(0, 255, 136, 0.8);
                transform: scale(1);
            }
            50% {
                text-shadow: 0 0 50px rgba(0, 255, 136, 1);
                transform: scale(1.02);
            }
        }

        /* About Description */
        .about-description {
            max-width: 900px;
            margin: 0 auto 50px;
            font-size: 22px;
            line-height: 2;
            color: rgba(255, 255, 255, 0.9);
            text-align: justify;
            padding: 40px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.05), rgba(102, 126, 234, 0.05));
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: descriptionFloat 6s ease infinite;
        }

        @keyframes descriptionFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .about-description strong {
            color: #00ff88;
            font-weight: 800;
        }

        /* Features List */
        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 60px 0;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-item {
            padding: 30px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.08), rgba(102, 126, 234, 0.08));
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 25px;
            backdrop-filter: blur(20px);
            transition: all 0.4s ease;
            animation: featureAppear 0.8s ease backwards;
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }

        @keyframes featureAppear {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .feature-item:hover {
            transform: translateY(-10px) scale(1.05);
            border-color: rgba(0, 255, 136, 0.6);
            box-shadow: 0 30px 80px rgba(0, 255, 136, 0.4);
        }

        .feature-item i {
            font-size: 48px;
            color: #00ff88;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 20px rgba(0, 255, 136, 0.8));
        }

        .feature-item h3 {
            font-size: 22px;
            color: #00ff88;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .feature-item p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Call to Action Button */
        .cta-button {
            display: inline-block;
            padding: 25px 70px;
            font-size: 28px;
            font-weight: 900;
            text-decoration: none;
            color: #0a0a0a;
            background: linear-gradient(135deg, #00ff88, #00cc6a);
            border-radius: 60px;
            border: none;
            cursor: pointer;
            box-shadow: 0 20px 60px rgba(0, 255, 136, 0.6),
                        inset 0 0 30px rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            animation: ctaPulse 3s ease infinite;
        }

        @keyframes ctaPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 20px 60px rgba(0, 255, 136, 0.6), inset 0 0 30px rgba(255, 255, 255, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 30px 90px rgba(0, 255, 136, 0.9), inset 0 0 50px rgba(255, 255, 255, 0.5);
            }
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            50%, 100% { left: 150%; }
        }

        .cta-button:hover {
            transform: translateY(-8px) scale(1.1);
            box-shadow: 0 40px 120px rgba(0, 255, 136, 1),
                        inset 0 0 50px rgba(255, 255, 255, 0.5);
        }

        .cta-button i {
            margin-left: 15px;
            animation: arrowBounce 1.5s ease infinite;
        }

        @keyframes arrowBounce {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(10px); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-title {
                font-size: 48px;
            }
            
            .subtitle {
                font-size: 24px;
            }

            .about-description {
                font-size: 18px;
                padding: 25px;
                text-align: left;
            }

            .cta-button {
                padding: 20px 50px;
                font-size: 22px;
            }

            .legendary-logo {
                width: 160px;
                height: 160px;
            }

            .legendary-logo i {
                font-size: 70px;
            }
        }

        /* Protection Overlay (invisible but functional) */
        .protection-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Ultra HD Background -->
    <div class="ultra-hd-background"></div>

    <!-- 3D Particle Canvas -->
    <canvas id="particleCanvas3D"></canvas>

    <!-- Gradient Overlay -->
    <div class="gradient-overlay"></div>

    <!-- Protection Layer -->
    <div class="protection-layer"></div>

    <!-- Main Container -->
    <div class="main-container">
        <div class="about-section">
            <!-- Premium Badge -->
            <div class="premium-badge">
                🏆 Premium Platform
            </div>

            <!-- Legendary Logo -->
            <div class="legendary-logo">
                <i class="fas fa-crown"></i>
            </div>

            <!-- Main Title -->
            <h1 class="main-title">LEGEND SHOP</h1>

            <!-- Subtitle -->
            <p class="subtitle">Where Legends Shop</p>

            <!-- About Description -->
            <div class="about-description">
                Welcome to <strong>Legend Shop</strong>, the ultimate destination for premium shopping experiences. 
                We are a <strong>next-generation e-commerce platform</strong> built with cutting-edge technology 
                and military-grade security. Our mission is to provide our customers with an unparalleled shopping 
                experience powered by <strong>AI technology</strong>, <strong>advanced encryption</strong>, and 
                <strong>24/7 customer support</strong>.
                <br><br>
                Founded with the vision of revolutionizing online shopping, Legend Shop combines 
                <strong>lightning-fast performance</strong>, <strong>DDoS protection</strong>, and 
                <strong>anti-dump security</strong> to ensure your data and transactions are always safe. 
                Join thousands of satisfied customers who trust Legend Shop for their premium shopping needs.
            </div>

            <!-- Features List -->
            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-shield-virus"></i>
                    <h3>Military Security</h3>
                    <p>AES-256 encryption with DDoS protection</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-rocket"></i>
                    <h3>Ultra Fast</h3>
                    <p>Lightning speed with CDN delivery</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    <h3>Anti-Dump</h3>
                    <p>Advanced code protection system</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Always here when you need us</p>
                </div>
            </div>

            <!-- CTA Button -->
            <a href="signup.html" class="cta-button">
                Get Started
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <script>
        // ==================== EXTREME PROTECTION ====================
        
        // Disable right-click
        document.addEventListener('contextmenu', e => {
            e.preventDefault();
            return false;
        });

        // Disable all keyboard shortcuts
        document.addEventListener('keydown', e => {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S, Ctrl+Shift+C
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S'))) {
                e.preventDefault();
                return false;
            }
        });

        // Detect DevTools
        const devtoolsDetector = () => {
            const threshold = 160;
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            
            if (widthThreshold || heightThreshold) {
                document.body.innerHTML = '<h1 style="color:#ff0000;text-align:center;margin-top:50vh;">⚠️ Developer tools detected!</h1>';
            }
        };
        setInterval(devtoolsDetector, 1000);

        // Disable text selection
        document.onselectstart = () => false;
        document.ondragstart = () => false;

        // Console warning
        console.log('%c⛔ STOP!', 'color: red; font-size: 60px; font-weight: bold;');
        console.log('%cThis is a browser feature intended for developers.', 'font-size: 20px;');
        console.log('%cIf someone told you to copy-paste something here, it is a scam!', 'font-size: 18px; color: red;');
        console.log('%c🛡️ This website is protected by Legend Shop Security System', 'font-size: 16px; color: #00ff88;');

        // Clear console repeatedly
        setInterval(() => {
            console.clear();
        }, 2000);

        // Prevent Ctrl+A
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
            }
        });

        // Obfuscate page source
        (() => {
            const originalHTML = document.documentElement.innerHTML;
            Object.defineProperty(document, 'innerHTML', {
                get: () => '<!-- Protected by Legend Shop -->',
                set: () => {}
            });
        })();

        // ==================== 3D PARTICLE SYSTEM ====================
        const canvas = document.getElementById('particleCanvas3D');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        class Particle3D {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.z = Math.random() * 1000;
                this.size = Math.random() * 3 + 1;
                this.speedX = (Math.random() - 0.5) * 2;
                this.speedY = (Math.random() - 0.5) * 2;
                this.speedZ = Math.random() * 3 + 1;
                this.hue = Math.random() * 60 + 120; // Green to blue hues
            }

            update() {
                this.z -= this.speedZ;
                if (this.z <= 0) {
                    this.z = 1000;
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                }

                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }

            draw() {
                const scale = 1000 / (1000 + this.z);
                const x2d = (this.x - canvas.width / 2) * scale + canvas.width / 2;
                const y2d = (this.y - canvas.height / 2) * scale + canvas.height / 2;
                const size = this.size * scale;

                const opacity = (1000 - this.z) / 1000;
                ctx.fillStyle = `hsla(${this.hue}, 100%, 50%, ${opacity * 0.8})`;
                ctx.shadowBlur = 20;
                ctx.shadowColor = `hsla(${this.hue}, 100%, 50%, ${opacity})`;
                
                ctx.beginPath();
                ctx.arc(x2d, y2d, size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        const particles = [];
        for (let i = 0; i < 150; i++) {
            particles.push(new Particle3D());
        }

        function animate() {
            ctx.fillStyle = 'rgba(10, 10, 10, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });

            // Connect nearby particles
            particles.forEach((p1, i) => {
                particles.slice(i + 1).forEach(p2 => {
                    const dx = p1.x - p2.x;
                    const dy = p1.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 150) {
                        const opacity = (150 - dist) / 150;
                        ctx.strokeStyle = `rgba(0, 255, 136, ${opacity * 0.3})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(p1.x, p1.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                });
            });

            requestAnimationFrame(animate);
        }

        animate();

        // Resize handler
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Anti-debugger
        (function() {
            setInterval(() => {
                (function() {
                    return false;
                })['constructor']('debugger')['call']();
            }, 50);
        })();

        // Prevent viewing source
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
                e.preventDefault();
                alert('⚠️ Source code viewing is disabled for security reasons!');
                return false;
            }
        });
        
        // Auto-redirect if already logged in
        (function() {
            const token = localStorage.getItem('legendShopToken');
            const user = localStorage.getItem('legendShopUser');
            
            if (token && user) {
                // User is already logged in, redirect to dashboard
                window.location.href = 'dashboard.html';
            }
        })();
    </script>
</body>
</html>
