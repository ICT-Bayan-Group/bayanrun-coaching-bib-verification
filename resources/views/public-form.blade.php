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
            background: linear-gradient(to bottom, #560000ff 0%, #8b0202ff 50%, #021f6eff 50%, #00113fff 100%);
            min-height: 100vh;
        }

        /* Main Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #560000ff 0%, #8b0202ff 30%, #021f6eff 70%, #00113fff 100%);
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

        /* Thank You Page Preloader */
        .thankyou-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #560000ff 0%, #8b0202ff 30%, #021f6eff 70%, #00113fff 100%);
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

        /* BIB Verification Loading Overlay */
        .bib-loading-overlay {
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

        .bib-loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .bib-loading-content {
            background: linear-gradient(135deg, #560000ff 0%, #8b0202ff 30%, #021f6eff 70%, #00113fff 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 300px;
            width: 90%;
            transform: translateY(20px) scale(0.9);
            transition: transform 0.3s ease-out;
        }

        .bib-loading-overlay.show .bib-loading-content {
            transform: translateY(0) scale(1);
        }

        /* Thank You Content Styles */
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

        .thankyou-sparkles {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            opacity: 0;
            animation: sparklesSlideUp 1s ease-out 1.9s forwards;
        }

        .sparkle {
            font-size: 2rem;
            animation: sparkle 2s ease-in-out infinite;
        }

        .sparkle:nth-child(1) { animation-delay: 0s; }
        .sparkle:nth-child(2) { animation-delay: 0.5s; }
        .sparkle:nth-child(3) { animation-delay: 1s; }
        .sparkle:nth-child(4) { animation-delay: 1.5s; }

        .thankyou-footer {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(15px);
            animation: footerSlideUp 1s ease-out 2.2s forwards;
        }

        /* Logo Styles */
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
            width: 60px;
            height: 60px;
            opacity: 0;
            transform: scale(0.8);
            animation: logoFadeIn 0.5s ease-out forwards;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
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

        .bib-loading-title {
            opacity: 0;
            transform: translateY(15px);
            animation: titleFadeIn 0.5s ease-out 0.2s forwards;
        }

        .bib-loading-subtitle {
            opacity: 0;
            transform: translateY(10px);
            animation: subtitleFadeIn 0.5s ease-out 0.4s forwards;
        }

        /* Loading Bars */
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

        /* Progress Bar */
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

        /* Animations for Main Preloader */
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
            to {
                width: 100%;
            }
        }

        @keyframes textSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animations for BIB Loading */
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
            to {
                opacity: 1;
            }
        }

        @keyframes barPulse {
            0%, 100% { transform: scaleY(1); opacity: 0.7; }
            50% { transform: scaleY(1.5); opacity: 1; }
        }

        @keyframes progressFadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes progressFillSmall {
            to {
                width: 100%;
            }
        }

        @keyframes textFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Thank You Page Animations */
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

        @keyframes sparklesSlideUp {
            to {
                opacity: 1;
            }
        }

        @keyframes footerSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes sparkle {
            0%, 100% { 
                transform: scale(1) rotate(0deg);
                opacity: 0.7;
            }
            50% { 
                transform: scale(1.3) rotate(180deg);
                opacity: 1;
            }
        }

        /* Spinning animation for verification icon */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        .main-content {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .main-content.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .thankyou-title {
                font-size: 2rem;
            }
            
            .thankyou-message {
                font-size: 1rem;
            }
            
            .thankyou-logo {
                width: 100px;
                height: 100px;
            }
            
            .thankyou-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="min-h-screen py-6 sm:py-8">
    <!-- Main Preloader -->
    <div id="preloader" class="preloader">
        <div class="logo-container">
            <div class="logo-glow"></div>
            <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="logo object-contain">
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
        
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        
        <div class="loading-text">
            Memuat halaman pendaftaran...
        </div>
    </div>

    <!-- Thank You Preloader -->
    <div id="thankyou-preloader" class="thankyou-preloader">
        <div class="thankyou-content">
            <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="thankyou-logo object-contain">
            
            <h1 class="thankyou-title">
                TERIMA KASIH!
            </h1>
            
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

    <!-- BIB Verification Loading Overlay -->
    <div id="bib-loading-overlay" class="bib-loading-overlay">
        <div class="bib-loading-content">
            <div class="logo-container">
                <div class="logo-glow-small"></div>
                <img src="{{ asset('images/bayanrun.png') }}" alt="Bayan Run 2025" class="logo-small object-contain mx-auto">
            </div>
            
            <h3 class="bib-loading-title text-lg font-bold text-white">
                Memverifikasi BIB
            </h3>
            
            <p class="bib-loading-subtitle text-white text-sm font-medium mt-2">
                Mohon tunggu sebentar...
            </p>
            
            <div class="loading-bars-small">
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
                <div class="loading-bar-small"></div>
            </div>
            
            <div class="loading-text-small">
                Validasi data peserta...
            </div>
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
                <h1 class="mt-4 sm:mt-5 text-2xl sm:text-3xl font-extrabold text-white italic tracking-wide drop-shadow-sm">
                    COACHING CLINIC REGISTRATION
                </h1>
                <p class="text-white font-bold mt-1 sm:mt-2 text-sm sm:text-xl uppercase">BAYAN RUN 2025</p>
            </div>

            <!-- BIB Verification Form -->
            <div id="bib-verification" class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-gray-800">
                    <span class="inline-block mr-2">🎫</span>
                    Verifikasi Nomor BIB
                </h2>
                
                <form id="bib-form" class="space-y-6">
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">🏃‍♂️</span>
                                Nomor BIB *
                            </span>
                        </label>
                        <input type="text" 
                               id="nomor_bib" 
                               required 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Masukkan nomor BIB Anda">
                        <div class="text-red-500 text-sm mt-1 font-extrabold hidden" id="error-nomor_bib"></div>
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
                                    Masukkan nomor BIB sesuai dengan yang tercantum pada email undangan Anda untuk melanjutkan proses pendaftaran.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            id="btn-verify" 
                            class="w-full bg-blue-700 hover:from-red-800 hover:to-red-600 text-white font-semibold py-4 rounded-lg transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none">
                        <span id="btn-verify-text">
                            <span class="inline-block mr-2">🔍</span>
                            Verifikasi BIB
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
                                <strong>BIB Terverifikasi!</strong><br>
                                <span id="verified-name"></span> - BIB #<span id="verified-bib"></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <form id="pendaftaran-form" class="space-y-6">
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">🎫</span>
                                Nomor BIB *
                            </span>
                        </label>
                        <input type="text" 
                               id="nomor_bib_readonly" 
                               readonly 
                               class="font-semibold w-full border border-gray-300 rounded-lg p-3 bg-gray-100 cursor-not-allowed" 
                               placeholder="Nomor BIB">
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
                                <span class="inline-block mr-2">📧</span>
                                Email *
                            </span>
                        </label>
                        <input type="email" 
                               id="email" 
                               required 
                               class=" font-semibold w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="email@example.com">
                        <div class="text-red-500 font-extrabold text-sm mt-1 hidden" id="error-email"></div>
                        <div class="text-blue-600 text-sm mt-1">Anda dapat mengubah email jika diperlukan</div>
                    </div>
                    
                    <div>
                        <label class="block font-medium text-gray-700 mb-2">
                            <span class="flex items-center font-semibold">
                                <span class="inline-block mr-2">📱</span>
                                Nomor Telepon *
                            </span>
                        </label>
                        <input type="text" 
                               id="telepon" 
                               required 
                               class=" font-semibold w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="08xxxxxxxxxx">
                        <div class="text-red-500 font-extrabold text-sm mt-1 hidden" id="error-telepon"></div>
                        <div class="text-blue-600 text-sm mt-1">Anda dapat mengubah nomor telepon jika diperlukan</div>
                        <p class="text-sm text-gray-600 mt-1 font-semibold">
                            <span class="inline-block mr-1 ">💬</span>
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
                            class="w-full bg-blue-700 hover:from-red-800 hover:to-red-600 text-white font-semibold py-4 rounded-lg transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none">
                        <span id="btn-text">
                            <span class="inline-block mr-2">🚀</span>
                            Daftar 
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
                            <span class="font-medium">BIB:</span>
                            <span id="success-bib"></span>
                        </div>
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
                                Kami telah mengirimkan konfirmasi pendaftaran dan QR Code bukti registrasi ke nomor WhatsApp Anda.
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
                        class="w-full sm:w-auto bg-yellow-500 to-red-500 hover:from-blue-700 hover:to-red-600 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105">
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

        // Preloader functionality
        window.addEventListener('load', function() {
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
                        document.getElementById('bib-form').reset();
                        document.getElementById('pendaftaran-form').reset();
                        clearErrors();
                        
                        document.getElementById('success-message').classList.add('hidden');
                        document.getElementById('form-pendaftaran').classList.add('hidden');
                        document.getElementById('bib-verification').classList.remove('hidden');
                        
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

        // Show BIB Loading Overlay
        function showBibLoading() {
            const overlay = document.getElementById('bib-loading-overlay');
            overlay.classList.add('show');
        }

        // Hide BIB Loading Overlay
        function hideBibLoading() {
            const overlay = document.getElementById('bib-loading-overlay');
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

        // BIB verification handler
        document.getElementById('bib-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            clearErrors();
            
            const nomorBib = document.getElementById('nomor_bib').value.trim();
            
            if (!nomorBib) {
                showError('nomor_bib', 'Nomor BIB harus diisi');
                return;
            }

            const btnVerify = document.getElementById('btn-verify');
            const btnVerifyText = document.getElementById('btn-verify-text');
            const originalText = btnVerifyText.innerHTML;
            
            // Show loading overlay
            showBibLoading();
            
            btnVerify.disabled = true;
            btnVerifyText.innerHTML = '<span class="inline-block mr-2 spin">🔍</span>Memverifikasi...';

            try {
                const response = await fetch('/verify-bib', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nomor_bib: nomorBib })
                });

                const result = await response.json();

                // Add minimum loading time for better UX
                await new Promise(resolve => setTimeout(resolve, 1500));

                if (result.success) {
                    currentPesertaData = result.data;
                    hideBibLoading();
                    showRegistrationForm(result.data);
                } else {
                    hideBibLoading();
                    showError('nomor_bib', result.message || 'Nomor BIB tidak ditemukan');
                }

            } catch (error) {
                console.error('Error:', error);
                hideBibLoading();
                showError('nomor_bib', 'Terjadi kesalahan saat memverifikasi BIB. Silakan coba lagi.');
            } finally {
                btnVerify.disabled = false;
                btnVerifyText.innerHTML = originalText;
            }
        });

        // Registration form handler
        document.getElementById('pendaftaran-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            clearErrors();
            
            const formData = {
                nomor_bib: document.getElementById('nomor_bib_readonly').value.trim(),
                nama_lengkap: document.getElementById('nama_lengkap').value.trim(),
                kategori_lari: document.getElementById('kategori_lari').value.trim(),
                email: document.getElementById('email').value.trim(),
                telepon: document.getElementById('telepon').value.trim()
            };
            
            if (!validateRegistrationForm(formData)) {
                return;
            }

            const btnDaftar = document.getElementById('btn-daftar');
            const btnText = document.getElementById('btn-text');
            const originalText = btnText.innerHTML;
            
            btnDaftar.disabled = true;
            btnText.innerHTML = '<span class="inline-block mr-2 spin">🚀</span>Mendaftar...';

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

                if (result.success) {
                    showSuccessMessage(result.data);
                } else {
                    if (response.status === 422 && result.errors) {
                        showValidationErrors(result.errors);
                    } else {
                        alert('Terjadi kesalahan: ' + result.message);
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim data. Silakan coba lagi.');
            } finally {
                btnDaftar.disabled = false;
                btnText.innerHTML = originalText;
            }
        });

        function showRegistrationForm(data) {
            document.getElementById('bib-verification').classList.add('hidden');
            document.getElementById('form-pendaftaran').classList.remove('hidden');
            
            document.getElementById('verified-name').textContent = data.nama;
            document.getElementById('verified-bib').textContent = data.nomor_bib;
            
            document.getElementById('nomor_bib_readonly').value = data.nomor_bib;
            document.getElementById('nama_lengkap').value = data.nama;
            document.getElementById('kategori_lari').value = data.kategori_lari;
            document.getElementById('email').value = data.email;
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
            
            document.getElementById('success-bib').textContent = data.nomor_bib;
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
                document.getElementById('bib-form').reset();
                document.getElementById('pendaftaran-form').reset();
                clearErrors();
                
                document.getElementById('success-message').classList.add('hidden');
                document.getElementById('form-pendaftaran').classList.add('hidden');
                document.getElementById('bib-verification').classList.remove('hidden');
                
                document.getElementById('qr-preview').classList.add('hidden');
                currentPesertaData = null;
                
                window.scrollTo(0, 0);
            }, 500);
        }

        // Auto-format phone number
        document.getElementById('telepon').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
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
                if (this.readonly) return;
                
                const field = this.id;
                const value = this.value.trim();
                
                if (this.classList.contains('border-red-500')) {
                    document.getElementById('error-' + field).classList.add('hidden');
                    this.classList.remove('border-red-500');
                }
                
                if (!value && field !== 'nomor_bib_readonly' && field !== 'nama_lengkap' && field !== 'kategori_lari') {
                    showError(field, 'Field ini harus diisi');
                } else if (field === 'email' && value && !isValidEmail(value)) {
                    showError(field, 'Format email tidak valid');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('border-red-500')) {
                    this.classList.remove('border-red-500');
                    document.getElementById('error-' + this.id).classList.add('hidden');
                }
            });
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