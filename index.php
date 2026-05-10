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
    
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        night: "#050a14",
                        midnight: "#140a05",
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
            outline: 2px solid #F97316;
            outline-offset: 2px;
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }

        
    </style>
</head>
<body class="bg-night text-white font-body antialiased">
    <!-- Skip to main content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-tide text-night px-4 py-2 rounded-lg z-50">
        Skip to main content
    </a>

    <!-- Header -->
    <header id="mainHeader" class="fixed top-0 left-0 right-0 z-50 border-b border-transparent" style="transition: background 0.4s ease, backdrop-filter 0.4s ease, border-color 0.4s ease;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="#home" class="flex items-center gap-2 sm:gap-3 group flex-shrink-0">
                    <img src="asset/img/icon.png" alt="Logo SEMA FTI UAJY" 
                         class="h-9 w-9 sm:h-12 sm:w-12 rounded-full border border-white/10 bg-white/5 p-1 object-contain transition-transform group-hover:scale-105">
                    <div class="flex flex-col justify-center">
                        <p class="font-bold text-sm sm:text-lg md:text-xl text-white leading-tight">Senat Mahasiswa</p>
                        <p class="text-[9px] sm:text-xs font-bold uppercase tracking-[0.25em] sm:tracking-[0.35em] text-accent leading-tight mt-0.5">FTI UAJY</p>
                    </div>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-2">
                    <a href="#home" class="nav-link active px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Home</a>
                    <a href="#bidang" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Bidang</a>
                    <a href="#komunitas" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Komunitas</a>
                    <a href="#berita" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Berita</a>
                    <a href="#galeri" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Galeri</a>
                    <a href="#kontak" class="nav-link px-4 py-2 text-white/70 hover:text-white hover:bg-white/5 rounded-full transition-smooth">Kontak</a>
                </nav>
                
                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="flex md:hidden items-center justify-center w-10 h-10 rounded-lg hover:bg-white/10 flex-shrink-0" aria-label="Open menu">
                    <i class="fas fa-bars text-xl text-white"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="fixed inset-0 z-[60] md:hidden hidden" id="mobileMenu">
        <div class="absolute inset-0 bg-black/80" id="menuOverlay"></div>
        <div class="absolute right-0 top-0 bottom-0 w-72 sm:w-80 bg-[#0d0d0d] border-l border-white/10 p-6 mobile-menu" id="mobileMenuPanel">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <img src="asset/img/icon.png" alt="Logo" class="h-10 w-10 rounded-full">
                    <div class="flex flex-col justify-center">
                        <p class="font-bold text-lg text-white leading-tight">Senat Mahasiswa</p>
                        <p class="text-[10px] font-bold uppercase tracking-[0.35em] text-accent leading-tight mt-0.5">FTI UAJY</p>
                    </div>
                </div>
                <button id="closeMobileMenu" class="p-2 rounded-lg hover:bg-white/10">
                    <i class="fas fa-times text-white text-xl"></i>
                </button>
            </div>
            
            <nav class="space-y-1">
                <a href="#home" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Home</a>
                <a href="#bidang" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Bidang</a>
                <a href="#komunitas" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Komunitas</a>
                <a href="#berita" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Berita</a>
                <a href="#tentang" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Tentang</a>
                <a href="#kontak" class="block py-3 px-4 text-white/80 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Kontak</a>
                <a href="admin/dashboard.php" class="block py-3 px-4 mt-3 rounded-xl bg-orange-500 text-white font-semibold text-center hover:bg-orange-400 transition-colors">Admin Panel</a>
            </nav>
            
            <div class="absolute bottom-8 left-6 right-6">
                <div class="border-t border-white/10 pt-6">
                    <div class="flex justify-center gap-4">
                        <a href="https://instagram.com" target="_blank" class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center hover:bg-accent/20 transition-colors">
                            <i class="fab fa-instagram text-accent"></i>
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
        <!-- Hero Section - Typography Only -->
        <section id="home" class="relative w-full min-h-screen flex items-center bg-[#050505] overflow-hidden">
            <div class="mx-auto max-w-7xl px-5 sm:px-6 w-full relative z-10 pt-24 pb-16 md:pt-32 md:pb-20">
                <div class="max-w-4xl">
                    <!-- Tag -->
                    <div class="flex items-center gap-3 mb-5" data-aos="fade-down">
                        <div class="h-4 w-1 bg-accent"></div>
                        <span class="text-[11px] sm:text-sm uppercase tracking-[0.15em] sm:tracking-[0.2em] text-accent font-semibold">#Satu Hati, Satu Jiwa, <span class="text-orange-500">FTI JAYA!</span></span>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-bold leading-tight tracking-tight mb-2 text-white" data-aos="fade-right">
                        Senat Mahasiswa
                    </h1>
                    <!-- Subtitle in accent color -->
                    <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-bold leading-tight tracking-tight mb-6 sm:mb-8 text-accent" data-aos="fade-left">
                        FTI <span class="text-orange-500">UAJY</span>
                    </h2>
                    
                    <!-- Description -->
                    <p class="text-sm sm:text-base md:text-xl text-white/70 mb-7 sm:mb-8 max-w-2xl leading-relaxed" data-aos="fade-up">
                        Senat Mahasiswa FTI UAJY merupakan organisasi kemahasiswaan non-akademik yang bergerak sebagai wadah kreatif minat &amp; bakat mahasiswa/i Fakultas Teknologi Industri di Universitas Atma Jaya Yogyakarta yang kolaboratif, kekeluargaan, dan responsif.
                    </p>
                    
                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4" data-aos="fade-up" data-aos-delay="200">
                        <a href="#tentang" class="group inline-flex items-center justify-center gap-2 px-6 py-3 sm:px-8 sm:py-4 bg-orange-500 text-white font-bold rounded-lg hover:opacity-90 transition-opacity">
                            TENTANG KAMI
                            <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </a>
                        <a href="#kontak" class="inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-4 border border-accent text-accent font-bold rounded-lg hover:bg-accent/10 hover:text-white transition-all">
                            HUBUNGI KAMI
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- Tentang Section -->
        <section id="tentang" class="py-24 bg-midnight">
            <div class="mx-auto max-w-7xl px-6">
                <!-- Section Label -->
                <div class="mb-10" data-aos="fade-up">
                    <span class="text-xs uppercase tracking-[0.5em] text-orange-500">Tentang SEMA</span>
                    <h2 class="text-3xl md:text-5xl font-bold mt-4">Kami merajut aspirasi,<br>menghubungkan komunitas.</h2>
                </div>

                <!-- 2-col: Sejarah left | Visi+Misi right -->
                <div class="grid lg:grid-cols-2 gap-10 items-start">
                    <!-- Left: Sejarah + Tags -->
                    <div data-aos="fade-right">
                        <p class="text-white/70 text-base leading-relaxed mb-6">
                            SEMA FTI UAJY (berdiri sejak 1990) hadir sebagai lembaga kemahasiswaan tingkat fakultas yang bertujuan membantu membina mahasiswa agar bermoral, berintelektual, dan berintegritas. Kami adalah wadah pengembangan potensi non-akademik serta media penyalur aspirasi mahasiswa.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-4 py-2 bg-accent/10 text-accent border border-accent/20 rounded-lg text-xs uppercase tracking-[0.35em]">Solidaritas</span>
                            <span class="px-4 py-2 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-lg text-xs uppercase tracking-[0.35em]">Potensi</span>
                            <span class="px-4 py-2 bg-accent/10 text-accent border border-accent/20 rounded-lg text-xs uppercase tracking-[0.35em]">Sosial</span>
                            <span class="px-4 py-2 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-lg text-xs uppercase tracking-[0.35em]">Kreativitas</span>
                        </div>
                    </div>

                    <!-- Right: Visi + Misi stacked -->
                    <div class="flex flex-col gap-5">
                        <!-- Visi Card -->
                        <div class="clean-panel rounded-xl p-6 border-l-4 border-accent" data-aos="fade-left" data-aos-delay="100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-eye text-accent text-sm"></i>
                                </div>
                                <h3 class="text-sm font-bold uppercase tracking-[0.3em] text-accent">Visi</h3>
                            </div>
                            <p class="text-white/80 text-sm leading-relaxed">
                                Mewujudkan SEMA FTI UAJY sebagai wadah pengembangan potensi serta menumbuhkan rasa empati dan simpati antar mahasiswa FTI UAJY.
                            </p>
                        </div>

                        <!-- Misi Card -->
                        <div class="clean-panel rounded-xl p-6 border-l-4 border-orange-500" data-aos="fade-left" data-aos-delay="150">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 bg-orange-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bullseye text-orange-500 text-sm"></i>
                                </div>
                                <h3 class="text-sm font-bold uppercase tracking-[0.3em] text-orange-500">Misi</h3>
                            </div>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3 text-sm text-white/80">
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-orange-500/20 text-orange-400 text-xs flex items-center justify-center font-bold mt-0.5">1</span>
                                    <span>Menyediakan wadah bagi mahasiswa FTI UAJY untuk mengembangkan potensi dalam bidang non-akademik.</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-white/80">
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-orange-500/20 text-orange-400 text-xs flex items-center justify-center font-bold mt-0.5">2</span>
                                    <span>Meningkatkan komunikasi dan solidaritas mahasiswa FTI UAJY melalui penyerapan aspirasi dan kegiatan mahasiswa.</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-white/80">
                                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-orange-500/20 text-orange-400 text-xs flex items-center justify-center font-bold mt-0.5">3</span>
                                    <span>Meningkatkan partisipasi seluruh mahasiswa FTI UAJY melalui kegiatan yang dilaksanakan SEMA FTI UAJY.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bidang Section -->
        <section id="bidang" class="py-20 bg-night">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12" data-aos="fade-up">
                    <span class="text-xs uppercase tracking-[0.5em] text-accent">Bidang Senat Mahasiswa</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Temui Bidang yang Ada di SEMA</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Lima bidang utama yang bekerja sinergis untuk melayani dan mengembangkan potensi mahasiswa FTI UAJY.
                    </p>
                </div>
                
                <!-- Row 1: 3 cards -->
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Card 1: Sosial Masyarakat -->
                    <div data-aos="fade-up" data-aos-delay="100" class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(16,185,129,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(16,185,129,0.12);">
                            <i class="fas fa-hand-holding-heart text-xl" style="color: #10B981;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Sosial Masyarakat</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Wadah kepedulian terhadap isu sosial, lingkungan hidup, dan pengabdian masyarakat.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #10B981;"></i>
                                <span>Bakti Sosial</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #10B981;"></i>
                                <span>Green Action</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=3" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #10B981;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 2: Kominfo -->
                    <div data-aos="fade-up" data-aos-delay="200" class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(139,92,246,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(139,92,246,0.12);">
                            <i class="fas fa-broadcast-tower text-xl" style="color: #8B5CF6;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Komunikasi Informasi</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Pusat penyebaran informasi SEMA, menampung aspirasi, dan mengelola media sosial organisasi.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #8B5CF6;"></i>
                                <span>Forum Lesehan</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #8B5CF6;"></i>
                                <span>Media SEMA FTI</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=5" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #8B5CF6;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 3: Usaha Dana -->
                    <div data-aos="fade-up" data-aos-delay="300" class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(245,158,11,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(245,158,11,0.12);">
                            <i class="fas fa-coins text-xl" style="color: #F59E0B;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Usaha Dana</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Bertanggung jawab terhadap penggalangan dana, promosi usaha, dan merchandise organisasi.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #F59E0B;"></i>
                                <span>Dapoer SEMA</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #F59E0B;"></i>
                                <span>Korsa & Kaos FTI</span>
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
                    <div data-aos="fade-up" data-aos-delay="100" class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(236,72,153,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(236,72,153,0.12);">
                            <i class="fas fa-star text-xl" style="color: #EC4899;"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Minat &amp; Bakat</h3>
                        <p class="text-white/70 mb-4 text-sm">
                            Menyediakan wadah pengembangan minat dan bakat mahasiswa di bidang seni, musik, olahraga, dan jurnalistik.
                        </p>
                        <ul class="space-y-2 mb-4">
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #EC4899;"></i>
                                <span>SPARKFEST</span>
                            </li>
                            <li class="flex items-center gap-2 text-sm text-white/70">
                                <i class="fas fa-check text-xs" style="color: #EC4899;"></i>
                                <span>Manajemen Komunitas</span>
                            </li>
                        </ul>
                        <a href="bidang-detail.php?id=2" class="inline-flex items-center gap-1 transition-colors text-sm font-medium hover:text-white/70" style="color: #EC4899;">
                            Pelajari <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <!-- Card 5: Pengurus Harian -->
                    <div data-aos="fade-up" data-aos-delay="200" class="card-hover clean-panel rounded-xl p-8" style="border-color: rgba(15,215,214,0.15);">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(15,215,214,0.12);">
                            <i class="fas fa-crown text-xl" style="color: #F97316;"></i>
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
        <section id="komunitas" class="py-20 bg-[#050505]">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-14" data-aos="fade-up">
                    <span class="text-xs uppercase tracking-[0.5em] text-orange-500">Unit Kegiatan Mahasiswa</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Komunitas di FTI UAJY</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        Bergabunglah dengan komunitas yang sesuai minat dan bakatmu untuk bertumbuh dan berprestasi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Badminton -->
                    <div data-aos="fade-up" data-aos-delay="100" class="card-hover clean-panel rounded-lg p-6">
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
                    <div data-aos="fade-up" data-aos-delay="200" class="card-hover clean-panel rounded-lg p-6">
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
                    <div data-aos="fade-up" data-aos-delay="300" class="card-hover clean-panel rounded-lg p-6">
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
                    <div data-aos="fade-up" data-aos-delay="100" class="card-hover clean-panel rounded-lg p-6">
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
                    <div data-aos="fade-up" data-aos-delay="200" class="card-hover clean-panel rounded-lg p-6">
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
                    <div data-aos="fade-up" data-aos-delay="300" class="clean-panel rounded-lg p-6 flex flex-col items-center justify-center text-center hover:border-accent/50 transition-colors">
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
        <section id="berita" class="py-20 bg-night">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12" data-aos="fade-up">
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] text-accent">Berita Terkini</span>
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
                        <article data-aos="fade-up" class="card-hover clean-panel rounded-lg overflow-hidden">
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
                        <div data-aos="fade-up" class="md:col-span-3 text-center py-16 text-white/40 clean-panel rounded-lg">
                            <i class="fas fa-newspaper text-4xl mb-4 block"></i>
                            <p>Belum ada berita yang ditambahkan.</p>
                            <a href="admin/login.php" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded text-accent border border-accent/20 text-sm hover:border-accent/50">Tambah via Admin</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        

        <!-- Kontak Section -->
        <section id="kontak" class="py-20 bg-midnight">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center mb-12" data-aos="fade-up">
                    <span class="text-xs uppercase tracking-[0.5em] text-orange-500">Kontak Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Mari ngobrol soal ide baru.</h2>
                    <p class="text-white/70 max-w-xl mx-auto">Punya pertanyaan, aspirasi, atau ide kolaborasi? Jangan ragu untuk menghubungi kami.</p>
                </div>
                <div class="grid lg:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <!-- Info Kontak -->
                    <div class="space-y-6" data-aos="fade-right">
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
                    <div data-aos="fade-left" class="clean-panel rounded-lg p-8">
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
    <footer class="border-t border-white/5 bg-[#050505] py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="asset/img/icon.png" alt="Logo" class="h-12 w-12 rounded-full border border-white/10">
                        <div class="flex flex-col justify-center">
                            <p class="font-bold text-lg text-white leading-tight">Senat Mahasiswa</p>
                            <p class="text-xs font-bold uppercase tracking-[0.35em] text-accent leading-tight mt-0.5">FTI UAJY</p>
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
            // Navbar Transparency on Scroll
            const mainHeader = document.getElementById('mainHeader');
            const heroSection = document.getElementById('home');

            function updateNavbarOnScroll() {
                if (window.scrollY > 60) {
                    mainHeader.style.background = 'rgba(5,5,5,0.4)';
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
            
            // Hero Slider - Removed as per typography-centric redesign
            
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
            // Scrollspy for Navigation Underline
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let isScrollingFromClick = false;
            let scrollTimeout;
            
            function updateScrollspy() {
                if (isScrollingFromClick) return;
                const scrollY = window.scrollY;
                const headerHeight = document.querySelector('header').offsetHeight;
                
                let currentSectionId = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - headerHeight - 100;
                    const sectionHeight = section.offsetHeight;
                    
                    if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                        currentSectionId = section.getAttribute('id');
                    }
                });
                
                if (!currentSectionId && scrollY < 300) {
                    currentSectionId = 'home';
                }
                
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (currentSectionId && link.getAttribute('href') === '#' + currentSectionId) {
                        link.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', updateScrollspy, { passive: true });
            updateScrollspy();

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    if (href === '#') return;
                    
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.getElementById(href.substring(1));
                        
                        if (target) {
                            // Stop scrollspy temporarily
                            isScrollingFromClick = true;
                            
                            // Update active link immediately
                            const navLinks = document.querySelectorAll('.nav-link');
                            navLinks.forEach(link => link.classList.remove('active'));
                            this.classList.add('active');
                            
                            const headerHeight = document.querySelector('header').offsetHeight;
                            const targetPosition = target.offsetTop - headerHeight;
                            
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                            
                            // Resume scrollspy after scroll completes
                            clearTimeout(scrollTimeout);
                            scrollTimeout = setTimeout(() => {
                                isScrollingFromClick = false;
                            }, 800);
                        }
                    }
                });
            });
        });
    </script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>