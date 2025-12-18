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

        /* ==================== SPACE JOURNEY INTRO SEQUENCE ==================== */
        
        /* Intro Overlay - Full screen space journey */
        #intro-sequence {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            background: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: introFadeOut 1.5s ease 16s forwards;
            pointer-events: all;
            overflow: hidden;
        }

        #intro-sequence.hidden {
            display: none;
        }

        @keyframes introFadeOut {
            to {
                opacity: 0;
                pointer-events: none;
            }
        }

        /* Space Canvas for Solar System */
        #spaceCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Skip Button */
        .intro-skip-button {
            position: absolute;
            top: 40px;
            right: 40px;
            padding: 15px 35px;
            background: rgba(0, 255, 136, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 50px;
            color: #00ff88;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            animation: introSkipReveal 1s ease 1s forwards;
            opacity: 0;
            z-index: 10;
        }

        @keyframes introSkipReveal {
            to { opacity: 1; }
        }

        .intro-skip-button:hover {
            background: rgba(0, 255, 136, 0.3);
            border-color: rgba(0, 255, 136, 0.9);
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.6);
            transform: translateY(-3px);
        }

        /* Loading Text */
        .intro-loading-text {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.9);
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            z-index: 10;
            animation: loadingPulse 2s ease infinite;
        }

        @keyframes loadingPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* Journey Title */
        .journey-title {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 80px;
            font-weight: 900;
            color: transparent;
            background: linear-gradient(135deg, #00ff88, #667eea, #f093fb, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            opacity: 0;
            z-index: 10;
            text-align: center;
            white-space: nowrap;
            animation: titleFadeIn 2s ease 13s forwards;
            filter: drop-shadow(0 0 40px rgba(0, 255, 136, 0.8));
        }

        @keyframes titleFadeIn {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Hide main content during intro with black background */
        body.intro-playing {
            background: #000000;
        }

        body.intro-playing .main-container {
            opacity: 0;
            pointer-events: none;
        }

        /* Main content background after intro */
        body.intro-complete {
            background: #000000;
        }

        body.intro-complete .ultra-hd-background,
        body.intro-complete .gradient-overlay,
        body.intro-complete .light-rays {
            display: none;
        }

        body.intro-complete .main-container {
            background: #000000;
        }
            max-width: 600px;
            animation: introProgressReveal 1s ease 6s forwards;
            opacity: 0;
        }

        /* Ultra HD 8K Background with Auto-Changing Colors */
        .ultra-hd-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 255, 136, 0.2), transparent 60%),
                radial-gradient(circle at 80% 80%, rgba(102, 126, 234, 0.2), transparent 60%),
                radial-gradient(circle at 40% 20%, rgba(240, 147, 251, 0.2), transparent 60%),
                radial-gradient(circle at 60% 70%, rgba(255, 107, 107, 0.15), transparent 55%),
                radial-gradient(circle at 90% 30%, rgba(72, 219, 251, 0.15), transparent 55%),
                radial-gradient(circle at 10% 90%, rgba(254, 202, 87, 0.15), transparent 55%),
                linear-gradient(135deg, 
                    #0a0a0a, #1a1a2e, #0f3460, #16213e, 
                    #1a1a40, #2d1b69, #150050, #0a0a0a);
            background-size: 150% 150%, 150% 150%, 150% 150%, 200% 200%, 200% 200%, 200% 200%, 800% 800%;
            animation: ultraHDShift8K 30s ease infinite, colorCycle 45s ease infinite;
            filter: saturate(1.3) contrast(1.1);
        }

        @keyframes ultraHDShift8K {
            0%, 100% { 
                background-position: 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 50%; 
            }
            25% { 
                background-position: 30% 30%, 70% 20%, 50% 80%, 20% 90%, 80% 10%, 10% 70%, 25% 50%; 
            }
            50% { 
                background-position: 100% 100%, 100% 100%, 100% 100%, 50% 50%, 80% 80%, 20% 20%, 100% 50%; 
            }
            75% { 
                background-position: 70% 20%, 30% 80%, 90% 40%, 80% 10%, 20% 90%, 90% 30%, 75% 50%; 
            }
        }

        @keyframes colorCycle {
            0% { filter: saturate(1.3) contrast(1.1) hue-rotate(0deg); }
            25% { filter: saturate(1.4) contrast(1.15) hue-rotate(30deg); }
            50% { filter: saturate(1.5) contrast(1.2) hue-rotate(60deg); }
            75% { filter: saturate(1.4) contrast(1.15) hue-rotate(30deg); }
            100% { filter: saturate(1.3) contrast(1.1) hue-rotate(0deg); }
        }

        /* Advanced 3D Particle System with Depth */
        #particleCanvas3D {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            filter: blur(0.5px) brightness(1.2);
            mix-blend-mode: screen;
        }

        /* Animated Gradient Overlay with Multi-Layer Effects */
        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            background: 
                linear-gradient(45deg, transparent 30%, rgba(0, 255, 136, 0.05) 50%, transparent 70%),
                linear-gradient(-45deg, transparent 30%, rgba(102, 126, 234, 0.05) 50%, transparent 70%),
                linear-gradient(135deg, transparent 20%, rgba(240, 147, 251, 0.04) 50%, transparent 80%),
                radial-gradient(ellipse at center, transparent 40%, rgba(255, 107, 107, 0.03) 70%, transparent);
            background-size: 400% 400%, 400% 400%, 300% 300%, 200% 200%;
            animation: gradientMove 20s ease infinite, gradientShift 35s ease infinite;
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 0%, 100% 100%, 50% 50%, 50% 50%; }
            25% { background-position: 50% 50%, 50% 50%, 100% 0%, 30% 70%; }
            50% { background-position: 100% 100%, 0% 0%, 0% 100%, 70% 30%; }
            75% { background-position: 50% 50%, 50% 50%, 50% 50%, 50% 50%; }
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        /* Cinematic Light Rays */
        .light-rays {
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            z-index: 3;
            background: 
                linear-gradient(to bottom, transparent 0%, rgba(0, 255, 136, 0.02) 50%, transparent 100%),
                linear-gradient(to right, transparent 0%, rgba(102, 126, 234, 0.02) 50%, transparent 100%),
                linear-gradient(135deg, transparent 30%, rgba(240, 147, 251, 0.03) 50%, transparent 70%);
            animation: lightRaysSweep 25s ease infinite;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        @keyframes lightRaysSweep {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-10%, -10%) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Lens Flares for 8K Cinematic Effect */
        .lens-flare {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 4;
            filter: blur(40px);
            mix-blend-mode: screen;
            opacity: 0.6;
        }

        .lens-flare-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 255, 136, 0.4), transparent 70%);
            top: 10%;
            left: 15%;
            animation: lensFlare1 20s ease infinite;
        }

        .lens-flare-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.4), transparent 70%);
            bottom: 20%;
            right: 20%;
            animation: lensFlare2 25s ease infinite;
        }

        .lens-flare-3 {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(240, 147, 251, 0.3), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: lensFlare3 30s ease infinite;
        }

        @keyframes lensFlare1 {
            0%, 100% { 
                transform: translate(0, 0) scale(1);
                opacity: 0.6;
                filter: blur(40px) hue-rotate(0deg);
            }
            50% { 
                transform: translate(50px, -50px) scale(1.5);
                opacity: 0.9;
                filter: blur(60px) hue-rotate(30deg);
            }
        }

        @keyframes lensFlare2 {
            0%, 100% { 
                transform: translate(0, 0) scale(1);
                opacity: 0.5;
                filter: blur(40px) hue-rotate(0deg);
            }
            50% { 
                transform: translate(-80px, 60px) scale(1.8);
                opacity: 0.8;
                filter: blur(70px) hue-rotate(45deg);
            }
        }

        @keyframes lensFlare3 {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.4;
                filter: blur(50px) hue-rotate(0deg);
            }
            33% { 
                transform: translate(-30%, -60%) scale(1.4);
                opacity: 0.7;
                filter: blur(65px) hue-rotate(20deg);
            }
            66% { 
                transform: translate(-70%, -40%) scale(1.6);
                opacity: 0.8;
                filter: blur(75px) hue-rotate(40deg);
            }
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

        /* Premium Badge with 8K Glow */
        .premium-badge {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, 
                rgba(0, 255, 136, 0.3), rgba(102, 126, 234, 0.3), 
                rgba(240, 147, 251, 0.3), rgba(255, 107, 107, 0.3));
            background-size: 300% 300%;
            border: 2px solid rgba(0, 255, 136, 0.6);
            border-radius: 50px;
            color: #00ff88;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 40px;
            backdrop-filter: blur(30px) saturate(180%);
            box-shadow: 
                0 0 40px rgba(0, 255, 136, 0.5),
                0 0 80px rgba(102, 126, 234, 0.3),
                0 0 120px rgba(240, 147, 251, 0.2),
                inset 0 0 20px rgba(0, 255, 136, 0.2);
            animation: glowPulseIntense8K 4s ease infinite, badgeShift 20s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .premium-badge::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: rotate(45deg);
            animation: badgeShine 3s infinite;
        }

        @keyframes glowPulseIntense8K {
            0%, 100% {
                box-shadow: 
                    0 0 40px rgba(0, 255, 136, 0.5),
                    0 0 80px rgba(102, 126, 234, 0.3),
                    inset 0 0 20px rgba(0, 255, 136, 0.2);
                transform: translateY(0) scale(1);
                filter: hue-rotate(0deg);
            }
            50% {
                box-shadow: 
                    0 0 80px rgba(0, 255, 136, 0.9),
                    0 0 120px rgba(102, 126, 234, 0.6),
                    0 0 160px rgba(240, 147, 251, 0.4),
                    inset 0 0 40px rgba(0, 255, 136, 0.4);
                transform: translateY(-5px) scale(1.05);
                filter: hue-rotate(30deg);
            }
        }

        @keyframes badgeShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes badgeShine {
            0% { left: -100%; }
            100% { left: 150%; }
        }

        /* Legendary Logo with Advanced 8K Effects */
        .legendary-logo {
            width: 220px;
            height: 220px;
            margin: 0 auto 50px;
            background: 
                linear-gradient(135deg, rgba(0, 255, 136, 0.4), rgba(102, 126, 234, 0.4)),
                radial-gradient(circle at 30% 30%, rgba(240, 147, 251, 0.3), transparent 70%);
            border: 4px solid rgba(0, 255, 136, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 0 100px rgba(0, 255, 136, 0.7),
                0 0 150px rgba(102, 126, 234, 0.5),
                0 0 200px rgba(240, 147, 251, 0.3),
                inset 0 0 50px rgba(0, 255, 136, 0.3),
                inset 0 0 80px rgba(102, 126, 234, 0.2);
            animation: logoRotateGlow8K 20s linear infinite, logoPulse8K 8s ease infinite;
            position: relative;
            backdrop-filter: blur(40px) saturate(180%);
            transform-style: preserve-3d;
        }

        @keyframes logoRotateGlow8K {
            0% {
                transform: rotate(0deg) scale(1);
                box-shadow: 
                    0 0 100px rgba(0, 255, 136, 0.7),
                    0 0 150px rgba(102, 126, 234, 0.5),
                    0 0 200px rgba(240, 147, 251, 0.3),
                    inset 0 0 50px rgba(0, 255, 136, 0.3);
                filter: hue-rotate(0deg);
            }
            25% {
                box-shadow: 
                    0 0 120px rgba(102, 126, 234, 0.9),
                    0 0 180px rgba(240, 147, 251, 0.6),
                    0 0 220px rgba(0, 255, 136, 0.4),
                    inset 0 0 70px rgba(102, 126, 234, 0.4);
                filter: hue-rotate(15deg);
            }
            50% {
                transform: rotate(180deg) scale(1.05);
                box-shadow: 
                    0 0 140px rgba(240, 147, 251, 0.9),
                    0 0 200px rgba(0, 255, 136, 0.6),
                    0 0 240px rgba(102, 126, 234, 0.4),
                    inset 0 0 80px rgba(240, 147, 251, 0.4);
                filter: hue-rotate(30deg);
            }
            75% {
                box-shadow: 
                    0 0 120px rgba(255, 107, 107, 0.8),
                    0 0 180px rgba(72, 219, 251, 0.5),
                    0 0 220px rgba(254, 202, 87, 0.3),
                    inset 0 0 70px rgba(255, 107, 107, 0.3);
                filter: hue-rotate(15deg);
            }
            100% {
                transform: rotate(360deg) scale(1);
                box-shadow: 
                    0 0 100px rgba(0, 255, 136, 0.7),
                    0 0 150px rgba(102, 126, 234, 0.5),
                    0 0 200px rgba(240, 147, 251, 0.3),
                    inset 0 0 50px rgba(0, 255, 136, 0.3);
                filter: hue-rotate(0deg);
            }
        }

        @keyframes logoPulse8K {
            0%, 100% {
                transform: scale(1) translateZ(0);
            }
            50% {
                transform: scale(1.08) translateZ(20px);
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

        /* Main Title with 8K Rainbow Animation */
        .main-title {
            font-size: 84px;
            font-weight: 900;
            margin-bottom: 30px;
            background: linear-gradient(
                135deg, 
                #00ff88, #667eea, #f093fb, #ff6b6b, 
                #feca57, #48dbfb, #1dd1a1, #ff9ff3,
                #54a0ff, #5f27cd, #00d2d3, #00ff88
            );
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 600% 600%;
            animation: rainbowFlow8K 12s ease infinite, titleFloat 6s ease infinite;
            line-height: 1.1;
            letter-spacing: -3px;
            filter: drop-shadow(0 0 40px rgba(0, 255, 136, 0.8)) 
                    drop-shadow(0 0 60px rgba(102, 126, 234, 0.6))
                    drop-shadow(0 0 80px rgba(240, 147, 251, 0.4));
            text-shadow: 
                0 0 100px rgba(0, 255, 136, 0.6),
                0 0 150px rgba(102, 126, 234, 0.4),
                0 0 200px rgba(240, 147, 251, 0.3);
        }

        @keyframes rainbowFlow8K {
            0%, 100% { 
                background-position: 0% 50%; 
                filter: drop-shadow(0 0 40px rgba(0, 255, 136, 0.8)) hue-rotate(0deg);
            }
            25% { 
                background-position: 50% 75%; 
                filter: drop-shadow(0 0 60px rgba(102, 126, 234, 0.9)) hue-rotate(30deg);
            }
            50% { 
                background-position: 100% 50%; 
                filter: drop-shadow(0 0 80px rgba(240, 147, 251, 0.9)) hue-rotate(60deg);
            }
            75% { 
                background-position: 50% 25%; 
                filter: drop-shadow(0 0 60px rgba(255, 107, 107, 0.8)) hue-rotate(30deg);
            }
        }

        @keyframes titleFloat {
            0%, 100% { 
                transform: translateY(0) scale(1); 
            }
            50% { 
                transform: translateY(-15px) scale(1.02); 
            }
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

        /* About Description with 8K Effects */
        .about-description {
            max-width: 900px;
            margin: 0 auto 50px;
            font-size: 22px;
            line-height: 2;
            color: rgba(255, 255, 255, 0.95);
            text-align: justify;
            padding: 40px;
            background: linear-gradient(135deg, 
                rgba(0, 255, 136, 0.08), rgba(102, 126, 234, 0.08),
                rgba(240, 147, 251, 0.06), rgba(255, 107, 107, 0.06));
            background-size: 300% 300%;
            border: 2px solid rgba(0, 255, 136, 0.4);
            border-radius: 30px;
            backdrop-filter: blur(30px) saturate(180%);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.4),
                0 0 80px rgba(0, 255, 136, 0.2),
                inset 0 0 50px rgba(0, 255, 136, 0.1);
            animation: descriptionFloat 8s ease infinite, descriptionGlow 15s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .about-description::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: descriptionShine 4s ease infinite;
        }

        @keyframes descriptionFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @keyframes descriptionGlow {
            0%, 100% { 
                background-position: 0% 50%;
                box-shadow: 
                    0 20px 60px rgba(0, 0, 0, 0.4),
                    0 0 80px rgba(0, 255, 136, 0.2),
                    inset 0 0 50px rgba(0, 255, 136, 0.1);
            }
            50% { 
                background-position: 100% 50%;
                box-shadow: 
                    0 20px 80px rgba(0, 0, 0, 0.5),
                    0 0 120px rgba(0, 255, 136, 0.4),
                    0 0 160px rgba(102, 126, 234, 0.3),
                    inset 0 0 80px rgba(0, 255, 136, 0.2);
            }
        }

        @keyframes descriptionShine {
            0% { left: -100%; }
            100% { left: 200%; }
        }

        .about-description strong {
            color: #00ff88;
            font-weight: 800;
        }

        /* Features List with 8K Effects */
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
            background: linear-gradient(135deg, 
                rgba(0, 255, 136, 0.1), rgba(102, 126, 234, 0.1),
                rgba(240, 147, 251, 0.08), rgba(255, 107, 107, 0.08));
            background-size: 300% 300%;
            border: 2px solid rgba(0, 255, 136, 0.4);
            border-radius: 25px;
            backdrop-filter: blur(30px) saturate(150%);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: featureAppear 0.8s ease backwards, featureGlow 8s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .feature-item:hover::before {
            opacity: 1;
            animation: featureShine 2s ease infinite;
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }

        @keyframes featureAppear {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.8) rotateX(-20deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
            }
        }

        @keyframes featureGlow {
            0%, 100% { 
                background-position: 0% 50%;
                filter: hue-rotate(0deg);
            }
            50% { 
                background-position: 100% 50%;
                filter: hue-rotate(20deg);
            }
        }

        @keyframes featureShine {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .feature-item:hover {
            transform: translateY(-15px) scale(1.08) rotateX(5deg);
            border-color: rgba(0, 255, 136, 0.8);
            box-shadow: 
                0 30px 100px rgba(0, 255, 136, 0.5),
                0 0 80px rgba(102, 126, 234, 0.4),
                0 0 120px rgba(240, 147, 251, 0.3),
                inset 0 0 50px rgba(0, 255, 136, 0.2);
        }

        .feature-item i {
            font-size: 48px;
            color: #00ff88;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 30px rgba(0, 255, 136, 0.9));
            animation: iconPulse 3s ease infinite;
        }

        @keyframes iconPulse {
            0%, 100% { 
                transform: scale(1) rotate(0deg);
                filter: drop-shadow(0 0 30px rgba(0, 255, 136, 0.9));
            }
            50% { 
                transform: scale(1.1) rotate(5deg);
                filter: drop-shadow(0 0 50px rgba(0, 255, 136, 1));
            }
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

        /* Call to Action Button with 8K Effects */
        .cta-button {
            display: inline-block;
            padding: 25px 70px;
            font-size: 28px;
            font-weight: 900;
            text-decoration: none;
            color: #0a0a0a;
            background: linear-gradient(135deg, 
                #00ff88, #00cc6a, #667eea, #00ff88);
            background-size: 300% 300%;
            border-radius: 60px;
            border: none;
            cursor: pointer;
            box-shadow: 
                0 20px 60px rgba(0, 255, 136, 0.7),
                0 0 100px rgba(102, 126, 234, 0.5),
                0 0 140px rgba(240, 147, 251, 0.3),
                inset 0 0 30px rgba(255, 255, 255, 0.4);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            animation: ctaPulse8K 4s ease infinite, ctaGlow 6s ease infinite;
            text-shadow: 
                0 2px 10px rgba(0, 0, 0, 0.3),
                0 0 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes ctaPulse8K {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 20px 60px rgba(0, 255, 136, 0.7),
                    0 0 100px rgba(102, 126, 234, 0.5),
                    inset 0 0 30px rgba(255, 255, 255, 0.4);
            }
            50% {
                transform: scale(1.08);
                box-shadow: 
                    0 30px 100px rgba(0, 255, 136, 1),
                    0 0 150px rgba(102, 126, 234, 0.8),
                    0 0 200px rgba(240, 147, 251, 0.6),
                    inset 0 0 60px rgba(255, 255, 255, 0.6);
            }
        }

        @keyframes ctaGlow {
            0%, 100% { 
                background-position: 0% 50%;
                filter: hue-rotate(0deg) brightness(1.1);
            }
            50% { 
                background-position: 100% 50%;
                filter: hue-rotate(20deg) brightness(1.2);
            }
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg, 
                transparent, 
                rgba(255, 255, 255, 0.7), 
                transparent
            );
            transform: rotate(45deg);
            animation: ctaShine 3s infinite;
        }

        .cta-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 60px;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.3), transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cta-button:hover::after {
            opacity: 1;
            animation: ctaRipple 1s ease-out;
        }

        @keyframes ctaShine {
            0% { left: -150%; }
            100% { left: 150%; }
        }

        @keyframes ctaRipple {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }

        .cta-button:hover {
            transform: translateY(-10px) scale(1.15);
            box-shadow: 
                0 40px 140px rgba(0, 255, 136, 1),
                0 0 150px rgba(102, 126, 234, 0.9),
                0 0 200px rgba(240, 147, 251, 0.7),
                inset 0 0 80px rgba(255, 255, 255, 0.7);
            filter: brightness(1.2) saturate(1.3);
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
            /* Space Journey Intro Responsive */
            .journey-title {
                font-size: 40px;
                padding: 0 20px;
                white-space: normal;
            }
            
            .intro-loading-text {
                font-size: 16px;
                bottom: 50px;
            }
            
            .intro-skip-button {
                top: 20px;
                right: 20px;
                padding: 12px 25px;
                font-size: 14px;
            }
            
            /* Main Content Responsive */
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

            .lens-flare {
                display: none; /* Hide lens flares on mobile for performance */
            }
        }

        /* 8K and High-Resolution Display Optimizations */
        @media (min-width: 3840px) {
            body {
                font-size: 20px;
            }

            .main-title {
                font-size: 140px;
                letter-spacing: -5px;
            }

            .subtitle {
                font-size: 52px;
            }

            .legendary-logo {
                width: 350px;
                height: 350px;
            }

            .legendary-logo i {
                font-size: 160px;
            }

            .about-description {
                font-size: 32px;
                padding: 70px;
                max-width: 1600px;
            }

            .feature-item {
                padding: 50px;
            }

            .feature-item i {
                font-size: 80px;
            }

            .feature-item h3 {
                font-size: 36px;
            }

            .feature-item p {
                font-size: 26px;
            }

            .cta-button {
                padding: 40px 120px;
                font-size: 44px;
            }

            .premium-badge {
                padding: 25px 70px;
                font-size: 26px;
            }
        }

        /* 4K Display Optimizations */
        @media (min-width: 2560px) and (max-width: 3839px) {
            body {
                font-size: 18px;
            }

            .main-title {
                font-size: 120px;
                letter-spacing: -4px;
            }

            .subtitle {
                font-size: 46px;
            }

            .legendary-logo {
                width: 300px;
                height: 300px;
            }

            .legendary-logo i {
                font-size: 140px;
            }

            .about-description {
                font-size: 28px;
                padding: 60px;
                max-width: 1400px;
            }

            .feature-item {
                padding: 45px;
            }

            .feature-item i {
                font-size: 68px;
            }

            .feature-item h3 {
                font-size: 32px;
            }

            .feature-item p {
                font-size: 22px;
            }

            .cta-button {
                padding: 35px 100px;
                font-size: 38px;
            }

            .premium-badge {
                padding: 22px 60px;
                font-size: 22px;
            }
        }

        /* High-DPI Retina Display Optimizations */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            * {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-rendering: optimizeLegibility;
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
    <!-- ==================== SPACE JOURNEY INTRO SEQUENCE ==================== -->
    <div id="intro-sequence">
        <!-- Space Canvas for Solar System -->
        <canvas id="spaceCanvas"></canvas>
        
        <!-- Skip Button -->
        <button class="intro-skip-button" onclick="skipIntro()">
            <i class="fas fa-forward"></i> Skip Intro
        </button>
        
        <!-- Journey Title (appears at the end) -->
        <h1 class="journey-title">WELCOME TO LEGEND SHOP</h1>
        
        <!-- Loading Text -->
        <div class="intro-loading-text">Entering Legend Shop...</div>
    </div>

    <!-- Ultra HD Background -->
    <div class="ultra-hd-background"></div>

    <!-- 3D Particle Canvas -->
    <canvas id="particleCanvas3D"></canvas>

    <!-- Gradient Overlay -->
    <div class="gradient-overlay"></div>

    <!-- Cinematic Light Rays -->
    <div class="light-rays"></div>

    <!-- Lens Flares -->
    <div class="lens-flare lens-flare-1"></div>
    <div class="lens-flare lens-flare-2"></div>
    <div class="lens-flare lens-flare-3"></div>

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
        // ==================== SPACE JOURNEY INTRO SEQUENCE ====================
        
        // Add intro-playing class to body
        document.body.classList.add('intro-playing');
        
        // Space Canvas Setup
        const spaceCanvas = document.getElementById('spaceCanvas');
        const spaceCtx = spaceCanvas.getContext('2d');
        spaceCanvas.width = window.innerWidth;
        spaceCanvas.height = window.innerHeight;
        
        // Planet data (8 planets of our solar system)
        const planets = [
            { name: 'Mercury', size: 15, color: '#8C7853', distance: 150, speed: 0.04, angle: 0 },
            { name: 'Venus', size: 22, color: '#FFC649', distance: 200, speed: 0.03, angle: 0.5 },
            { name: 'Earth', size: 24, color: '#4A90E2', distance: 250, speed: 0.02, angle: 1.0 },
            { name: 'Mars', size: 18, color: '#E27B58', distance: 300, speed: 0.018, angle: 1.5 },
            { name: 'Jupiter', size: 45, color: '#C88B3A', distance: 400, speed: 0.012, angle: 2.0 },
            { name: 'Saturn', size: 40, color: '#FAD5A5', distance: 480, speed: 0.01, angle: 2.5 },
            { name: 'Uranus', size: 30, color: '#4FD0E0', distance: 540, speed: 0.008, angle: 3.0 },
            { name: 'Neptune', size: 28, color: '#4166F5', distance: 590, speed: 0.007, angle: 3.5 }
        ];
        
        // Animation state
        let animationPhase = 'solar-system'; // solar-system -> zoom-to-earth -> zoom-into-earth -> complete
        let cameraZoom = 1;
        let cameraX = 0;
        let cameraY = 0;
        let phaseTime = 0;
        let introAnimationId;
        
        // Stars background
        const stars = [];
        for (let i = 0; i < 200; i++) {
            stars.push({
                x: Math.random() * spaceCanvas.width,
                y: Math.random() * spaceCanvas.height,
                size: Math.random() * 2 + 0.5,
                opacity: Math.random() * 0.8 + 0.2
            });
        }
        
        function drawStars() {
            stars.forEach(star => {
                spaceCtx.globalAlpha = star.opacity;
                spaceCtx.fillStyle = '#ffffff';
                spaceCtx.beginPath();
                spaceCtx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                spaceCtx.fill();
            });
            spaceCtx.globalAlpha = 1;
        }
        
        function drawSun() {
            const centerX = spaceCanvas.width / 2 + cameraX;
            const centerY = spaceCanvas.height / 2 + cameraY;
            
            // Sun glow
            const gradient = spaceCtx.createRadialGradient(centerX, centerY, 0, centerX, centerY, 60 * cameraZoom);
            gradient.addColorStop(0, '#FDB813');
            gradient.addColorStop(0.5, '#FFAA00');
            gradient.addColorStop(1, 'rgba(255, 170, 0, 0)');
            
            spaceCtx.fillStyle = gradient;
            spaceCtx.beginPath();
            spaceCtx.arc(centerX, centerY, 60 * cameraZoom, 0, Math.PI * 2);
            spaceCtx.fill();
            
            // Sun core
            spaceCtx.fillStyle = '#FFDD00';
            spaceCtx.shadowBlur = 40;
            spaceCtx.shadowColor = '#FF8800';
            spaceCtx.beginPath();
            spaceCtx.arc(centerX, centerY, 35 * cameraZoom, 0, Math.PI * 2);
            spaceCtx.fill();
            spaceCtx.shadowBlur = 0;
        }
        
        function drawPlanets() {
            const centerX = spaceCanvas.width / 2 + cameraX;
            const centerY = spaceCanvas.height / 2 + cameraY;
            
            planets.forEach((planet, index) => {
                // Update planet angle
                planet.angle += planet.speed;
                
                // Calculate position
                const x = centerX + Math.cos(planet.angle) * planet.distance * cameraZoom;
                const y = centerY + Math.sin(planet.angle) * planet.distance * cameraZoom;
                
                // Draw orbit path
                spaceCtx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
                spaceCtx.lineWidth = 1;
                spaceCtx.beginPath();
                spaceCtx.arc(centerX, centerY, planet.distance * cameraZoom, 0, Math.PI * 2);
                spaceCtx.stroke();
                
                // Draw planet
                spaceCtx.fillStyle = planet.color;
                spaceCtx.shadowBlur = 15;
                spaceCtx.shadowColor = planet.color;
                spaceCtx.beginPath();
                spaceCtx.arc(x, y, planet.size * cameraZoom, 0, Math.PI * 2);
                spaceCtx.fill();
                spaceCtx.shadowBlur = 0;
                
                // Highlight Earth
                if (planet.name === 'Earth') {
                    spaceCtx.strokeStyle = 'rgba(0, 255, 136, 0.6)';
                    spaceCtx.lineWidth = 3;
                    spaceCtx.beginPath();
                    spaceCtx.arc(x, y, (planet.size + 8) * cameraZoom, 0, Math.PI * 2);
                    spaceCtx.stroke();
                }
            });
        }
        
        function animateSpaceJourney() {
            phaseTime++;
            
            // Clear canvas
            spaceCtx.fillStyle = '#000000';
            spaceCtx.fillRect(0, 0, spaceCanvas.width, spaceCanvas.height);
            
            // Draw stars
            drawStars();
            
            // Phase 1: Show solar system (0-5 seconds)
            if (phaseTime < 300) {
                animationPhase = 'solar-system';
                cameraZoom = 1;
                cameraX = 0;
                cameraY = 0;
                drawSun();
                drawPlanets();
            }
            // Phase 2: Zoom towards Earth (5-10 seconds)
            else if (phaseTime < 600) {
                animationPhase = 'zoom-to-earth';
                const progress = (phaseTime - 300) / 300;
                cameraZoom = 1 + progress * 4;
                
                // Move camera towards Earth
                const earth = planets[2]; // Earth is index 2
                const earthX = Math.cos(earth.angle) * earth.distance;
                const earthY = Math.sin(earth.angle) * earth.distance;
                cameraX = -earthX * progress * cameraZoom * 0.5;
                cameraY = -earthY * progress * cameraZoom * 0.5;
                
                drawSun();
                drawPlanets();
            }
            // Phase 3: Zoom into Earth (10-14 seconds)
            else if (phaseTime < 840) {
                animationPhase = 'zoom-into-earth';
                const progress = (phaseTime - 600) / 240;
                cameraZoom = 5 + progress * 15;
                
                const earth = planets[2];
                const earthX = Math.cos(earth.angle) * earth.distance;
                const earthY = Math.sin(earth.angle) * earth.distance;
                cameraX = -earthX * cameraZoom * 0.8;
                cameraY = -earthY * cameraZoom * 0.8;
                
                // Only draw Earth at this point
                const centerX = spaceCanvas.width / 2 + cameraX;
                const centerY = spaceCanvas.height / 2 + cameraY;
                const x = centerX + Math.cos(earth.angle) * earth.distance * cameraZoom;
                const y = centerY + Math.sin(earth.angle) * earth.distance * cameraZoom;
                
                spaceCtx.fillStyle = earth.color;
                spaceCtx.shadowBlur = 30;
                spaceCtx.shadowColor = earth.color;
                spaceCtx.beginPath();
                spaceCtx.arc(x, y, earth.size * cameraZoom, 0, Math.PI * 2);
                spaceCtx.fill();
                spaceCtx.shadowBlur = 0;
                
                // Add green glow
                spaceCtx.strokeStyle = `rgba(0, 255, 136, ${0.8 - progress * 0.6})`;
                spaceCtx.lineWidth = 5;
                spaceCtx.beginPath();
                spaceCtx.arc(x, y, (earth.size + 10) * cameraZoom, 0, Math.PI * 2);
                spaceCtx.stroke();
            }
            // Phase 4: Fade to black and show title (14-16 seconds)
            else if (phaseTime < 960) {
                const progress = (phaseTime - 840) / 120;
                spaceCtx.fillStyle = `rgba(0, 0, 0, ${progress})`;
                spaceCtx.fillRect(0, 0, spaceCanvas.width, spaceCanvas.height);
            }
            // Phase 5: Complete (16+ seconds)
            else {
                animationPhase = 'complete';
                endIntroSequence();
                return;
            }
            
            introAnimationId = requestAnimationFrame(animateSpaceJourney);
        }
        
        animateSpaceJourney();
        
        // Function to skip intro
        function skipIntro() {
            endIntroSequence();
        }
        
        // Function to end intro sequence
        function endIntroSequence() {
            const introElement = document.getElementById('intro-sequence');
            introElement.classList.add('hidden');
            document.body.classList.remove('intro-playing');
            document.body.classList.add('intro-complete');
            cancelAnimationFrame(introAnimationId);
            
            // Trigger main content fade-in
            const mainContainer = document.querySelector('.main-container');
            mainContainer.style.animation = 'fadeInScale 1.5s cubic-bezier(0.19, 1, 0.22, 1) forwards';
            mainContainer.style.opacity = '1';
            mainContainer.style.pointerEvents = 'all';
        }
        
        // Window resize handler for intro canvas
        window.addEventListener('resize', () => {
            spaceCanvas.width = window.innerWidth;
            spaceCanvas.height = window.innerHeight;
        });
        
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

        // ==================== ADVANCED 8K 3D PARTICLE SYSTEM ====================
        const canvas = document.getElementById('particleCanvas3D');
        const ctx = canvas.getContext('2d', { alpha: true, desynchronized: true });

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        // Color palette for auto-changing colors
        const colorPalettes = [
            [120, 180, 240], // Green-Cyan-Blue
            [180, 240, 300], // Cyan-Blue-Purple
            [240, 300, 360], // Blue-Purple-Pink
            [300, 0, 60],    // Purple-Pink-Red
            [0, 60, 120]     // Red-Orange-Green
        ];
        let currentPaletteIndex = 0;
        let paletteTransition = 0;

        class Particle3D {
            constructor() {
                this.reset();
                this.z = Math.random() * 2000; // Start at various depths
                this.opacity = Math.random();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.z = 2000;
                this.size = Math.random() * 4 + 1;
                this.speedX = (Math.random() - 0.5) * 3;
                this.speedY = (Math.random() - 0.5) * 3;
                this.speedZ = Math.random() * 4 + 2;
                this.baseHue = Math.random() * 360;
                this.hueShift = Math.random() * 100 - 50;
                this.pulseSpeed = Math.random() * 0.05 + 0.02;
                this.pulseOffset = Math.random() * Math.PI * 2;
                this.trailLength = Math.floor(Math.random() * 3) + 2;
                this.trail = [];
            }

            update(time) {
                this.z -= this.speedZ;
                if (this.z <= 0) {
                    this.reset();
                }

                // Add cinematic motion
                this.x += this.speedX + Math.sin(time * 0.001 + this.pulseOffset) * 0.5;
                this.y += this.speedY + Math.cos(time * 0.001 + this.pulseOffset) * 0.5;

                // Wrap around screen edges
                if (this.x < 0) this.x = canvas.width;
                if (this.x > canvas.width) this.x = 0;
                if (this.y < 0) this.y = canvas.height;
                if (this.y > canvas.height) this.y = 0;

                // Pulsing effect
                this.opacity = 0.5 + Math.sin(time * this.pulseSpeed + this.pulseOffset) * 0.5;

                // Update trail
                const scale = 2000 / (2000 + this.z);
                const x2d = (this.x - canvas.width / 2) * scale + canvas.width / 2;
                const y2d = (this.y - canvas.height / 2) * scale + canvas.height / 2;
                this.trail.push({ x: x2d, y: y2d, opacity: this.opacity });
                if (this.trail.length > this.trailLength) {
                    this.trail.shift();
                }
            }

            draw(time) {
                const scale = 2000 / (2000 + this.z);
                const x2d = (this.x - canvas.width / 2) * scale + canvas.width / 2;
                const y2d = (this.y - canvas.height / 2) * scale + canvas.height / 2;
                const size = this.size * scale * (1 + Math.sin(time * this.pulseSpeed + this.pulseOffset) * 0.3);

                const depthOpacity = (2000 - this.z) / 2000;
                const finalOpacity = this.opacity * depthOpacity * 0.9;

                // Auto-changing color based on palette
                const currentPalette = colorPalettes[currentPaletteIndex];
                const nextPalette = colorPalettes[(currentPaletteIndex + 1) % colorPalettes.length];
                const hueBase = currentPalette[0] + (nextPalette[0] - currentPalette[0]) * paletteTransition;
                const hue = (hueBase + this.hueShift + time * 0.02) % 360;

                // Draw trail with fade
                ctx.shadowBlur = 0;
                for (let i = 0; i < this.trail.length - 1; i++) {
                    const t1 = this.trail[i];
                    const t2 = this.trail[i + 1];
                    const trailOpacity = finalOpacity * (i / this.trail.length) * 0.4;
                    ctx.strokeStyle = `hsla(${hue}, 100%, 60%, ${trailOpacity})`;
                    ctx.lineWidth = size * 0.5;
                    ctx.beginPath();
                    ctx.moveTo(t1.x, t1.y);
                    ctx.lineTo(t2.x, t2.y);
                    ctx.stroke();
                }

                // Optimized single-pass particle with gradient fill for glow
                ctx.shadowBlur = 30 + size * 5;
                ctx.shadowColor = `hsla(${hue}, 100%, 50%, ${finalOpacity})`;
                
                // Create radial gradient for efficient glow
                const gradient = ctx.createRadialGradient(x2d, y2d, 0, x2d, y2d, size * 3);
                gradient.addColorStop(0, `hsla(${hue}, 100%, ${70 + depthOpacity * 20}%, ${finalOpacity})`);
                gradient.addColorStop(0.4, `hsla(${hue}, 100%, ${50 + depthOpacity * 30}%, ${finalOpacity * 0.8})`);
                gradient.addColorStop(1, `hsla(${hue}, 100%, 40%, 0)`);
                
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(x2d, y2d, size * 3, 0, Math.PI * 2);
                ctx.fill();
                
                // Core particle
                ctx.shadowBlur = 15;
                ctx.fillStyle = `hsla(${hue}, 100%, ${80 + depthOpacity * 20}%, ${finalOpacity})`;
                ctx.beginPath();
                ctx.arc(x2d, y2d, size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        const particles = [];
        // Adaptive particle count based on device capability
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isHighDPI = window.devicePixelRatio > 1.5;
        let particleCount;
        
        if (isMobile) {
            particleCount = 50; // Fewer particles on mobile for performance
        } else if (window.innerWidth > 2560) {
            particleCount = isHighDPI ? 150 : 200; // 4K/8K displays
        } else if (window.innerWidth > 1920) {
            particleCount = 120; // 2K displays
        } else {
            particleCount = 100; // Standard displays
        }
        
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle3D());
        }

        let lastTime = 0;
        function animate(time) {
            const deltaTime = time - lastTime;
            lastTime = time;

            // Smooth fade trail instead of full clear
            ctx.fillStyle = 'rgba(10, 10, 10, 0.08)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Update palette transition
            paletteTransition += 0.0003;
            if (paletteTransition >= 1) {
                paletteTransition = 0;
                currentPaletteIndex = (currentPaletteIndex + 1) % colorPalettes.length;
            }

            particles.forEach(particle => {
                particle.update(time);
                particle.draw(time);
            });

            // Connect nearby particles with optimized spatial approach
            // Use a maximum connection distance to limit computation
            const maxConnectionDist = 200;
            const maxConnectionsPerParticle = 3; // Limit connections per particle
            
            particles.forEach((p1, i) => {
                let connections = 0;
                for (let j = i + 1; j < particles.length && connections < maxConnectionsPerParticle; j++) {
                    const p2 = particles[j];
                    const dx = p1.x - p2.x;
                    const dy = p1.y - p2.y;
                    
                    // Quick distance check before expensive sqrt
                    const distSq = dx * dx + dy * dy;
                    if (distSq > maxConnectionDist * maxConnectionDist) continue;
                    
                    const dist = Math.sqrt(distSq);
                    const opacity = (maxConnectionDist - dist) / maxConnectionDist * 0.4;
                    const avgZ = (p1.z + p2.z) / 2;
                    const depthOpacity = (2000 - avgZ) / 2000;
                    const currentPalette = colorPalettes[currentPaletteIndex];
                    const hue = (currentPalette[1] + time * 0.01) % 360;
                    
                    ctx.strokeStyle = `hsla(${hue}, 100%, 50%, ${opacity * depthOpacity * 0.5})`;
                    ctx.lineWidth = 1.5;
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = `hsla(${hue}, 100%, 50%, ${opacity * depthOpacity * 0.3})`;
                    ctx.beginPath();
                    
                    const scale1 = 2000 / (2000 + p1.z);
                    const scale2 = 2000 / (2000 + p2.z);
                    const x1 = (p1.x - canvas.width / 2) * scale1 + canvas.width / 2;
                    const y1 = (p1.y - canvas.height / 2) * scale1 + canvas.height / 2;
                    const x2 = (p2.x - canvas.width / 2) * scale2 + canvas.width / 2;
                    const y2 = (p2.y - canvas.height / 2) * scale2 + canvas.height / 2;
                    
                    ctx.moveTo(x1, y1);
                    ctx.lineTo(x2, y2);
                    ctx.stroke();
                    connections++;
                }
            });

            requestAnimationFrame(animate);
        }

        animate(0);

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
