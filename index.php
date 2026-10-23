<?php
require_once 'includes/functions.php';

$stats = getStats();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #8B5CF6;
            --secondary-purple: #A855F7;
            --light-purple: #C4B5FD;
            --dark-purple: #6D28D9;
            --primary-teal: #06B6D4;
            --secondary-teal: #0891B2;
            --light-teal: #67E8F9;
            --dark-teal: #0E7490;
            --gradient-main: linear-gradient(135deg, #8B5CF6 0%, #06B6D4 100%);
            --gradient-secondary: linear-gradient(135deg, #A855F7 0%, #0891B2 100%);
            --gradient-light: linear-gradient(135deg, #C4B5FD 0%, #67E8F9 100%);
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--gray-50);
            line-height: 1.6;
        }

        .navbar {
            background: var(--white);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-main);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            color: var(--gray-700);
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover {
            color: var(--primary-purple);
            background: rgba(139, 92, 246, 0.1);
        }

        .btn-primary {
            background: var(--gradient-main);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .hero-section {
            background: var(--gradient-main);
            color: white;
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .stats-section {
            padding: 4rem 0;
            background: var(--white);
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .stat-icon.books {
            background: var(--gradient-main);
        }

        .stat-icon.genres {
            background: var(--gradient-secondary);
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }

        .stat-icon.borrowed {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 1rem;
        }

        .features-section {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .section-subtitle {
            text-align: center;
            color: var(--gray-600);
            font-size: 1.125rem;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
            background: var(--gradient-main);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .feature-description {
            color: var(--gray-600);
            line-height: 1.6;
        }

        .cta-section {
            padding: 6rem 0;
            background: var(--gradient-main);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .btn-outline-light {
            border: 2px solid white;
            color: white;
            background: transparent;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary-purple);
            transform: translateY(-2px);
        }

        .footer {
            background: var(--gray-900);
            color: white;
            padding: 3rem 0 2rem;
        }

        .footer-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-link {
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--light-teal);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book-open me-2"></i>
                Perpustakaan Digital
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistik">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Perpustakaan Digital Cerdas</h1>
                        <p class="hero-subtitle">
                            Sistem manajemen perpustakaan modern berbasis struktur data dengan 
                            algoritma pencarian canggih dan antarmuka yang intuitif.
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="login.php" class="btn btn-light btn-lg">
                                <i class="fas fa-rocket me-2"></i>
                                Mulai Sekarang
                            </a>
                            <a href="#fitur" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-info-circle me-2"></i>
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-book-reader" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="stats-section" id="statistik">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon books">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-number"><?= $stats['total_books'] ?></div>
                        <div class="stat-label">Total Buku</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon genres">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-number"><?= $stats['total_genres'] ?></div>
                        <div class="stat-label">Genre Tersedia</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?= $stats['active_users'] ?></div>
                        <div class="stat-label">Pengguna Aktif</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon borrowed">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <div class="stat-number"><?= $stats['books_borrowed'] ?></div>
                        <div class="stat-label">Buku Dipinjam</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Fitur Unggulan</h2>
                <p class="section-subtitle">
                    Dilengkapi dengan teknologi terdepan untuk pengalaman pengelolaan perpustakaan yang optimal
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="feature-title">Pencarian Cerdas</h3>
                        <p class="feature-description">
                            Algoritma pencarian DFS dan BFS untuk menemukan buku dengan cepat dan akurat
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h3 class="feature-title">Struktur Data Tree</h3>
                        <p class="feature-description">
                            Organisasi buku berdasarkan genre dengan struktur pohon untuk navigasi yang intuitif
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Analitik Real-time</h3>
                        <p class="feature-description">
                            Dashboard dengan statistik lengkap dan visualisasi data perpustakaan
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Responsive Design</h3>
                        <p class="feature-description">
                            Antarmuka yang responsif dan dapat diakses dari berbagai perangkat
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Keamanan Terjamin</h3>
                        <p class="feature-description">
                            Sistem autentikasi dan otorisasi yang aman untuk melindungi data perpustakaan
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="feature-title">Mudah Dikelola</h3>
                        <p class="feature-description">
                            Interface admin yang user-friendly untuk pengelolaan buku dan genre
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Siap Memulai?</h2>
            <p class="cta-subtitle">
                Bergabunglah dengan revolusi digital perpustakaan modern
            </p>
            <a href="login.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-arrow-right me-2"></i>
                Akses Dashboard Admin
            </a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-title">Perpustakaan Digital</h5>
                    <p class="text-muted">
                        Sistem manajemen perpustakaan modern dengan teknologi struktur data terdepan.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="#beranda" class="footer-link">Beranda</a></li>
                        <li><a href="#fitur" class="footer-link">Fitur</a></li>
                        <li><a href="#statistik" class="footer-link">Statistik</a></li>
                        <li><a href="login.php" class="footer-link">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Fitur</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Pencarian Cerdas</a></li>
                        <li><a href="#" class="footer-link">Struktur Tree</a></li>
                        <li><a href="#" class="footer-link">Analitik</a></li>
                        <li><a href="#" class="footer-link">Dashboard</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="">
                            <i class="fas fa-envelope me-2"></i>
                            admin@perpustakaan.com
                        </li>
                        <li class="text-muted">
                            <i class="fas fa-phone me-2"></i>
                            +62 123 456 789
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: var(--gray-700);">
            <div class="text-center">
                <p class="text-muted mb-0">
                    &copy; 2024 Perpustakaan Digital Cerdas. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = 'var(--white)';
                navbar.style.backdropFilter = 'none';
            }
        });

        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    const finalNumber = parseInt(statNumber.textContent);
                    animateNumber(statNumber, 0, finalNumber, 2000);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-card').forEach(card => {
            observer.observe(card);
        });

        function animateNumber(element, start, end, duration) {
            const startTime = performance.now();
            const range = end - start;

            function updateNumber(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(start + (range * progress));
                element.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(updateNumber);
                }
            }

            requestAnimationFrame(updateNumber);
        }
    </script>
</body>
</html>
