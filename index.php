<?php
require_once __DIR__ . '/config/database.php';

// Ambil data event dari database
$eventsResult = mysqli_query($conn, 'SELECT * FROM event ORDER BY tanggal DESC LIMIT 3');
$events = $eventsResult ? mysqli_fetch_all($eventsResult, MYSQLI_ASSOC) : [];

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatTanggal(?string $tanggal): string {
    if (!$tanggal) return '-';
    $ts = strtotime($tanggal);
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    return date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMA FTI UAJY - Senat Mahasiswa Fakultas Teknologi Industri</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        night: "#050505",
                        midnight: "#0B0B0F",
                        accent: "#0FD7D6",
                    },
                    fontFamily: {
                        body: ['"Space Grotesk"', "sans-serif"],
                    },
                },
            },
        };
    </script>
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: #0FD7D6;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0bc2c1;
        }
        
        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.3s ease;
        }
        
        /* Card hover effects */
        .card-hover {
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            border-color: rgba(15, 215, 214, 0.5);
        }

        .clean-panel {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            left: 1rem;
            right: 1rem;
            bottom: 0.35rem;
            height: 2px;
            border-radius: 999px;
            background: #0FD7D6;
            opacity: 0;
            transform: scaleX(0.4);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            opacity: 1;
            transform: scaleX(1);
        }
        
        /* Mobile menu animation */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        /* Focus styles */
        :focus-visible {
            outline: 2px solid #0FD7D6;
            outline-offset: 2px;
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }

        /* Loading Screen Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; visibility: visible; }
            to { opacity: 0; visibility: hidden; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top-color: #0FD7D6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #050505;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .loading-screen.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .loading-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid rgba(15, 215, 214, 0.3);
            background: rgba(255, 255, 255, 0.02);
            padding: 15px;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .loading-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        
        .loading-text {
            color: #0FD7D6;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.3em;
            margin-top: 20px;
            text-transform: uppercase;
            animation: slideUp 0.5s ease-out;
        }
        
        .loading-subtext {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
            margin-top: 10px;
            letter-spacing: 0.1em;
        }
        
        .loading-progress {
            width: 200px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 30px;
            overflow: hidden;
        }
        
        .loading-progress-bar {
            height: 100%;
            width: 0%;
            background: #0FD7D6;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        
        .main-content {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .main-content.loaded {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-night text-white font-body antialiased">
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-logo">
            <img src="asset/img/icon.png" alt="SEMA FTI UAJY Logo">
        </div>
        <div class="loading-text">SEMA FTI</div>
        <div class="loading-subtext">Senat Mahasiswa Fakultas Teknologi Industri</div>
        <div class="loading-progress">
            <div class="loading-progress-bar" id="loadingProgress"></div>
        </div>
        <div class="mt-8 text-white/40 text-sm" id="loadingMessage">
            <i class="fas fa-circle-notch fa-spin mr-2"></i> Memuat konten...
        </div>
    </div>

    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-tide text-night px-4 py-2 rounded-lg z-50">
        Skip to main content
    </a>

    <!-- Header -->
    <header id="mainHeader" class="fixed top-0 left-0 right-0 z-50 border-b border-transparent" style="transition: background 0.4s ease, backdrop-filter 0.4s ease, border-color 0.4s ease;">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#home" class="flex items-center gap-3 group">
                    <img src="asset/img/icon.png" alt="Logo SEMA FTI UAJY" 
                         class="h-12 w-12 rounded-full border border-white/10 bg-white/5 p-1 object-contain transition-transform group-hover:scale-105">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-white/60">FTI UAJY</p>
                        <p class="font-display text-xl tracking-wide text-white">SENAT MAHASISWA</p>
                    </div>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-2">
                    <a href="#home" class="nav-link active px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Home</a>
                    <a href="#bidang" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Bidang</a>
                    <a href="#komunitas" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Komunitas</a>
                    <a href="#berita" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Berita</a>
                    <a href="#tentang" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Tentang</a>
                    <a href="#kontak" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Kontak</a>
                    <a href="admin/dashboard.php" class="ml-4 px-5 py-2.5 rounded-full bg-gradient-to-r from-ember to-tide text-night font-semibold shadow-[0_10px_30px_-15px_rgba(15,215,214,0.7)] hover:shadow-[0_0_30px_rgba(241,90,41,0.35)] transition-all">
                        Admin Panel
                    </a>
                </nav>
                
                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg hover:bg-white/10" aria-label="Open menu">
                    <i class="fas fa-bars text-xl text-white"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="fixed inset-0 z-50 md:hidden hidden" id="mobileMenu">
        <div class="absolute inset-0 bg-black/70" id="menuOverlay"></div>
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-midnight border-l border-white/10 p-6 mobile-menu" id="mobileMenuPanel">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <img src="asset/img/icon.png" alt="Logo" class="h-10 w-10 rounded-full">
                    <span class="font-display font-semibold">SEMA FTI</span>
                </div>
                <button id="closeMobileMenu" class="p-2 rounded-lg hover:bg-white/10">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>
            
            <nav class="space-y-2">
                <a href="#home" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Home</a>
                <a href="#bidang" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Bidang</a>
                <a href="#komunitas" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Komunitas</a>
                <a href="#berita" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Berita</a>
                <a href="#tentang" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Tentang</a>
                <a href="#kontak" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-xl">Kontak</a>
                <a href="admin/dashboard.php" class="block py-3 px-4 mt-4 rounded-full bg-gradient-to-r from-ember to-tide text-night font-semibold text-center">Admin Panel</a>
            </nav>
            
            <div class="absolute bottom-8 left-6 right-6">
                <div class="border-t border-white/10 pt-6">
                    <div class="flex justify-center gap-4">
                        <a href="/cdn-cgi/l/email-protection#85e3f1ecf0e4effcabf6e0ebe4f1e8e4ede4f6ecf6f2e4c5e2e8e4ece9abe6eae8" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-ember/20 transition-colors">
                            <i class="fas fa-envelope text-ember"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-tide/20 transition-colors">
                            <i class="fab fa-instagram text-tide"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-white/10 transition-colors">
                            <i class="fab fa-line text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="main-content">
        <!-- Hero Section - Full Width Auto-Sliding -->
        <section id="home" class="relative w-full h-screen overflow-hidden" style="background: #050505;">
            <!-- Image Slider Container -->
            <div class="absolute inset-0 w-full h-full">
                <div id="heroSlider" class="flex transition-transform duration-700 h-full">
                    <!-- Slide 1 -->
                    <div class="w-full h-full flex-shrink-0 relative">
                        <img src="asset/img/seminar%20backdrop.JPG" alt="Seminar SEMA" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-b from-night/30 via-transparent to-night/80"></div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="w-full h-full flex-shrink-0 relative">
                        <img src="asset/img/spfest.JPG" alt="Sparkfest" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-b from-night/30 via-transparent to-night/80"></div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="w-full h-full flex-shrink-0 relative">
                        <img src="asset/img/baksos.JPG" alt="Bakti Sosial" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-b from-night/30 via-transparent to-night/80"></div>
                    </div>
                </div>
            </div>
            
            <!-- Centered Content Overlay -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-6 z-10">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/20 bg-white/5 mb-8">
                        <span class="h-2 w-2 rounded-full bg-accent animate-pulse"></span>
                        <span class="text-xs uppercase tracking-[0.5em] text-white/70">Senat Mahasiswa FTI UAJY</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6 tracking-tight">
                        Organisasi Wadah Kreatif Mahasiswa FTI
                    </h1>
                    
                    <p class="text-lg md:text-xl text-white/80 mb-10 max-w-2xl mx-auto leading-relaxed">
                        Kolaboratif, hangat, dan responsif terhadap isu kampus. Kami merancang program strategis dan merayakan komunitas.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#bidang" class="group flex items-center justify-center gap-2 px-8 py-4 bg-accent text-night font-semibold rounded-lg hover:opacity-90 transition-opacity">
                            Jelajahi Bidang
                            <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </a>
                        <a href="#komunitas" class="px-8 py-4 border border-white/30 rounded-lg font-semibold hover:border-white/50 hover:bg-white/5 transition-all">
                            Temui Komunitas
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Slider Indicators -->
            <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                <button class="hero-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
                <button class="hero-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
                <button class="hero-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/40 animate-bounce z-20">
                <i class="fas fa-chevron-down text-2xl"></i>
            </div>
        </section>

        <!-- Bidang Section -->
        <section id="bidang" class="py-20 bg-midnight/30">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Bidang Senat Mahasiswa</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Temui Bidang yang Ada di SEMA</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Lima bidang utama yang bekerja sinergis untuk melayani dan mengembangkan potensi mahasiswa FTI UAJY.
                    </p>
                </div>
                
                <!-- Row 1: 3 cards -->
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Card 1: Sosial Masyarakat -->
                    <div class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(16,185,129,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(16,185,129,0.12);">
                            <i class="fas fa-hand-holding-heart text-xl" style="color: #10B981;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Sosial Masyarakat</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Merancang gerakan sosial dan advokasi, memastikan kebijakan kampus berpihak pada mahasiswa.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #10B981;"></i>
                                <span>Klinik Aspirasi</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #10B981;"></i>
                                <span>Aksi Nyata</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=3" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #10B981;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 2: Kominfo -->
                    <div class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(139,92,246,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(139,92,246,0.12);">
                            <i class="fas fa-broadcast-tower text-xl" style="color: #8B5CF6;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Komunikasi Informasi</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Mengemas narasi positif, membangun dokumentasi visual, dan menyebarkan informasi akurat.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #8B5CF6;"></i>
                                <span>Weekly Digest</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #8B5CF6;"></i>
                                <span>Desain & Audio</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=5" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #8B5CF6;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 3: Usaha Dana -->
                    <div class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(245,158,11,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(245,158,11,0.12);">
                            <i class="fas fa-coins text-xl" style="color: #F59E0B;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Usaha Dana</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Mengelola sumber pendanaan organisasi untuk mendukung program dan kegiatan SEMA.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #F59E0B;"></i>
                                <span>Pengelolaan Dana</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #F59E0B;"></i>
                                <span>Program Wirausaha</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=4" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #F59E0B;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Row 2: 2 cards centered -->
                <div class="grid md:grid-cols-2 gap-8 md:max-w-3xl md:mx-auto">
                    <!-- Card 4: Minat Bakat -->
                    <div class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(236,72,153,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(236,72,153,0.12);">
                            <i class="fas fa-star text-xl" style="color: #EC4899;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Minat &amp; Bakat</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Memfasilitasi pengembangan potensi melalui kegiatan, kompetisi, dan program kreatif.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #EC4899;"></i>
                                <span>Festival Seni & Olahraga</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #EC4899;"></i>
                                <span>Showcase Bakat</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=2" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #EC4899;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 5: Pengurus Harian -->
                    <div class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(15,215,214,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(15,215,214,0.12);">
                            <i class="fas fa-crown text-xl" style="color: #0FD7D6;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Pengurus Harian</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Mengoordinasikan kegiatan operasional SEMA dan memastikan sinergi antar bidang optimal.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-accent text-xs"></i>
                                <span>Koordinasi Bidang</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-accent text-xs"></i>
                                <span>Program Strategis</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=1" class="inline-flex items-center gap-1 text-accent hover:text-white/70 transition-colors text-sm font-medium">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Komunitas Section -->
        <section id="komunitas" class="py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-14">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Unit Kegiatan Mahasiswa</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Komunitas di FTI UAJY</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Bergabunglah dengan komunitas yang sesuai minat dan bakatmu untuk bertumbuh dan berprestasi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Badminton -->
                    <div class="card-hover clean-panel rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-feather-alt text-accent text-lg"></i>
                            </div>
                            <h3 class="font-semibold">Badminton</h3>
                        </div>
                        <p class="text-white/70 text-sm mb-3">
                            Komunitas bulu tangkis FTI aktif berlatih dan kompetitif antar kampus.
                        </p>
                        <span class="text-xs text-white/50">⚽ Olahraga • Aktif</span>
                    </div>

                    <!-- SIKMA -->
                    <div class="card-hover clean-panel rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-music text-accent text-lg"></i>
                            </div>
                            <h3 class="font-semibold">SIKMA</h3>
                        </div>
                        <p class="text-white/70 text-sm mb-3">
                            Komunitas seni dan musik dengan fokus pada pertunjukan panggung.
                        </p>
                        <span class="text-xs text-white/50">🎵 Seni & Budaya • Kreatif</span>
                    </div>

                    <!-- Texere Basket -->
                    <div class="card-hover clean-panel rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-basketball text-accent text-lg"></i>
                            </div>
                            <h3 class="font-semibold">Texere</h3>
                        </div>
                        <p class="text-white/70 text-sm mb-3">
                            Tim basket FTI bersaing di turnamen kampus dan antar universitas.
                        </p>
                        <span class="text-xs text-white/50">⚽ Olahraga • Kompetitif</span>
                    </div>

                    <!-- FTI Image -->
                    <div class="card-hover clean-panel rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-camera text-accent text-lg"></i>
                            </div>
                            <h3 class="font-semibold">FTI Image</h3>
                        </div>
                        <p class="text-white/70 text-sm mb-3">
                            Komunitas fotografi dan videografi mendokumentasikan momen kampus.
                        </p>
                        <span class="text-xs text-white/50">📸 Visual • Kreatif</span>
                    </div>

                    <!-- Futsal -->
                    <div class="card-hover clean-panel rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-futbol text-accent text-lg"></i>
                            </div>
                            <h3 class="font-semibold">Futsal</h3>
                        </div>
                        <p class="text-white/70 text-sm mb-3">
                            Komunitas futsal aktif berlatih dan mengikuti turnamen reguler.
                        </p>
                        <span class="text-xs text-white/50">⚽ Olahraga • Kompetitif</span>
                    </div>

                    <!-- CTA Card -->
                    <div class="clean-panel rounded-lg p-6 flex flex-col items-center justify-center text-center hover:border-accent/50 transition-colors">
                        <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas fa-plus text-accent text-lg"></i>
                        </div>
                        <h3 class="font-semibold mb-1">Komunitas Lainnya</h3>
                        <p class="text-white/60 text-xs mb-3">
                            Hubungi kami untuk info selengkapnya
                        </p>
                        <a href="#kontak" class="text-xs text-accent hover:text-white/70 transition-colors">
                            Hubungi Kami →
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Berita Section -->
        <section id="berita" class="py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] text-white/60">Berita Terkini</span>
                        <h2 class="text-3xl md:text-4xl font-bold mt-4">Update Terbaru dari SEMA</h2>
                    </div>
                    <div class="flex items-center gap-2 text-white/60 mt-4 md:mt-0">
                        <i class="fas fa-sync-alt text-sm"></i>
                        <span class="text-sm">Diperbarui setiap pekan</span>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <?php if ($events): ?>
                        <?php foreach ($events as $i => $event): ?>
                        <article class="card-hover clean-panel rounded-lg overflow-hidden">
                            <?php if (!empty($event['foto_event'])): ?>
                                <div class="aspect-[4/3] overflow-hidden">
                                    <img src="<?php echo h($event['foto_event']); ?>" alt="<?php echo h($event['judul']); ?>" class="w-full h-full object-cover">
                                </div>
                            <?php else: ?>
                                <div class="aspect-[4/3] bg-gradient-to-br from-accent/10 to-transparent"></div>
                            <?php endif; ?>
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs uppercase tracking-[0.4em] text-white/50"><?php echo formatTanggal($event['tanggal']); ?></span>
                                    <span class="px-2 py-1 bg-accent/10 text-accent text-xs font-medium rounded">Event</span>
                                </div>
                                <h3 class="text-lg font-semibold mb-2"><?php echo h($event['judul']); ?></h3>
                                <p class="text-white/70 mb-3 text-sm">
                                    <?php echo h(mb_substr($event['deskripsi'] ?? '', 0, 90)) . (mb_strlen($event['deskripsi'] ?? '') > 90 ? '...' : ''); ?>
                                </p>
                                <?php if (!empty($event['lokasi'])): ?>
                                    <p class="text-white/40 text-xs mb-3"><i class="fas fa-map-marker-alt mr-1"></i><?php echo h($event['lokasi']); ?></p>
                                <?php endif; ?>
                                <a href="#" class="inline-flex items-center gap-1 text-accent hover:text-white/70 transition-colors text-sm">
                                    Baca <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="md:col-span-3 text-center py-16 text-white/40 clean-panel rounded-lg">
                            <i class="fas fa-newspaper text-4xl mb-4 block"></i>
                            <p>Belum ada berita yang ditambahkan.</p>
                            <a href="admin/login.php" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded text-accent border border-accent/20 text-sm hover:border-accent/50">Tambah via Admin</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Tentang Section -->
        <section id="tentang" class="py-20 bg-midnight/30">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid lg:grid-cols-[1.1fr_1fr] gap-10 items-start">
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] text-white/60">Tentang SEMA</span>
                        <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Kami merajut aspirasi, menghubungkan komunitas.</h2>
                        <p class="text-white/70 max-w-2xl">
                            Menjadi jembatan antara mahasiswa, program studi, serta jajaran fakultas berarti memupuk rasa percaya. Kami menjaga ritme komunikasi, menyediakan data aspirasi yang rapi, dan menindaklanjuti isu hingga tuntas.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Aspirasi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Kolaborasi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Komunitas</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="clean-panel rounded-lg p-5 text-center">
                            <div class="text-3xl font-bold text-accent mb-1">94%</div>
                            <div class="text-xs uppercase tracking-[0.3em] text-white/60">Kepuasan</div>
                            <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-[94%] bg-accent rounded-full"></div>
                            </div>
                        </div>
                        <div class="clean-panel rounded-lg p-5 text-center">
                            <div class="text-3xl font-bold text-accent mb-1">120+</div>
                            <div class="text-xs uppercase tracking-[0.3em] text-white/60">Kolaborator</div>
                            <p class="mt-2 text-xs text-white/50">12 komunitas</p>
                        </div>
                        <div class="clean-panel rounded-lg p-5 text-center">
                            <div class="text-3xl font-bold text-accent mb-1">50+</div>
                            <div class="text-xs uppercase tracking-[0.3em] text-white/60">Kegiatan</div>
                            <p class="mt-2 text-xs text-white/50">Per tahun</p>
                        </div>
                        <div class="clean-panel rounded-lg p-5 text-center">
                            <div class="text-3xl font-bold text-accent mb-1">5</div>
                            <div class="text-xs uppercase tracking-[0.3em] text-white/60">Bidang Aktif</div>
                            <p class="mt-2 text-xs text-white/50">Berjalan optimal</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kontak Section -->
        <section id="kontak" class="py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Kontak Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Mari ngobrol soal ide baru.</h2>
                    <p class="text-white/70 max-w-xl mx-auto">Punya pertanyaan, aspirasi, atau ide kolaborasi? Jangan ragu untuk menghubungi kami.</p>
                </div>
                <div class="grid lg:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <!-- Info Kontak -->
                    <div class="space-y-6">
                        <div class="clean-panel rounded-lg p-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-accent"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40">Lokasi</p>
                                <p class="text-white/80 text-sm">Gedung Bonaventura Lt. 4, Kampus FTI UAJY</p>
                            </div>
                        </div>
                        <div class="clean-panel rounded-lg p-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-accent"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40">Email</p>
                                <p class="text-white/80 text-sm">sema@fti.uajy.ac.id</p>
                            </div>
                        </div>
                        <div class="clean-panel rounded-lg p-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-accent"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40">Telepon</p>
                                <p class="text-white/80 text-sm">+62 812 3456 7890</p>
                            </div>
                        </div>
                        <div class="clean-panel rounded-lg p-5">
                            <p class="text-xs uppercase tracking-widest text-white/40 mb-3">Ikuti Kami</p>
                            <div class="flex gap-2">
                                <a href="https://instagram.com" target="_blank" class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center hover:bg-accent/20 transition-colors">
                                    <i class="fab fa-instagram text-accent"></i>
                                </a>
                                <a href="#" class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center hover:bg-accent/20 transition-colors">
                                    <i class="fab fa-line text-accent"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Form -->
                    <div class="clean-panel rounded-lg p-8">
                        <form id="contactForm" class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-white/80 mb-1">Nama Lengkap</label>
                                <input type="text" id="name" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:border-accent focus:outline-none transition-colors" placeholder="Masukkan nama">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-white/80 mb-1">Email</label>
                                <input type="email" id="email" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:border-accent focus:outline-none transition-colors" placeholder="nama@email.com">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-white/80 mb-1">Pesan</label>
                                <textarea id="message" rows="4" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:border-accent focus:outline-none transition-colors resize-none" placeholder="Tulis pesan Anda..."></textarea>
                            </div>
                            <button type="submit" class="w-full px-6 py-3 bg-accent text-night font-semibold rounded-lg hover:opacity-90 transition-opacity">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-night/90 py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="asset/img/icon.png" alt="Logo" class="h-12 w-12 rounded-full border border-white/10">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-white/60">FTI UAJY</p>
                            <p class="font-semibold">SENAT MAHASISWA</p>
                        </div>
                    </div>
                    <p class="text-white/60 text-sm leading-relaxed">
                        Wadah aspirasi dan kreativitas mahasiswa Fakultas Teknologi Industri UAJY.
                    </p>
                </div>
                
                <!-- Links -->
                <div>
                    <h4 class="font-semibold mb-4">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-white/60 hover:text-white transition-colors text-sm">Home</a></li>
                        <li><a href="#bidang" class="text-white/60 hover:text-white transition-colors text-sm">Bidang</a></li>
                        <li><a href="#komunitas" class="text-white/60 hover:text-white transition-colors text-sm">Komunitas</a></li>
                        <li><a href="#berita" class="text-white/60 hover:text-white transition-colors text-sm">Berita</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Informasi</h4>
                    <ul class="space-y-2">
                        <li><a href="#tentang" class="text-white/60 hover:text-white transition-colors text-sm">Tentang Kami</a></li>
                        <li><a href="#kontak" class="text-white/60 hover:text-white transition-colors text-sm">Kontak</a></li>
                        <li><a href="admin/dashboard.php" class="text-white/60 hover:text-white transition-colors text-sm">Admin Login</a></li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="font-semibold mb-4">Media Sosial</h4>
                    <div class="flex gap-2">
                        <a href="https://instagram.com" target="_blank" class="w-9 h-9 bg-white/5 rounded-lg flex items-center justify-center hover:bg-accent/20 transition-colors">
                            <i class="fab fa-instagram text-accent"></i>
                        </a>
                        <a href="#" class="w-9 h-9 bg-white/5 rounded-lg flex items-center justify-center hover:bg-accent/20 transition-colors">
                            <i class="fab fa-line text-accent"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-white/40 text-sm">
                    © 2025 Senat Mahasiswa FTI UAJY. Developed by KOMINFO SEMA FTI UAJY
                </p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-tide text-night rounded-full shadow-lg flex items-center justify-center opacity-0 invisible transition-all duration-300 hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript — 100% sama dengan versi asli, tidak ada perubahan -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading Screen Simulation
            const loadingScreen = document.getElementById('loadingScreen');
            const loadingProgress = document.getElementById('loadingProgress');
            const loadingMessage = document.getElementById('loadingMessage');
            const mainContent = document.querySelector('.main-content');
            
            const messages = [
                { text: 'Memuat konten...', progress: 20 },
                { text: 'Menyiapkan antarmuka...', progress: 40 },
                { text: 'Memuat gambar dan aset...', progress: 60 },
                { text: 'Hampir selesai...', progress: 80 },
                { text: 'Selamat datang di SEMA FTI!', progress: 100 }
            ];
            
            let currentMessageIndex = 0;
            
            function updateLoading() {
                if (currentMessageIndex < messages.length) {
                    const msg = messages[currentMessageIndex];
                    loadingMessage.innerHTML = `<i class="fas fa-circle-notch fa-spin mr-2"></i> ${msg.text}`;
                    loadingProgress.style.width = msg.progress + '%';
                    
                    currentMessageIndex++;
                    
                    if (currentMessageIndex < messages.length) {
                        setTimeout(updateLoading, 800);
                    } else {
                        setTimeout(() => {
                            loadingScreen.classList.add('fade-out');
                            mainContent.classList.add('loaded');
                            setTimeout(() => {
                                loadingScreen.style.display = 'none';
                            }, 500);
                        }, 1000);
                    }
                }
            }
            
            setTimeout(updateLoading, 500);
            
            // Navbar Transparency on Scroll
            const mainHeader = document.getElementById('mainHeader');
            const heroSection = document.getElementById('home');

            function updateNavbarOnScroll() {
                if (window.scrollY > 60) {
                    mainHeader.style.background = 'rgba(5,5,5,0.85)';
                    mainHeader.style.backdropFilter = 'blur(20px)';
                    mainHeader.style.webkitBackdropFilter = 'blur(20px)';
                    mainHeader.style.borderBottomColor = 'rgba(255,255,255,0.1)';
                } else {
                    mainHeader.style.background = 'transparent';
                    mainHeader.style.backdropFilter = 'none';
                    mainHeader.style.webkitBackdropFilter = 'none';
                    mainHeader.style.borderBottomColor = 'transparent';
                }
            }

            window.addEventListener('scroll', updateNavbarOnScroll, { passive: true });
            updateNavbarOnScroll();

            // Mobile Menu
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuOverlay = document.getElementById('menuOverlay');
            const mobileMenuPanel = document.getElementById('mobileMenuPanel');
            
            function openMobileMenu() {
                mobileMenu.classList.remove('hidden');
                setTimeout(() => {
                    mobileMenuPanel.classList.add('open');
                }, 10);
                document.body.style.overflow = 'hidden';
            }
            
            function closeMobileMenuFunc() {
                mobileMenuPanel.classList.remove('open');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }
            
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', openMobileMenu);
                closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
                menuOverlay.addEventListener('click', closeMobileMenuFunc);
                
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', closeMobileMenuFunc);
                });
            }
            
            // Hero Slider - Auto-sliding images
            const heroSlider = document.getElementById('heroSlider');
            const heroDots = document.querySelectorAll('.hero-dot');
            let currentHeroSlide = 0;
            const totalHeroSlides = 3;
            
            function updateHeroSlider() {
                if (heroSlider) {
                    heroSlider.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
                    
                    heroDots.forEach((dot, index) => {
                        if (index === currentHeroSlide) {
                            dot.classList.add('bg-accent', 'w-6');
                            dot.classList.remove('bg-white/40', 'w-2');
                        } else {
                            dot.classList.remove('bg-accent', 'w-6');
                            dot.classList.add('bg-white/40', 'w-2');
                        }
                    });
                }
            }
            
            if (heroSlider) {
                updateHeroSlider();
                
                heroDots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        currentHeroSlide = index;
                        updateHeroSlider();
                    });
                });
                
                // Auto-slide every 5 seconds
                setInterval(() => {
                    currentHeroSlide = (currentHeroSlide + 1) % totalHeroSlides;
                    updateHeroSlider();
                }, 5000);
            }
            
            // Back to Top
            const backToTop = document.getElementById('backToTop');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTop.classList.remove('opacity-0', 'invisible');
                    backToTop.classList.add('opacity-100', 'visible');
                } else {
                    backToTop.classList.remove('opacity-100', 'visible');
                    backToTop.classList.add('opacity-0', 'invisible');
                }
            });
            
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Form
            const contactForm = document.getElementById('contactForm');
            
            if (contactForm) {
                contactForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const button = contactForm.querySelector('button[type="submit"]');
                    const originalText = button.textContent;
                    
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
                    button.disabled = true;
                    
                    setTimeout(() => {
                        button.innerHTML = '✓ Terkirim';
                        button.style.backgroundColor = '#10B981';
                        
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.style.backgroundColor = '';
                            button.disabled = false;
                            contactForm.reset();
                        }, 2000);
                    }, 1500);
                });
            }
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    if (href === '#') return;
                    
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.getElementById(href.substring(1));
                        
                        if (target) {
                            const headerHeight = document.querySelector('header').offsetHeight;
                            const targetPosition = target.offsetTop - headerHeight;
                            
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>