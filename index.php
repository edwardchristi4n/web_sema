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
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        blush: "#F4DBD7",
                        night: "#050505",
                        ember: "#F15A29",
                        depth: "#0F1B5B",
                        tide: "#0FD7D6",
                    },
                    fontFamily: {
                        display: ['"Source Sans 3"', "sans-serif"],
                        body: ['"Source Sans 3"', "sans-serif"],
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -10px rgba(15, 215, 214, 0.2);
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
            border: 3px solid rgba(15, 215, 214, 0.2);
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
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-night/90 backdrop-blur-md">
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
                    <a href="#home" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Home</a>
                    <a href="#bidang" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Bidang</a>
                    <a href="#komunitas" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Komunitas</a>
                    <a href="#berita" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Berita</a>
                    <a href="#tentang" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Tentang</a>
                    <a href="#kontak" class="px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-smooth">Kontak</a>
                    <a href="admin/dashboard.php" class="ml-4 px-4 py-2 bg-tide/20 text-tide hover:bg-tide/30 rounded-lg transition-smooth">
                        Admin
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
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-night-light border-l border-white/10 p-6 mobile-menu" id="mobileMenuPanel">
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
                <a href="#home" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Home</a>
                <a href="#bidang" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Bidang</a>
                <a href="#komunitas" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Komunitas</a>
                <a href="#berita" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Berita</a>
                <a href="#tentang" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Tentang</a>
                <a href="#kontak" class="block py-3 px-4 text-white/70 hover:text-white hover:bg-white/5 rounded-lg">Kontak</a>
                <a href="admin/dashboard.php" class="block py-3 px-4 mt-4 bg-tide/20 text-tide hover:bg-tide/30 rounded-lg">Admin Login</a>
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
    <main id="main-content" class="pt-20 main-content">
        <!-- Hero Section -->
        <section id="home" class="relative overflow-hidden py-16 lg:py-24">
            <div class="absolute inset-0 opacity-30 pointer-events-none">
                <div class="absolute -right-20 top-10 h-64 w-64 rounded-full bg-tide/20 blur-3xl"></div>
                <div class="absolute -left-20 bottom-10 h-72 w-72 rounded-full bg-blush/20 blur-3xl"></div>
            </div>
            
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/10 bg-white/5 mb-6">
                            <span class="h-2 w-2 rounded-full bg-ember animate-pulse"></span>
                            <span class="text-xs uppercase tracking-[0.5em] text-white/60">SENAT MAHASISWA</span>
                        </div>
                        
                        <h1 class="font-display text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight mb-6">
                            Organisasi wadah kreatif untuk komunitas non akademik mahasiswa FTI UAJY.
                        </h1>
                        
                        <p class="text-lg text-white/80 mb-8 max-w-2xl lg:mx-0 mx-auto">
                            SEMA hadir dengan wajah baru kolaboratif, hangat, dan responsif terhadap isu kampus. Kami merancang program strategis, merayakan komunitas, dan memastikan suara mahasiswa terdengar jelas.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                            <a href="#berita" class="group flex items-center justify-center gap-2 px-8 py-4 bg-tide text-night font-semibold rounded-full hover:shadow-[0_0_30px_rgba(15,215,214,0.4)] transition-all">
                                Jelajahi Agenda
                                <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </a>
                            <a href="#komunitas" class="px-8 py-4 border border-white/20 rounded-full font-semibold hover:border-white hover:bg-white/5 transition-all">
                                Temui Komunitas
                            </a>
                        </div>
                        
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 max-w-2xl mx-auto lg:mx-0">
                            <div class="p-4 border border-white/5 bg-white/5 rounded-2xl">
                                <div class="text-2xl font-bold text-tide">12+</div>
                                <div class="text-xs uppercase tracking-wider text-white/60">Komunitas</div>
                            </div>
                            <div class="p-4 border border-white/5 bg-white/5 rounded-2xl">
                                <div class="text-2xl font-bold text-ember">50+</div>
                                <div class="text-xs uppercase tracking-wider text-white/60">Kegiatan</div>
                            </div>
                            <div class="p-4 border border-white/5 bg-white/5 rounded-2xl">
                                <div class="text-2xl font-bold text-blush">1000+</div>
                                <div class="text-xs uppercase tracking-wider text-white/60">Mahasiswa</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Content - Image Slider -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-tide/20 via-transparent to-ember/20 blur-3xl"></div>
                        <div class="relative rounded-[2rem] overflow-hidden border border-white/10 bg-night/40 shadow-2xl shadow-tide/20">
                            <div class="relative h-[500px] overflow-hidden">
                                <div id="slider" class="flex transition-transform duration-500 h-full">
                                    <!-- Slide 1 -->
                                    <div class="w-full h-full flex-shrink-0 relative">
                                        <img src="asset/img/seminar%20backdrop.JPG" alt="Seminar SEMA" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-night/90 via-night/30 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-6">
                                            <p class="text-xs uppercase tracking-[0.5em] text-white/60 mb-2">Dokumentasi Lapangan</p>
                                            <h3 class="text-2xl font-semibold mb-2">Seminar Kolaborasi</h3>
                                            <p class="text-white/70">SEMA FTI memfasilitasi dialog lintas program studi.</p>
                                        </div>
                                    </div>
                                    <!-- Slide 2 -->
                                    <div class="w-full h-full flex-shrink-0 relative">
                                        <img src="asset/img/spfest.JPG" alt="Sparkfest #13" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-night/90 via-night/30 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-6">
                                            <p class="text-xs uppercase tracking-[0.5em] text-white/60 mb-2">Dokumentasi Lapangan</p>
                                            <h3 class="text-2xl font-semibold mb-2">Sparkfest #13</h3>
                                            <p class="text-white/70">Festival komunitas dengan inovasi dan seni.</p>
                                        </div>
                                    </div>
                                    <!-- Slide 3 -->
                                    <div class="w-full h-full flex-shrink-0 relative">
                                        <img src="asset/img/baksos.JPG" alt="Bakti Sosial" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-night/90 via-night/30 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-6">
                                            <p class="text-xs uppercase tracking-[0.5em] text-white/60 mb-2">Dokumentasi Lapangan</p>
                                            <h3 class="text-2xl font-semibold mb-2">Baksos SEMA</h3>
                                            <p class="text-white/70">Kegiatan pengabdian bakti sosial.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Slider Controls -->
                                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                                    <button class="slider-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
                                    <button class="slider-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
                                    <button class="slider-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/60 transition-all"></button>
                                </div>
                                
                                <button id="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all">
                                    <i class="fas fa-chevron-left text-white"></i>
                                </button>
                                <button id="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all">
                                    <i class="fas fa-chevron-right text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bidang Section -->
        <section id="bidang" class="py-20 bg-night-light/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Bidang Senat Mahasiswa</span>
                    <h2 class="font-display text-3xl md:text-4xl font-bold mt-4 mb-4">Temui Bidang yang Ada di SEMA</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Lima bidang utama yang bekerja sinergis untuk melayani dan mengembangkan potensi mahasiswa FTI UAJY.
                    </p>
                </div>
                
                <!-- Row 1: 3 cards -->
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Card 1: Sosial Masyarakat -->
                    <div class="card-hover border border-white/5 bg-white/5 rounded-3xl p-8">
                        <div class="w-14 h-14 bg-ember/20 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-hand-holding-heart text-ember text-2xl"></i>
                        </div>
                        <span class="text-xs uppercase tracking-[0.5em] text-ember">Bidang Sosial Masyarakat</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3">Sosial Masyarakat</h3>
                        <p class="text-white/70 mb-6">
                            Merancang gerakan sosial dan advokasi, memastikan kebijakan kampus berpihak pada mahasiswa dan masyarakat sekitar.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-ember text-sm"></i>
                                <span class="text-white/80">Klinik Aspirasi Mingguan</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-ember text-sm"></i>
                                <span class="text-white/80">Kampanye Aksi Nyata</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=3" class="inline-flex items-center gap-2 text-ember hover:text-ember/80 transition-colors">
                            Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 2: Kominfo -->
                    <div class="card-hover border border-white/5 bg-white/5 rounded-3xl p-8">
                        <div class="w-14 h-14 bg-tide/20 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-broadcast-tower text-tide text-2xl"></i>
                        </div>
                        <span class="text-xs uppercase tracking-[0.5em] text-tide">Bidang Kominfo</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3">Studio Komunikasi</h3>
                        <p class="text-white/70 mb-6">
                            Mengemas narasi positif, membangun dokumentasi visual, dan menyebarkan informasi akurat.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-tide text-sm"></i>
                                <span class="text-white/80">Produksi Weekly Digest</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-tide text-sm"></i>
                                <span class="text-white/80">Pelatihan desain & audio</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=5" class="inline-flex items-center gap-2 text-tide hover:text-tide/80 transition-colors">
                            Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 3: Usaha Dana -->
                    <div class="card-hover border border-white/5 bg-white/5 rounded-3xl p-8">
                        <div class="w-14 h-14 bg-yellow-400/20 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-coins text-yellow-400 text-2xl"></i>
                        </div>
                        <span class="text-xs uppercase tracking-[0.5em] text-yellow-400">Bidang Usaha Dana</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3">Usaha Dana</h3>
                        <p class="text-white/70 mb-6">
                            Mengelola dan mengembangkan sumber pendanaan organisasi untuk mendukung seluruh program dan kegiatan SEMA.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-yellow-400 text-sm"></i>
                                <span class="text-white/80">Pengelolaan Dana Organisasi</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-yellow-400 text-sm"></i>
                                <span class="text-white/80">Program Wirausaha Mahasiswa</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=4" class="inline-flex items-center gap-2 text-yellow-400 hover:text-yellow-400/80 transition-colors">
                            Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Row 2: 2 cards centered -->
                <div class="grid md:grid-cols-2 gap-8 md:max-w-3xl md:mx-auto">
                    <!-- Card 4: Minat Bakat -->
                    <div class="card-hover border border-white/5 bg-white/5 rounded-3xl p-8">
                        <div class="w-14 h-14 bg-blush/20 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-star text-blush text-2xl"></i>
                        </div>
                        <span class="text-xs uppercase tracking-[0.5em] text-blush">Bidang Minat Bakat</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3">Minat &amp; Bakat</h3>
                        <p class="text-white/70 mb-6">
                            Memfasilitasi pengembangan potensi dan bakat mahasiswa melalui berbagai kegiatan, kompetisi, dan program kreatif.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-blush text-sm"></i>
                                <span class="text-white/80">Festival Seni & Olahraga</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-blush text-sm"></i>
                                <span class="text-white/80">Kompetisi & Showcase Bakat</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=2" class="inline-flex items-center gap-2 text-blush hover:text-blush/80 transition-colors">
                            Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 5: Pengurus Harian -->
                    <div class="card-hover border border-white/5 bg-white/5 rounded-3xl p-8">
                        <div class="w-14 h-14 bg-purple-400/20 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-crown text-purple-400 text-2xl"></i>
                        </div>
                        <span class="text-xs uppercase tracking-[0.5em] text-purple-400">Pengurus Harian</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3">Pengurus Harian</h3>
                        <p class="text-white/70 mb-6">
                            Mengoordinasikan seluruh kegiatan operasional SEMA, memimpin rapat, dan memastikan sinergi antar bidang berjalan optimal.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-purple-400 text-sm"></i>
                                <span class="text-white/80">Koordinasi Antar Bidang</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-purple-400 text-sm"></i>
                                <span class="text-white/80">Perencanaan Program Strategis</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=1" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-400/80 transition-colors">
                            Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
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
                    <h2 class="font-display text-3xl md:text-4xl font-bold mt-4 mb-4">Komunitas di FTI UAJY</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Bergabunglah dengan komunitas yang sesuai minat dan bakatmu. Setiap komunitas adalah ruang untuk bertumbuh, berkolaborasi, dan berprestasi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Badminton -->
                    <div class="card-hover group relative border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-tide to-tide/30"></div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-tide/10 rounded-2xl flex items-center justify-center border border-tide/20 group-hover:bg-tide/20 transition-colors">
                                    <i class="fas fa-feather-alt text-tide text-2xl"></i>
                                </div>
                                <span class="px-3 py-1 bg-tide/10 text-tide text-xs font-medium rounded-full border border-tide/20">Olahraga</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Badminton</h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-6">
                                Komunitas bulu tangkis FTI UAJY yang aktif berlatih dan mengikuti kompetisi antar kampus. Terbuka untuk semua level, dari pemula hingga atlet.
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span class="flex items-center gap-1.5"><i class="fas fa-users"></i> Aktif</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-trophy"></i> Kompetitif</span>
                            </div>
                        </div>
                    </div>

                    <!-- SIKMA -->
                    <div class="card-hover group relative border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-ember to-ember/30"></div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-ember/10 rounded-2xl flex items-center justify-center border border-ember/20 group-hover:bg-ember/20 transition-colors">
                                    <i class="fas fa-music text-ember text-2xl"></i>
                                </div>
                                <span class="px-3 py-1 bg-ember/10 text-ember text-xs font-medium rounded-full border border-ember/20">Seni & Budaya</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">SIKMA</h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-6">
                                Komunitas seni dan musik mahasiswa FTI yang mewadahi kreativitas di bidang musik, vokal, dan pertunjukan seni panggung.
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span class="flex items-center gap-1.5"><i class="fas fa-users"></i> Aktif</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-guitar"></i> Kreatif</span>
                            </div>
                        </div>
                    </div>

                    <!-- Texere (Basket) -->
                    <div class="card-hover group relative border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-400 to-orange-400/30"></div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-orange-400/10 rounded-2xl flex items-center justify-center border border-orange-400/20 group-hover:bg-orange-400/20 transition-colors">
                                    <i class="fas fa-basketball text-orange-400 text-2xl"></i>
                                </div>
                                <span class="px-3 py-1 bg-orange-400/10 text-orange-400 text-xs font-medium rounded-full border border-orange-400/20">Olahraga</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Texere</h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-6">
                                Tim basket FTI UAJY yang bersaing di berbagai turnamen kampus dan antar universitas. Texere membangun kekompakan dan sportivitas.
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span class="flex items-center gap-1.5"><i class="fas fa-users"></i> Aktif</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-trophy"></i> Kompetitif</span>
                            </div>
                        </div>
                    </div>

                    <!-- FTI Image -->
                    <div class="card-hover group relative border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 to-purple-400/30"></div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-purple-400/10 rounded-2xl flex items-center justify-center border border-purple-400/20 group-hover:bg-purple-400/20 transition-colors">
                                    <i class="fas fa-camera text-purple-400 text-2xl"></i>
                                </div>
                                <span class="px-3 py-1 bg-purple-400/10 text-purple-400 text-xs font-medium rounded-full border border-purple-400/20">Fotografi & Videografi</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">FTI Image</h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-6">
                                Komunitas fotografi dan videografi FTI yang mendokumentasikan momen kampus, mengadakan pameran foto, dan mengembangkan skill visual storytelling.
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span class="flex items-center gap-1.5"><i class="fas fa-users"></i> Aktif</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-image"></i> Visual</span>
                            </div>
                        </div>
                    </div>

                    <!-- Futsal -->
                    <div class="card-hover group relative border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-green-400/30"></div>
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-green-400/10 rounded-2xl flex items-center justify-center border border-green-400/20 group-hover:bg-green-400/20 transition-colors">
                                    <i class="fas fa-futbol text-green-400 text-2xl"></i>
                                </div>
                                <span class="px-3 py-1 bg-green-400/10 text-green-400 text-xs font-medium rounded-full border border-green-400/20">Olahraga</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Futsal</h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-6">
                                Komunitas futsal FTI UAJY yang rutin berlatih dan aktif mengikuti turnamen. Membangun semangat tim, strategi, dan solidaritas antar mahasiswa.
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/40">
                                <span class="flex items-center gap-1.5"><i class="fas fa-users"></i> Aktif</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-trophy"></i> Kompetitif</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Card -->
                    <div class="relative border border-dashed border-white/10 rounded-3xl p-8 flex flex-col items-center justify-center text-center hover:bg-white/5 transition-colors group cursor-pointer">
                        <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-tide/10 transition-colors border border-white/10">
                            <i class="fas fa-plus text-white/40 text-2xl group-hover:text-tide transition-colors"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white/70 mb-2 group-hover:text-white transition-colors">Komunitas Lainnya</h3>
                        <p class="text-white/40 text-sm leading-relaxed">
                            Ada komunitas lain yang ingin kamu ketahui? Hubungi kami untuk informasi selengkapnya.
                        </p>
                        <a href="#kontak" class="mt-6 px-5 py-2.5 bg-tide/10 text-tide border border-tide/20 rounded-xl text-sm font-medium hover:bg-tide/20 transition-colors">
                            Hubungi Kami
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
                        <h2 class="font-display text-3xl md:text-4xl font-bold mt-4">Update Terbaru dari SEMA</h2>
                    </div>
                    <div class="flex items-center gap-2 text-white/60 mt-4 md:mt-0">
                        <i class="fas fa-sync-alt text-sm"></i>
                        <span class="text-sm">Diperbarui setiap pekan</span>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <?php if ($events): ?>
                        <?php 
                        $colors = [
                            ['bg' => 'from-tide/20', 'badge_bg' => 'bg-tide/20', 'badge_text' => 'text-tide', 'link' => 'text-tide hover:text-tide/80'],
                            ['bg' => 'from-ember/20', 'badge_bg' => 'bg-ember/20', 'badge_text' => 'text-ember', 'link' => 'text-ember hover:text-ember/80'],
                            ['bg' => 'from-blush/40', 'badge_bg' => 'bg-blush/20', 'badge_text' => 'text-blush', 'link' => 'text-blush hover:text-blush/80'],
                        ];
                        foreach ($events as $i => $event):
                            $c = $colors[$i % 3];
                        ?>
                        <article class="card-hover border border-white/5 bg-white/5 rounded-3xl overflow-hidden">
                            <?php if (!empty($event['foto_event'])): ?>
                                <div class="aspect-[4/3] overflow-hidden">
                                    <img src="<?php echo h($event['foto_event']); ?>" alt="<?php echo h($event['judul']); ?>" class="w-full h-full object-cover">
                                </div>
                            <?php else: ?>
                                <div class="aspect-[4/3] bg-gradient-to-br <?php echo $c['bg']; ?> to-transparent"></div>
                            <?php endif; ?>
                            <div class="p-8">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-xs uppercase tracking-[0.5em] text-white/60"><?php echo formatTanggal($event['tanggal']); ?></span>
                                    <span class="px-3 py-1 <?php echo $c['badge_bg']; ?> <?php echo $c['badge_text']; ?> text-xs font-medium rounded-full">Event</span>
                                </div>
                                <h3 class="text-xl font-semibold mb-3"><?php echo h($event['judul']); ?></h3>
                                <p class="text-white/70 mb-4">
                                    <?php echo h(mb_substr($event['deskripsi'] ?? '', 0, 100)) . (mb_strlen($event['deskripsi'] ?? '') > 100 ? '...' : ''); ?>
                                </p>
                                <?php if (!empty($event['lokasi'])): ?>
                                    <p class="text-white/40 text-xs mb-4"><i class="fas fa-map-marker-alt mr-1"></i><?php echo h($event['lokasi']); ?></p>
                                <?php endif; ?>
                                <a href="#" class="inline-flex items-center gap-2 <?php echo $c['link']; ?> transition-colors">
                                    Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="md:col-span-3 text-center py-16 text-white/40">
                            <i class="fas fa-newspaper text-4xl mb-4 block"></i>
                            <p>Belum ada berita yang ditambahkan.</p>
                            <a href="admin/login.php" class="mt-4 inline-block text-tide hover:underline text-sm">Tambah via Admin Panel</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Tentang Section -->
        <section id="tentang" class="py-20 bg-night-light/50">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Tentang SEMA</span>
                    <h2 class="font-display text-3xl md:text-4xl font-bold mt-4 mb-4">Kami merajut aspirasi, menghubungkan komunitas.</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Menjadi jembatan antara mahasiswa, program studi, serta jajaran fakultas berarti memupuk rasa percaya. Kami menjaga ritme komunikasi, menyediakan data aspirasi yang rapi, dan menindaklanjuti isu hingga tuntas.
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="border border-white/5 bg-white/5 rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-tide mb-2">94%</div>
                        <div class="text-xs uppercase tracking-[0.4em] text-white/60">Kepuasan</div>
                        <div class="mt-3 h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full w-[94%] bg-tide rounded-full"></div>
                        </div>
                    </div>
                    <div class="border border-white/5 bg-white/5 rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-ember mb-2">120+</div>
                        <div class="text-xs uppercase tracking-[0.4em] text-white/60">Kolaborator</div>
                        <p class="mt-2 text-xs text-white/60">Dari 12 komunitas</p>
                    </div>
                    <div class="border border-white/5 bg-white/5 rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-blush mb-2">50+</div>
                        <div class="text-xs uppercase tracking-[0.4em] text-white/60">Kegiatan</div>
                        <p class="mt-2 text-xs text-white/60">Per tahun</p>
                    </div>
                    <div class="border border-white/5 bg-white/5 rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-purple-400 mb-2">5</div>
                        <div class="text-xs uppercase tracking-[0.4em] text-white/60">Bidang</div>
                        <p class="mt-2 text-xs text-white/60">Aktif berjalan</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kontak Section -->
        <section id="kontak" class="py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12">
                    <span class="text-xs uppercase tracking-[0.5em] text-white/60">Kontak Kami</span>
                    <h2 class="font-display text-3xl md:text-4xl font-bold mt-4 mb-4">Mari ngobrol soal ide baru.</h2>
                    <p class="text-white/70 max-w-xl mx-auto">Punya pertanyaan, aspirasi, atau ide kolaborasi? Jangan ragu untuk menghubungi kami.</p>
                </div>
                <div class="grid lg:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <!-- Info Kontak -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-tide/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-tide text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Lokasi</p>
                                <p class="text-white/80">Gedung Bonaventura Lt. 4, Kampus FTI UAJY</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-ember/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-ember text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Email</p>
                                <p class="text-white/80"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="384b5d5559165e4c51784d59524116595b16515c">[email&#160;protected]</a></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blush/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blush text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Telepon</p>
                                <p class="text-white/80">+62 812 3456 7890</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-white/10">
                            <p class="text-xs uppercase tracking-widest text-white/40 mb-4">Ikuti Kami</p>
                            <div class="flex gap-3">
                                <a href="/cdn-cgi/l/email-protection#3d5b4954485c5744134e58535c49505c555c4e544e4a5c7d5a505c5451135e5250" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-ember/20 transition-colors">
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
                    <!-- Form -->
                    <div class="border border-white/5 bg-white/5 rounded-3xl p-8">
                        <form id="contactForm" class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-white/80 mb-2">Nama Lengkap</label>
                                <input type="text" id="name" class="w-full px-4 py-3 bg-night border border-white/10 rounded-xl text-white placeholder-white/40 focus:border-tide focus:outline-none transition-colors" placeholder="Masukkan nama">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-white/80 mb-2">Email</label>
                                <input type="email" id="email" class="w-full px-4 py-3 bg-night border border-white/10 rounded-xl text-white placeholder-white/40 focus:border-tide focus:outline-none transition-colors" placeholder="nama@email.com">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-white/80 mb-2">Pesan</label>
                                <textarea id="message" rows="4" class="w-full px-4 py-3 bg-night border border-white/10 rounded-xl text-white placeholder-white/40 focus:border-tide focus:outline-none transition-colors resize-none" placeholder="Tulis pesan Anda..."></textarea>
                            </div>
                            <button type="submit" class="w-full px-6 py-4 bg-tide text-night font-semibold rounded-xl hover:shadow-[0_0_30px_rgba(15,215,214,0.3)] transition-all">
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
                            <p class="font-display font-semibold">SENAT MAHASISWA</p>
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
                        <li><a href="#home" class="text-white/60 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#bidang" class="text-white/60 hover:text-white transition-colors">Bidang</a></li>
                        <li><a href="#komunitas" class="text-white/60 hover:text-white transition-colors">Komunitas</a></li>
                        <li><a href="#berita" class="text-white/60 hover:text-white transition-colors">Berita</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Informasi</h4>
                    <ul class="space-y-2">
                        <li><a href="#tentang" class="text-white/60 hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#kontak" class="text-white/60 hover:text-white transition-colors">Kontak</a></li>
                        <li><a href="admin/dashboard.php" class="text-white/60 hover:text-white transition-colors">Admin Login</a></li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="font-semibold mb-4">Media Sosial</h4>
                    <div class="flex gap-3">
                        <a href="/cdn-cgi/l/email-protection#9bfdeff2eefaf1e2b5e8fef5faeff6faf3fae8f2e8ecfadbfcf6faf2f7b5f8f4f6" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-ember/20 transition-colors">
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
            
            // Slider
            const slider = document.getElementById('slider');
            const dots = document.querySelectorAll('.slider-dot');
            const prevBtn = document.getElementById('prevSlide');
            const nextBtn = document.getElementById('nextSlide');
            let currentSlide = 0;
            const totalSlides = 3;
            
            function updateSlider() {
                if (slider) {
                    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                    
                    dots.forEach((dot, index) => {
                        if (index === currentSlide) {
                            dot.classList.add('bg-tide', 'w-6');
                            dot.classList.remove('bg-white/40', 'w-2');
                        } else {
                            dot.classList.remove('bg-tide', 'w-6');
                            dot.classList.add('bg-white/40', 'w-2');
                        }
                    });
                }
            }
            
            if (slider) {
                updateSlider();
                
                prevBtn?.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    updateSlider();
                });
                
                nextBtn?.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    updateSlider();
                });
                
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        currentSlide = index;
                        updateSlider();
                    });
                });
                
                setInterval(() => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    updateSlider();
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