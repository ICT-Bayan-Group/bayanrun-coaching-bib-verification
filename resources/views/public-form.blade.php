<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayan Run 2025 - Pendaftaran Coaching Clinic</title>
    <link rel="icon" type="ico" href="{{ asset('logo.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Montserrat', sans-serif !important;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to bottom, #ffffffff 0%, #d9d9d9ff 50%, #021f6eff 50%, #00113fff 100%);
            min-height: 100vh;
        }

        /* ===== PRELOADER STYLES ===== */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #d9d9d9ff 0%, #d9d9d9ff 30%, #021f6eff 70%, #00113fff 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: transform 0.8s ease-in-out, opacity 0.8s ease-in-out;
        }

        .preloader.fade-out {
            transform: translateY(-100%);
            opacity: 0;
        }

        /* ===== THANK YOU PRELOADER ===== */
        .thankyou-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #d9d9d9ff 0%, #d9d9d9ff 30%, #021f6eff 70%, #00113fff 100%);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }

        .thankyou-preloader.show {
            display: flex;
            opacity: 1;
        }

        /* ===== EMAIL LOADING OVERLAY ===== */
        .email-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 8888;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }

        .email-loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .email-loading-content {
            background: linear-gradient(135deg, #021f6eff 0%, #00113fff 30%, #021f6eff 70%, #00113fff 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 300px;
            width: 90%;
            transform: translateY(20px) scale(0.9);
            transition: transform 0.3s ease-out;
        }

        .email-loading-overlay.show .email-loading-content {
            transform: translateY(0) scale(1);
        }

        /* ===== REGISTRATION LOADING OVERLAY ===== */
        .registration-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 8889;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease-in-out, visibility 0.4s ease-in-out;
        }

        .registration-loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .registration-loading-content {
            background: linear-gradient(135deg, #021f6eff 0%, #00113fff 30%, #021f6eff 70%, #00113fff 100%);
            border-radius: 25px;
            padding: 3.5rem 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
            max-width: 350px;
            width: 90%;
            transform: translateY(30px) scale(0.9);
            transition: transform 0.4s ease-out;
        }

        .registration-loading-overlay.show .registration-loading-content {
            transform: translateY(0) scale(1);
        }

        /* ===== MAX REGISTRATION ALERT ===== */
        .max-registration-alert {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9990;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
        }

        .max-registration-alert.show {
            opacity: 1;
            visibility: visible;
        }

             .max-alert-content {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 480px;
            width: 90%;
            transform: translateY(50px) scale(0.8);
            transition: transform 0.5s ease-out;
        }

        .max-registration-alert.show .max-alert-content {
            transform: translateY(0) scale(1);
        }

        .max-alert-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: alertPulse 2s ease-in-out infinite;
        }

        .max-alert-title {
            color: white;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .max-alert-message {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.6;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .max-alert-button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 1rem 2rem;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .max-alert-button:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* ===== CONTENT STYLES ===== */
        .thankyou-content {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            opacity: 0;
            transform: translateY(50px) scale(0.9);
            animation: thankYouSlideUp 1.5s ease-out 0.5s forwards;
        }

        .thankyou-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            opacity: 0;
            transform: translateY(30px) scale(0.8);
            animation: logoSlideUp 1.2s ease-out 0.2s forwards;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.4));
        }

        .thankyou-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            font-style: italic;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(30px);
            animation: titleSlideUp 1s ease-out 1s forwards;
        }

        .thankyou-message {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.8;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(20px);
            animation: messageSlideUp 1s ease-out 1.3s forwards;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .thankyou-location {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            font-weight: 600;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            animation: locationSlideUp 1s ease-out 1.6s forwards;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* ===== LOGO STYLES ===== */
        .logo-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .logo {
            width: 150px;
            height: 150px;
            opacity: 0;
            transform: translateY(50px) scale(0.8);
            animation: logoSlideUp 1.2s ease-out 0.3s forwards;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
        }

        .logo-small {
            width: 100px;
            height: 100px;
            opacity: 0;
            transform: scale(0.8);
            animation: logoFadeIn 0.5s ease-out forwards;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .logo-medium {
            width: 80px;
            height: 80px;
            opacity: 0;
            transform: scale(0.8);
            animation: logoFadeIn 0.5s ease-out forwards;
            filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.3));
        }

        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0;
            animation: glowPulse 2s ease-in-out 0.8s infinite;
        }

        .logo-glow-small {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 2s ease-in-out infinite;
        }

        .logo-glow-medium {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 2s ease-in-out infinite;
        }

        /* ===== TEXT ANIMATION STYLES ===== */
        .title {
            opacity: 0;
            transform: translateY(30px);
            animation: titleSlideUp 1s ease-out 0.8s forwards;
        }

        .subtitle {
            opacity: 0;
            transform: translateY(20px);
            animation: subtitleSlideUp 1s ease-out 1.2s forwards;
        }

        .email-loading-title {
            opacity: 0;
            transform: translateY(15px);
            animation: titleFadeIn 0.5s ease-out 0.2s forwards;
        }

        .email-loading-subtitle {
            opacity: 0;
            transform: translateY(10px);
            animation: subtitleFadeIn 0.5s ease-out 0.4s forwards;
        }

        .registration-loading-title {
            opacity: 0;
            transform: translateY(20px);
            animation: titleFadeIn 0.6s ease-out 0.3s forwards;
        }

        .registration-loading-subtitle {
            opacity: 0;
            transform: translateY(15px);
            animation: subtitleFadeIn 0.6s ease-out 0.6s forwards;
        }

        .registration-loading-steps {
            opacity: 0;
            transform: translateY(10px);
            animation: subtitleFadeIn 0.5s ease-out 0.9s forwards;
        }

        /* ===== LOADING BARS ===== */
        .loading-bars {
            display: flex;
            gap: 4px;
            margin-top: 2rem;
        }

        .loading-bar {
            width: 4px;
            height: 40px;
            background: linear-gradient(to top, #ffffff40, #ffffff);
            border-radius: 2px;
            opacity: 0;
            animation: barSlideUp 0.6s ease-out forwards;
        }

        .loading-bar:nth-child(1) { animation-delay: 1.5s; }
        .loading-bar:nth-child(2) { animation-delay: 1.6s; }
        .loading-bar:nth-child(3) { animation-delay: 1.7s; }
        .loading-bar:nth-child(4) { animation-delay: 1.8s; }
        .loading-bar:nth-child(5) { animation-delay: 1.9s; }

        .loading-bars-small {
            display: flex;
            gap: 3px;
            margin-top: 1.5rem;
            justify-content: center;
        }

        .loading-bar-small {
            width: 3px;
            height: 25px;
            background: linear-gradient(to top, #ffffff40, #ffffff);
            border-radius: 2px;
            opacity: 0;
            animation: barFadeIn 0.4s ease-out forwards, barPulse 1.5s ease-in-out infinite;
        }

        .loading-bar-small:nth-child(1) { animation-delay: 0.6s, 0.6s; }
        .loading-bar-small:nth-child(2) { animation-delay: 0.7s, 0.7s; }
        .loading-bar-small:nth-child(3) { animation-delay: 0.8s, 0.8s; }
        .loading-bar-small:nth-child(4) { animation-delay: 0.9s, 0.9s; }
        .loading-bar-small:nth-child(5) { animation-delay: 1s, 1s; }

        .loading-bars-medium {
            display: flex;
            gap: 4px;
            margin-top: 1.8rem;
            justify-content: center;
        }

        .loading-bar-medium {
            width: 4px;
            height: 35px;
            background: linear-gradient(to top, #ffffff40, #ffffff);
            border-radius: 2px;
            opacity: 0;
            animation: barFadeIn 0.5s ease-out forwards, barPulse 1.8s ease-in-out infinite;
        }

        .loading-bar-medium:nth-child(1) { animation-delay: 1.2s, 1.2s; }
        .loading-bar-medium:nth-child(2) { animation-delay: 1.3s, 1.3s; }
        .loading-bar-medium:nth-child(3) { animation-delay: 1.4s, 1.4s; }
        .loading-bar-medium:nth-child(4) { animation-delay: 1.5s, 1.5s; }
        .loading-bar-medium:nth-child(5) { animation-delay: 1.6s, 1.6s; }

        /* ===== PROGRESS BARS ===== */
        .progress-container {
            width: 200px;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1px;
            margin-top: 2rem;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            animation: progressSlideUp 0.8s ease-out 2s forwards;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #ffffff, #ffffff80);
            border-radius: 1px;
            width: 0%;
            animation: progressFill 2.5s ease-out 2.2s forwards;
        }

        .progress-container-small {
            width: 150px;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1px;
            margin-top: 1.5rem;
            overflow: hidden;
            opacity: 0;
            animation: progressFadeIn 0.5s ease-out 1.2s forwards;
        }

        .progress-bar-small {
            height: 100%;
            background: linear-gradient(90deg, #ffffff, #ffffff80);
            border-radius: 1px;
            width: 0%;
            animation: progressFillSmall 2s ease-out 1.4s forwards;
        }

        .progress-container-medium {
            width: 180px;
            height: 3px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1.5px;
            margin-top: 2rem;
            overflow: hidden;
            opacity: 0;
            animation: progressFadeIn 0.6s ease-out 1.8s forwards;
        }

        .progress-bar-medium {
            height: 100%;
            background: linear-gradient(90deg, #ffffff, #ffffff80);
            border-radius: 1.5px;
            width: 0%;
            animation: progressFillMedium 3s ease-out 2s forwards;
        }

        /* ===== LOADING TEXT ===== */
        .loading-text {
            margin-top: 1.5rem;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(15px);
            animation: textSlideUp 0.8s ease-out 2.4s forwards;
        }

        .loading-text-small {
            margin-top: 1rem;
            color: white;
            font-size: 0.8rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(10px);
            animation: textFadeIn 0.5s ease-out 1.6s forwards;
        }

        .loading-text-medium {
            margin-top: 1.2rem;
            color: white;
            font-size: 0.85rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(12px);
            animation: textFadeIn 0.6s ease-out 2.2s forwards;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .main-content.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== STEP INDICATOR ===== */
        .step-indicator {
            display: inline-block;
            margin-right: 0.5rem;
            animation: stepPulse 2s ease-in-out infinite;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes alertPulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.9;
            }
            50% { 
                transform: scale(1.1);
                opacity: 1;
            }
        }

        @keyframes logoSlideUp {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
        }

        @keyframes titleSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes subtitleSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes barSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
            from {
                transform: translateY(20px);
            }
        }

        @keyframes progressSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes progressFill {
            to { width: 100%; }
        }

        @keyframes progressFillMedium {
            to { width: 100%; }
        }

        @keyframes textSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes logoFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes titleFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes subtitleFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes barFadeIn {
            to { opacity: 1; }
        }

        @keyframes barPulse {
            0%, 100% { transform: scaleY(1); opacity: 0.7; }
            50% { transform: scaleY(1.5); opacity: 1; }
        }

        @keyframes progressFadeIn {
            to { opacity: 1; }
        }

        @keyframes progressFillSmall {
            to { width: 100%; }
        }

        @keyframes textFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes thankYouSlideUp {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes messageSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes locationSlideUp {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes stepPulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .thankyou-title, .max-alert-title {
                font-size: 2rem;
            }
            
            .thankyou-message, .max-alert-message {
                font-size: 1rem;
            }
            
            .thankyou-logo {
                width: 100px;
                height: 100px;
            }
            
            .thankyou-content, .max-alert-content {
                padding: 1rem;
            }
            
            .max-alert-content {
                max-width: 320px;
            }
        }
    </style>
</head>
<body class="min-h-screen py-6 sm:py-8">
    <!-- Main Preloader -->
    <div id="preloader" class="preloader">
        <div class="logo-container">
            <div class="logo-glow"></div>
            <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="mx-auto w-34 h-36 sm:w-34 sm:h-36 object-contain">
        </div>
        
        <h1 class="title text-4xl sm:text-5xl font-extrabold text-white italic tracking-wide text-center">
            BAYAN RUN 2025
        </h1>
        
        <p class="subtitle text-white font-bold text-sm sm:text-base uppercase tracking-wider">
            Coaching Clinic Registration
        </p>
        
        <div class="loading-bars">
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
        </div>
        
        <div class="loading-text">
            Memuat halaman pendaftaran...
        </div>
    </div>

    <!-- Thank You Preloader -->
    <div id="thankyou-preloader" class="thankyou-preloader">
        <div class="thankyou-content">
            <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="mx-auto w-34 h-36 sm:w-34 sm:h-36 object-contain">
            
            <h1 class="thankyou-title">TERIMA KASIH!</h1>
            
            <p class="thankyou-message">
                Terima kasih telah melakukan registrasi<br>
                <strong>Coaching Clinic Bayan Run 2025</strong>
            </p>
            
            <div class="thankyou-location">
                <div class="text-lg font-bold mb-2">📍 Sampai Bertemu Di</div>
                <div class="text-base">
                    <strong>Gedung Kesenian Balikpapan</strong><br>
                    Kalimantan Timur, Indonesia
                </div>
            </div>
        </div>
    </div>

    <!-- Email Verification Loading Overlay -->
    <div id="email-loading-overlay" class="email-loading-overlay">
        <div class="email-loading-content">
            <div class="logo-container">
                <div class="logo-glow-small"></div>
                <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="mx-auto w-34 h-36 sm:w-34 sm:h-36 object-contain">
            </div>
            
            <h3 class="email-loading-title text-lg font-bold text-white">
                Memverifikasi Email
            </h3>
            
            <p class="email-loading-subtitle text-white text-sm font-medium mt-2">
                Mohon tunggu sebentar...
            </p>
            
            <div class="loading-bars-small">
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
            </div>
            
            <div class="progress-container-small">
                <div class="progress-bar-small"></div>
            </div>
            
            <div class="loading-text-small">
                Validasi data peserta...
            </div>
        </div>
    </div>

    <!-- Registration Loading Overlay -->
    <div id="registration-loading-overlay" class="registration-loading-overlay">
        <div class="registration-loading-content">
            <div class="logo-container">
                <div class="logo-glow-medium"></div>
                <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="mx-auto w-34 h-36 sm:w-34 sm:h-36 object-contain">
            </div>
            
            <h3 class="registration-loading-title text-xl font-bold text-white">
                Memproses Pendaftaran
            </h3>
            
            <p class="registration-loading-subtitle text-white text-sm font-medium mt-2">
                Tunggu sebentar, kami sedang memproses data Anda...
            </p>
            
            <div class="registration-loading-steps text-white text-xs mt-3 space-y-1">
                <div id="step1" class="opacity-50">
                    <span class="step-indicator">🔄</span>Menyimpan data peserta...
                </div>
                <div id="step2" class="opacity-50">
                    <span class="step-indicator">📱</span>Membuat QR Code...
                </div>
                <div id="step3" class="opacity-50">
                    <span class="step-indicator">💬</span>Mengirim WhatsApp...
                </div>
                <div id="step4" class="opacity-50">
                    <span class="step-indicator">✅</span>Menyelesaikan registrasi...
                </div>
            </div>
            
            <div class="loading-bars-medium">
                <div class="loading-bar-medium"></div>
                <div class="loading-bar-medium"></div>
                <div class="loading-bar-medium"></div>
                <div class="loading-bar-medium"></div>
                <div class="loading-bar-medium"></div>
            </div>
            
            <div class="loading-text-medium">
                Proses ini memerlukan beberapa detik...
            </div>
        </div>
    </div>

    <!-- MAX Registration Alert -->
    <div id="max-registration-alert" class="max-registration-alert">
        <div class="max-alert-content">
            <div class="max-alert-icon">🚫</div>
            
            <h2 class="max-alert-title">PENDAFTARAN DITUTUP</h2>
            
            <div class="max-alert-message">
                <strong>Mohon Maaf!</strong><br><br>
                Pendaftaran Coaching Clinic Bayan Run 2025 telah <strong>DITUTUP</strong> karena sudah mencapai batas maksimal <strong>600 peserta</strong>.<br><br>
                Terima kasih atas antusiasme Anda.
            </div>
            
            <button onclick="hideMaxAlert()" class="max-alert-button">
                Mengerti, Tutup
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="main-content">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <!-- Header dengan Logo -->
            <div class="text-center mb-8 sm:mb-10">
                <img src="{{ asset('images/bayanrun.png') }}" 
                     alt="Bayan Run 2025" 
                     class="mx-auto w-34 h-36 sm:w-34 sm:h-36 object-contain">
                <h1 class="mt-4 sm:mt-5 text-2xl sm:text-3xl font-extrabold text-blue-900 italic tracking-wide drop-shadow-sm">
                    COACHING CLINIC REGISTRATION
                </h1>
                <p class="text-red-800 font-extrabold mt-1 sm:mt-2 text-sm sm:text-xl uppercase">BAYAN RUN 2025</p>
                
                <!-- Registration Status Alert -->
                <div id="registration-status" class="mt-4 hidden">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mx-auto max-w-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-red-500 text-lg">🚫</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold">
                                    <strong>Pendaftaran Ditutup!</strong><br>
                                    Batas maksimal 600 peserta telah tercapai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Verification Form -->
            <div id="email-verification" class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-gray-800">
                    <span class="inline-block mr-2">🎫</span>
                    Verifikasi Email Terdaftar
                </h2>
                
                <form id="email-form" class="space-y-6">
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">📧</span>
                                Email *
                            </span>
                        </label>
                        <input type="email" 
                               id="email" 
                               required 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Masukkan email Anda">
                        <div class="text-red-500 text-sm mt-1 font-extrabold hidden" id="error-email"></div>
                    </div>
                    
                    <!-- Info Section -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-blue-400 text-xl">ℹ️</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 font-semibold">
                                    <strong>Informasi:</strong><br>
                                     Gunakan alamat email yang sama saat registrasi Bayan Run 2025.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            id="btn-verify" 
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-4 rounded-lg transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none">
                        <span id="btn-verify-text">
                            <span class="inline-block mr-2">🔍</span>
                            Verifikasi Email
                        </span>
                    </button>
                </form>
            </div>

            <!-- Form Pendaftaran Event Lari (Hidden Initially) -->
            <div id="form-pendaftaran" class="bg-white shadow-lg rounded-lg p-6 sm:p-8 hidden">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-gray-800">
                    <span class="inline-block mr-2">🏃‍♂️</span>
                    Form Pendaftaran
                </h2>
                
                <!-- Verified User Info -->
                <div id="verified-info" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-green-400 text-xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-semibold">
                                <strong>Email Terverifikasi!</strong><br>
                                <span id="verified-name"></span> - Email: <span id="verified-email"></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="pendaftaran-form" class="space-y-6">
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">📧</span>
                                Email *
                            </span>
                        </label>
                        <input type="email" 
                               id="email_readonly" 
                               readonly 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 bg-gray-100 cursor-not-allowed" 
                               placeholder="Email">
                        <div class="text-gray-500 text-sm mt-1">Field ini tidak dapat diubah</div>
                    </div>
                    
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">👤</span>
                                Nama Lengkap *
                            </span>
                        </label>
                        <input type="text" 
                               id="nama_lengkap" 
                               readonly 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 bg-gray-100 cursor-not-allowed" 
                               placeholder="Nama lengkap">
                        <div class="text-gray-500 text-sm mt-1">Field ini tidak dapat diubah</div>
                    </div>
                    
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">🏃‍♀️</span>
                                Kategori Lari *
                            </span>
                        </label>
                        <input type="text" 
                               id="kategori_lari" 
                               readonly 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 bg-gray-100 cursor-not-allowed" 
                               placeholder="Kategori lari">
                        <div class="text-gray-500 text-sm mt-1">Field ini tidak dapat diubah</div>
                    </div>
                    
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">📱</span>
                                Nomor Telepon *
                            </span>
                        </label>
                        <input type="tel" 
                               id="telepon" 
                               required 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="08xxxxxxxxxx">
                        <div class="text-red-500 font-extrabold text-sm mt-1 hidden" id="error-telepon"></div>
                        <div class="text-blue-600 text-sm mt-1">Anda dapat mengubah nomor telepon jika diperlukan</div>
                        <p class="text-sm text-gray-600 mt-1 font-semibold">
                            <span class="inline-block mr-1">💬</span>
                            Nomor WhatsApp aktif untuk menerima konfirmasi pendaftaran
                        </p>
                    </div>

                    <!-- Info Section -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-yellow-400 text-xl">ℹ️</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 font-semibold">
                                    <strong>Informasi:</strong><br>
                                    Setelah pendaftaran berhasil, Anda akan menerima konfirmasi dan QR Code melalui WhatsApp sebagai bukti registrasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            id="btn-daftar" 
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-4 rounded-lg transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none">
                        <span id="btn-text">
                            <span class="inline-block mr-2">🚀</span>
                            Daftar Sekarang
                        </span>
                    </button>
                </form>
            </div>

            <!-- Success Message -->
            <div id="success-message" class="bg-white shadow-lg rounded-lg p-6 sm:p-8 text-center hidden">
                <div class="text-green-600 text-6xl mb-4">✅</div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Pendaftaran Berhasil!</h2>
                <p class="text-gray-600 mb-6 text-sm sm:text-base font-semibold">
                    Hasil pendaftaran beserta QR Code registrasi akan menjadi bukti resmi untuk memasuki area acara saat hari pelaksanaan.
                    Mohon simpan dan tunjukkan QR Code Anda di pintu masuk.
                </p>
                
                <!-- Peserta Info -->
                <div id="peserta-info" class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-bold text-lg mb-3 text-center text-gray-800">
                        <span class="inline-block mr-2">🏃‍♂️</span>
                        Detail Pendaftaran
                    </h3>
                    <div class="grid gap-2 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium">Nama:</span>
                            <span id="success-nama"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Kategori Lari:</span>
                            <span id="success-kategori"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Email:</span>
                            <span id="success-email"></span>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Info -->
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 text-left">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-green-400 text-xl">💬</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Cek WhatsApp Anda!</strong><br>
                                Kami telah mengirimkan konfirmasi pendaftaran dan QR Code bukti registrasi coaching clinic ke nomor WhatsApp Anda.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Preview -->
                <div id="qr-preview" class="mb-6 hidden">
                    <h4 class="font-bold text-gray-800 mb-2">QR Code Anda:</h4>
                    <div class="flex justify-center">
                        <img id="qr-image" src="" alt="QR Code" class="w-40 h-40 border rounded-lg">
                    </div>
                </div>
                
                <button onclick="showThankYou()" 
                        id="btn-reset"
                        class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105">
                    Selesai
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-10 sm:mt-12 text-center font-semibold text-white text-xs sm:text-sm">
            <p>&copy; 2025 ICT Bayan Group. All Rights Reserved.</p>
        </footer>
    </div>

    <script>
        let currentPesertaData = null;
        let registrationStepInterval = null;

        // Preloader functionality
        window.addEventListener('load', function() {
            checkRegistrationStatus();

            setTimeout(function() {
                const preloader = document.getElementById('preloader');
                const mainContent = document.getElementById('main-content');
                
                preloader.classList.add('fade-out');
                
                setTimeout(function() {
                    preloader.style.display = 'none';
                    mainContent.classList.add('show');
                }, 800);
            }, 4500);
        });
         // Check registration status function
      async function checkRegistrationStatus() {
            try {
                const response = await fetch('/check-registration-status');
                const result = await response.json();
                
                const registrationStatus = document.getElementById('registration-status');
                
                if (!result.registration_open) {
                    showMaxAlert();
                    registrationStatus.classList.remove('hidden');
                    
                    // Disable all form inputs
                    const inputs = document.querySelectorAll('input[type="email"], input[type="text"], input[type="tel"]');
                    const buttons = document.querySelectorAll('button[type="submit"]');
                    
                    inputs.forEach(input => input.disabled = true);
                    buttons.forEach(btn => btn.disabled = true);
                    
                    return false;
                } else {
                    registrationStatus.classList.add('hidden');
                    
                    // Re-enable form inputs (except readonly ones)
                    const inputs = document.querySelectorAll('input[type="email"]:not([readonly]), input[type="text"]:not([readonly]), input[type="tel"]');
                    const buttons = document.querySelectorAll('button[type="submit"]');
                    
                    inputs.forEach(input => input.disabled = false);
                    buttons.forEach(btn => btn.disabled = false);
                }
                return true;
            } catch (error) {
                console.error('Error checking registration status:', error);
                return true; // Allow form to show on error
            }
        }

        function showMaxAlert() {
            const maxAlert = document.getElementById('max-registration-alert');
            const registrationStatus = document.getElementById('registration-status');
            
            maxAlert.classList.add('show');
            registrationStatus.classList.remove('hidden');
            
            // Disable forms
            document.getElementById('success-message').classList.add('hidden');
            document.getElementById('form-pendaftaran').classList.add('hidden');
            document.getElementById('email-verification').classList.remove('hidden');
            document.getElementById('registration-status').classList.add('hidden');
            
            document.getElementById('qr-preview').classList.add('hidden');
            currentPesertaData = null;
            
            // Re-enable forms
            document.getElementById('btn-verify').disabled = false;
            document.getElementById('email').disabled = false;
            
            // Re-check registration status
            checkRegistrationStatus();
        }

        // Show Thank You Page
        function showThankYou() {
            const mainContent = document.getElementById('main-content');
            const thankYouPreloader = document.getElementById('thankyou-preloader');
            
            // Hide main content with fade out effect
            mainContent.classList.remove('show');
            
            setTimeout(function() {
                mainContent.style.display = 'none';
                thankYouPreloader.classList.add('show');
                
                // Auto hide thank you page after 8 seconds and reset form
                setTimeout(function() {
                    thankYouPreloader.classList.remove('show');
                    
                    setTimeout(function() {
                        // Reset all forms and show initial state
                        document.getElementById('email-form').reset();
                        document.getElementById('pendaftaran-form').reset();
                        clearErrors();
                        
                        document.getElementById('success-message').classList.add('hidden');
                        document.getElementById('form-pendaftaran').classList.add('hidden');
                        document.getElementById('email-verification').classList.remove('hidden');
                        
                        document.getElementById('qr-preview').classList.add('hidden');
                        currentPesertaData = null;
                        
                        // Show main content again
                        mainContent.style.display = 'block';
                        mainContent.classList.add('show');
                        
                        window.scrollTo(0, 0);
                    }, 800);
                }, 8000);
            }, 300);
        }

        // Show Email Loading Overlay
        function showEmailLoading() {
            const overlay = document.getElementById('email-loading-overlay');
            overlay.classList.add('show');
        }

        // Hide Email Loading Overlay
        function hideEmailLoading() {
            const overlay = document.getElementById('email-loading-overlay');
            overlay.classList.remove('show');
        }

        // Show main preloader for reset
        function showMainPreloader() {
            const preloader = document.getElementById('preloader');
            const mainContent = document.getElementById('main-content');
            
            // Reset preloader animations
            preloader.style.display = 'flex';
            preloader.classList.remove('fade-out');
            mainContent.classList.remove('show');
            
            // Hide main content immediately
            mainContent.style.opacity = '0';
            mainContent.style.transform = 'translateY(30px)';
            
            // Show preloader for shorter duration on reset
            setTimeout(function() {
                preloader.classList.add('fade-out');
                
                setTimeout(function() {
                    preloader.style.display = 'none';
                    mainContent.classList.add('show');
                }, 800);
            }, 2500); // Shorter duration for reset
        }

        // Email verification handler
        document.getElementById('email-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            clearErrors();
            
            const email = document.getElementById('email').value.trim();
            
            if (!email) {
                showError('email', 'Email harus diisi');
                return;
            }

            const btnVerify = document.getElementById('btn-verify');
            const btnVerifyText = document.getElementById('btn-verify-text');
            const originalText = btnVerifyText.innerHTML;
            
            // Show loading overlay
            showEmailLoading();
            
            btnVerify.disabled = true;
            btnVerifyText.innerHTML = '<span class="inline-block mr-2 spin">🔍</span>Memverifikasi...';

            try {
                const response = await fetch('/verify-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const result = await response.json();

                // Add minimum loading time for better UX
                await new Promise(resolve => setTimeout(resolve, 1500));

                if (result.success) {
                    currentPesertaData = result.data;
                    hideEmailLoading();
                    showRegistrationForm(result.data);
                } else {
                    hideEmailLoading();
                    showError('email', result.message || 'Email tidak ditemukan');
                }

            } catch (error) {
                console.error('Error:', error);
                hideEmailLoading();
                showError('email', 'Terjadi kesalahan saat memverifikasi email. Silakan coba lagi.');
            } finally {
                btnVerify.disabled = false;
                btnVerifyText.innerHTML = originalText;
            }
        });

        function showRegistrationLoading() {
                const overlay = document.getElementById('registration-loading-overlay');
                overlay.classList.add('show');
                
                // Start registration step animation
                startRegistrationSteps();
            }

            // Hide Registration Loading Overlay
            function hideRegistrationLoading() {
                const overlay = document.getElementById('registration-loading-overlay');
                overlay.classList.remove('show');
                
                // Stop registration step animation
                if (registrationStepInterval) {
                    clearInterval(registrationStepInterval);
                    registrationStepInterval = null;
                }
            }
            // Registration steps animation
                function startRegistrationSteps() {
                    const steps = ['step1', 'step2', 'step3', 'step4'];
                    let currentStep = 0;
                    
                    // Reset all steps
                    steps.forEach(stepId => {
                        const stepElement = document.getElementById(stepId);
                        stepElement.classList.remove('opacity-100', 'text-green-300');
                        stepElement.classList.add('opacity-50');
                        stepElement.querySelector('.step-indicator').textContent = '🔄';
                    });
                    registrationStepInterval = setInterval(() => {
                if (currentStep < steps.length) {
                    const stepElement = document.getElementById(steps[currentStep]);
                    stepElement.classList.remove('opacity-50');
                    stepElement.classList.add('opacity-100', 'text-green-300');
                    stepElement.querySelector('.step-indicator').textContent = '✅';
                    currentStep++;
                } else {
                    clearInterval(registrationStepInterval);
                    registrationStepInterval = null;
                }
            }, 800); // Each step takes 800ms
        }


        // Registration form handler
       document.getElementById('pendaftaran-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Check registration status first
                const canRegister = await checkRegistrationStatus();
                if (!canRegister) {
                    return;
                }
                
                clearErrors();
                
                const formData = {
                    email: document.getElementById('email_readonly').value.trim(),
                    nama_lengkap: document.getElementById('nama_lengkap').value.trim(),
                    kategori_lari: document.getElementById('kategori_lari').value.trim(),
                    telepon: document.getElementById('telepon').value.trim()
                };
                
                if (!validateRegistrationForm(formData)) {
                    return;
                }

                const btnDaftar = document.getElementById('btn-daftar');
                const btnText = document.getElementById('btn-text');
                const originalText = btnText.innerHTML;
                
                // Show registration loading overlay
                showRegistrationLoading();
                
                btnDaftar.disabled = true;
                btnText.innerHTML = '<span class="inline-block mr-2 spin">🚀</span>Memproses...';

                try {
                    const response = await fetch('/daftar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();

                    // Add minimum loading time for better UX (allow steps to complete)
                    await new Promise(resolve => setTimeout(resolve, 4000));

                    if (result.success) {
                        hideRegistrationLoading();
                        showSuccessMessage(result.data);
                    } else {
                        hideRegistrationLoading();
                        
                        if (response.status === 422 && result.errors) {
                            showValidationErrors(result.errors);
                        } else if (result.message && result.message.includes('maksimal')) {
                            showMaxAlert();
                        } else {
                            alert('Terjadi kesalahan: ' + result.message);
                        }
                    }

                } catch (error) {
                    console.error('Error:', error);
                    hideRegistrationLoading();
                    alert('Terjadi kesalahan saat mengirim data. Silakan coba lagi.');
                } finally {
                    btnDaftar.disabled = false;
                    btnText.innerHTML = originalText;
                }
            });

        function showRegistrationForm(data) {
            document.getElementById('email-verification').classList.add('hidden');
            document.getElementById('form-pendaftaran').classList.remove('hidden');
            
            document.getElementById('verified-name').textContent = data.nama;
            document.getElementById('verified-email').textContent = data.email;

            document.getElementById('email_readonly').value = data.email;
            document.getElementById('nama_lengkap').value = data.nama;
            document.getElementById('kategori_lari').value = data.kategori_lari;
            document.getElementById('telepon').value = data.nomor_telepon;
            
            window.scrollTo(0, 0);
        }

        function validateRegistrationForm(data) {
            let isValid = true;
            
            if (!data.email) {
                showError('email', 'Email harus diisi');
                isValid = false;
            } else if (!isValidEmail(data.email)) {
                showError('email', 'Format email tidak valid');
                isValid = false;
            }
            
            if (!data.telepon) {
                showError('telepon', 'Nomor telepon harus diisi');
                isValid = false;
            }
            
            return isValid;
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showError(field, message) {
            const errorDiv = document.getElementById('error-' + field);
            const inputField = document.getElementById(field);
            
            if (errorDiv && inputField) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                inputField.classList.add('border-red-500');
            }
        }

        function clearErrors() {
            const errorDivs = document.querySelectorAll('[id^="error-"]');
            const inputFields = document.querySelectorAll('input');
            
            errorDivs.forEach(div => {
                div.textContent = '';
                div.classList.add('hidden');
            });
            
            inputFields.forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        function showValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                if (errors[field] && errors[field].length > 0) {
                    showError(field, errors[field][0]);
                }
            });
        }

        function showSuccessMessage(data) {
            document.getElementById('form-pendaftaran').classList.add('hidden');
            document.getElementById('success-message').classList.remove('hidden');

            document.getElementById('success-email').textContent = data.email;
            document.getElementById('success-nama').textContent = data.nama;
            document.getElementById('success-kategori').textContent = data.kategori;
            document.getElementById('success-email').textContent = document.getElementById('email').value;
            
            if (data.qr_code_url) {
                document.getElementById('qr-image').src = data.qr_code_url;
                document.getElementById('qr-preview').classList.remove('hidden');
            }
            
            window.scrollTo(0, 0);
        }

        function resetForm() {
            // Show preloader with reset functionality
            showMainPreloader();
            
            // Clear form data after a short delay
            setTimeout(function() {
                document.getElementById('email-form').reset();
                document.getElementById('pendaftaran-form').reset();
                clearErrors();
                
                document.getElementById('success-message').classList.add('hidden');
                document.getElementById('form-pendaftaran').classList.add('hidden');
                document.getElementById('email-verification').classList.remove('hidden');
                
                document.getElementById('qr-preview').classList.add('hidden');
                currentPesertaData = null;
                
                window.scrollTo(0, 0);
            }, 500);
        }

        // Auto-format phone number
        document.getElementById('telepon').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            // Limit to reasonable phone number length
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            
            if (value.startsWith('62')) {
                // Already has country code
            } else if (value.startsWith('0')) {
                // Local format, keep as is
            } else if (value.startsWith('8')) {
                // Add 0 prefix
                value = '0' + value;
            }
            
            this.value = value;
        });

      // Real-time validation feedback
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.readonly || this.disabled) return;
                
                const field = this.id;
                const value = this.value.trim();
                
                // Clear existing errors first
                if (this.classList.contains('border-red-500')) {
                    document.getElementById('error-' + field).classList.add('hidden');
                    this.classList.remove('border-red-500');
                }
                
                // Validate specific fields
                if (!value && field !== 'email_readonly' && field !== 'nama_lengkap' && field !== 'kategori_lari') {
                    showError(field, 'Field ini harus diisi');
                } else if ((field === 'email' || field === 'email_readonly') && value && !isValidEmail(value)) {
                    showError(field, 'Format email tidak valid');
                } else if (field === 'telepon' && value && value.length < 10) {
                    showError(field, 'Nomor telepon minimal 10 digit');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('border-red-500')) {
                    this.classList.remove('border-red-500');
                    document.getElementById('error-' + this.id).classList.add('hidden');
                }
            });
        });
        // Prevent double-click on buttons with improved debouncing
        function debounceButton(button, delay = 2000) {
            button.addEventListener('click', function() {
                this.style.pointerEvents = 'none';
                setTimeout(() => {
                    this.style.pointerEvents = 'auto';
                }, delay);
            });
        }

        debounceButton(document.getElementById('btn-verify'), 1500);
        debounceButton(document.getElementById('btn-daftar'), 3000);
        debounceButton(document.getElementById('btn-reset'), 1000);

          // Enhanced form validation
        function enhancedValidation() {
            const emailInput = document.getElementById('email');
            const teleponInput = document.getElementById('telepon');
            
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.classList.add('border-yellow-300');
                } else {
                    this.classList.remove('border-yellow-300');
                }
            });
            
            teleponInput.addEventListener('input', function() {
                const telepon = this.value.trim();
                if (telepon && telepon.length < 10) {
                    this.classList.add('border-yellow-300');
                } else {
                    this.classList.remove('border-yellow-300');
                }
            });
        }
           document.addEventListener('DOMContentLoaded', enhancedValidation);
            // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC key to close max alert
            if (e.key === 'Escape') {
                const maxAlert = document.getElementById('max-registration-alert');
                if (maxAlert.classList.contains('show')) {
                    hideMaxAlert();
                }
            }
            
            // Enter key optimization for forms
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                const form = e.target.closest('form');
                if (form) {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton && !submitButton.disabled) {
                        e.preventDefault();
                        submitButton.click();
                    }
                }
            }
        });
        function hideMaxAlert() {
            const maxAlert = document.getElementById('max-registration-alert');
            maxAlert.classList.remove('show');
            
            // Reset form to initial state
            setTimeout(() => {
                document.getElementById('email-form').reset();
                document.getElementById('pendaftaran-form').reset();
                clearErrors();
                
                document.getElementById('success-message').classList.add('hidden');
                document.getElementById('form-pendaftaran').classList.add('hidden');
                document.getElementById('email-verification').classList.remove('hidden');
                document.getElementById('registration-status').classList.add('hidden');
                
                document.getElementById('qr-preview').classList.add('hidden');
                currentPesertaData = null;
                
                // Re-enable forms
                document.getElementById('btn-verify').disabled = false;
                document.getElementById('email').disabled = false;
                
                // Re-check registration status
                checkRegistrationStatus();
            }, 500);
        }

          // Performance optimization: Preload images
        function preloadImages() {
            const images = [
                '{{ asset("images/bayanrun.png") }}'
            ];
            
            images.forEach(src => {
                const img = new Image();
                img.src = src;
            });
        }

        // Call preload on page load
        window.addEventListener('load', preloadImages);

          // Network status monitoring
        function monitorNetworkStatus() {
            window.addEventListener('online', function() {
                console.log('Network connection restored');
                // Re-enable forms if they were disabled due to network issues
                checkRegistrationStatus();
            });

            window.addEventListener('offline', function() {
                console.log('Network connection lost');
                // Show user-friendly message about network issues
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    const buttons = form.querySelectorAll('button');
                    buttons.forEach(btn => {
                        if (!btn.dataset.originalText) {
                            btn.dataset.originalText = btn.innerHTML;
                        }
                        btn.innerHTML = '<span class="inline-block mr-2">📶</span>Koneksi Terputus';
                        btn.disabled = true;
                    });
                });
            });
        }

        // Initialize network monitoring
        monitorNetworkStatus();
         // Auto-retry mechanism for failed requests
        async function fetchWithRetry(url, options, maxRetries = 3) {
            let lastError;
            
            for (let i = 0; i < maxRetries; i++) {
                try {
                    const response = await fetch(url, options);
                    if (response.ok) {
                        return response;
                    }
                    lastError = new Error(`HTTP ${response.status}: ${response.statusText}`);
                } catch (error) {
                    lastError = error;
                    // Wait before retry
                    if (i < maxRetries - 1) {
                        await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
                    }
                }
            }
            
            throw lastError;
        }

         // Improved error handling with user-friendly messages
        function handleApiError(error, context = '') {
            console.error(`API Error${context ? ` (${context})` : ''}:`, error);
            
            let message = 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.';
            
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                message = 'Koneksi internet bermasalah. Periksa koneksi Anda dan coba lagi.';
            } else if (error.message.includes('timeout')) {
                message = 'Permintaan memakan waktu terlalu lama. Silakan coba lagi.';
            } else if (error.message.includes('400')) {
                message = 'Data yang dikirim tidak valid. Periksa kembali form Anda.';
            } else if (error.message.includes('500')) {
                message = 'Server mengalami gangguan. Silakan coba beberapa saat lagi.';
            }
            
            return message;
        }

           // Enhanced loading states
        function setLoadingState(element, isLoading, loadingText = 'Loading...') {
            if (isLoading) {
                element.dataset.originalText = element.innerHTML;
                element.innerHTML = loadingText;
                element.disabled = true;
            } else {
                element.innerHTML = element.dataset.originalText || element.innerHTML;
                element.disabled = false;
            }
        }
        // Smooth scrolling enhancement
        function smoothScrollTo(element, offset = 0) {
            const elementPosition = element.offsetTop - offset;
            window.scrollTo({
                top: elementPosition,
                behavior: 'smooth'
            });
        }
        // Initialize all enhancements
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bayan Run 2025 Registration Form - Clean Version Loaded');
        });
        // Prevent double-click on buttons
        document.getElementById('btn-verify').addEventListener('click', function() {
            this.style.pointerEvents = 'none';
            setTimeout(() => {
                this.style.pointerEvents = 'auto';
            }, 2000);
        });

        document.getElementById('btn-daftar').addEventListener('click', function() {
            this.style.pointerEvents = 'none';
            setTimeout(() => {
                this.style.pointerEvents = 'auto';
            }, 2000);
        });

        document.getElementById('btn-reset').addEventListener('click', function() {
            this.style.pointerEvents = 'none';
            setTimeout(() => {
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
    </script>
</body>
</html>