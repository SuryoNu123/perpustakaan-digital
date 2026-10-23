<?php
require_once 'includes/functions.php';
require_once 'includes/tree_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$algoritmaTraversal = 'best_first';
$hasilPencarian = [];
$waktuEksekusi = 0;
$statistikPencarian = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $algoritmaTraversal = sanitize($_POST['algorithm'] ?? 'best_first');
    
    $waktuMulai = microtime(true);
    
   
    $kataPencarianDefault = 'a'; 
    
    if ($algoritmaTraversal === 'best_first') {
        $hasilPencarian = searchGenresBestFirst($kataPencarianDefault);
    } else {
        $hasilPencarian = searchGenresDFS($kataPencarianDefault);
    }
    
    $waktuSelesai = microtime(true);
    $waktuEksekusi = ($waktuSelesai - $waktuMulai) * 1000;
    
    $statistikPencarian = [
        'total_ditemukan' => $hasilPencarian['total_ditemukan'],
        'nodes_dikunjungi' => count($hasilPencarian['nodes_dikunjungi']),
        'waktu_eksekusi' => $waktuEksekusi,
        'algoritma' => $algoritmaTraversal
    ];
}


$statistikTree = getTreeStats();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Genre Tree - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        .sidebar {
            background: var(--white);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            border-right: 1px solid var(--gray-200);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .sidebar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            background: var(--gradient-main);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            color: var(--gray-700);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--gradient-light);
            color: var(--dark-purple);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: var(--gradient-main);
            color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        .topbar {
            background: var(--white);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--gray-900);
            margin: 0;
        }

        .content-area {
            padding: 2rem;
        }

        .algorithm-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }

        .algorithm-header {
            background: var(--gradient-light);
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .algorithm-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-purple);
            margin: 0;
        }

        .algorithm-subtitle {
            color: var(--gray-600);
            margin-top: 0.5rem;
        }

        .algorithm-body {
            padding: 2rem;
        }

        .algorithm-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .algorithm-btn {
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .algorithm-btn:hover {
            border-color: var(--primary-purple);
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.2);
            text-decoration: none;
            color: inherit;
        }

        .algorithm-btn.active {
            border-color: var(--primary-purple);
            background: var(--gradient-light);
        }

        .algorithm-icon {
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

        .algorithm-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--gray-900);
        }

        .algorithm-description {
            color: var(--gray-600);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-purple);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .results-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }

        .results-header {
            background: var(--gradient-secondary);
            padding: 1.5rem 2rem;
            border-radius: 20px 20px 0 0;
            color: white;
        }

        .results-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }

        .results-body {
            padding: 2rem;
        }

        .search-results {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .result-item {
            background: var(--white);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid var(--primary-purple);
            transition: all 0.3s ease;
            position: relative;
        }

        .result-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .result-item.level-1 {
            border-left-color: var(--primary-purple);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .result-item.level-2 {
            margin-left: 2rem;
            border-left-color: var(--primary-teal);
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-title {
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
        }

        .result-title i {
            margin-right: 0.5rem;
        }

        .result-score {
            background: var(--gradient-main);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .result-meta {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .visit-order {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gradient-secondary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .search-stats {
            background: var(--gradient-light);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .search-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .search-stat-item {
            text-align: center;
        }

        .search-stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark-purple);
        }

        .search-stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                padding: 1rem;
            }

            .content-area {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .algorithm-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open me-2"></i>
                Perpustakaan Digital
            </a>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="edit_genre.php" class="nav-link">
                    <i class="fas fa-tags"></i>
                    Kelola Genre
                </a>
            </div>
            <div class="nav-item">
                <a href="tree_classification.php" class="nav-link active">
                    <i class="fas fa-sitemap"></i>
                    Pencarian Genre Tree
                </a>
            </div>
            <div class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h1 class="page-title">Algoritma Traversal Tree</h1>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $statistikTree['total_genre'] ?></div>
                    <div class="stat-label">Total Genre</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $statistikTree['total_subgenre'] ?></div>
                    <div class="stat-label">Total Subgenre</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $statistikTree['total_buku'] ?></div>
                    <div class="stat-label">Total Buku Terdaftar</div>
                </div>
                <?php if (!empty($statistikPencarian)): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($waktuEksekusi, 2) ?>ms</div>
                        <div class="stat-label">Waktu Eksekusi</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $statistikPencarian['nodes_dikunjungi'] ?></div>
                        <div class="stat-label">Node Dikunjungi</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Algorithm Selection Card -->
            <div class="algorithm-card">
                <div class="algorithm-header">
                    <h2 class="algorithm-title">
                        <i class="fas fa-sitemap me-2"></i>
                        Pilih Algoritma Traversal Tree
                    </h2>
                    <p class="algorithm-subtitle">Klik salah satu algoritma untuk melihat hasil traversal tree secara langsung</p>
                </div>

                <div class="algorithm-body">
                    <div class="algorithm-buttons">
                        <form method="POST" action="" style="display: contents;">
                            <input type="hidden" name="algorithm" value="best_first">
                            <button type="submit" class="algorithm-btn <?= $algoritmaTraversal === 'best_first' ? 'active' : '' ?>">
                                <div class="algorithm-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="algorithm-name">Best-First Search</div>
                                <div class="algorithm-description">
                                    Pencarian berdasarkan prioritas skor relevasi tertinggi. 
                                    Menggunakan priority queue untuk mengunjungi node dengan skor terbaik terlebih dahulu.
                                </div>
                            </button>
                        </form>

                        <form method="POST" action="" style="display: contents;">
                            <input type="hidden" name="algorithm" value="dfs">
                            <button type="submit" class="algorithm-btn <?= $algoritmaTraversal === 'dfs' ? 'active' : '' ?>">
                                <div class="algorithm-icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="algorithm-name">Depth-First Search (DFS)</div>
                                <div class="algorithm-description">
                                    Pencarian mendalam dari root, mengunjungi setiap cabang sedalam mungkin. 
                                    Diurutkan berdasarkan skor dari terkecil ke terbesar.
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (!empty($hasilPencarian['hasil'])): ?>
                <!-- Search Results -->
                <div class="results-card">
                    <div class="results-header">
                        <h3 class="results-title">
                            <i class="fas fa-list-ol me-2"></i>
                            Hasil Traversal Algoritma <?= strtoupper($algoritmaTraversal === 'best_first' ? 'Best-First Search' : 'Depth-First Search') ?>
                        </h3>
                    </div>
                    <div class="results-body">
                        <!-- Search Statistics -->
                        <div class="search-stats">
                            <div class="search-stats-grid">
                                <div class="search-stat-item">
                                    <div class="search-stat-number"><?= $statistikPencarian['total_ditemukan'] ?></div>
                                    <div class="search-stat-label">Node Ditemukan</div>
                                </div>
                                <div class="search-stat-item">
                                    <div class="search-stat-number"><?= $statistikPencarian['nodes_dikunjungi'] ?></div>
                                    <div class="search-stat-label">Node Dikunjungi</div>
                                </div>
                                <div class="search-stat-item">
                                    <div class="search-stat-number"><?= number_format($statistikPencarian['waktu_eksekusi'], 2) ?>ms</div>
                                    <div class="search-stat-label">Waktu Eksekusi</div>
                                </div>
                                <div class="search-stat-item">
                                    <div class="search-stat-number"><?= strtoupper($algoritmaTraversal === 'best_first' ? 'BFS' : 'DFS') ?></div>
                                    <div class="search-stat-label">Algoritma</div>
                                </div>
                            </div>
                        </div>

                        <div class="search-results">
                            <?php foreach ($hasilPencarian['hasil'] as $index => $result): ?>
                                <div class="result-item level-<?= $result['level'] ?>">
                                    <div class="visit-order"><?= $result['urutan_kunjungan'] ?></div>
                                    <div class="result-header">
                                        <div class="result-title">
                                            <?php if ($result['level'] == 1): ?>
                                                <i class="fas fa-folder"></i>
                                            <?php else: ?>
                                                <i class="fas fa-folder-open"></i>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($result['nama']) ?>
                                        </div>
                                        <div class="result-score">
                                            Skor: <?= $result['skor_relevasi'] ?>
                                        </div>
                                    </div>
                                    <div class="result-meta">
                                        <strong>Level:</strong> <?= $result['level'] == 1 ? 'Genre' : 'Subgenre' ?> | 
                                        <strong>Jumlah Buku:</strong> <span class="text-primary fw-bold"><?= $result['jumlah_buku'] ?></span> | 
                                        <strong>Parent:</strong> <?= $result['parent'] ? htmlspecialchars($result['parent']) : 'Root' ?> |
                                        <strong>Urutan Kunjungan:</strong> #<?= $result['urutan_kunjungan'] ?> |
                                        <strong>Skor Relevasi:</strong> <span class="text-success fw-bold"><?= $result['skor_relevasi'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Static Tree Structure -->
            <div class="results-card">
                <div class="results-header">
                    <h3 class="results-title">
                        <i class="fas fa-sitemap me-2"></i>
                        Struktur Tree Genre (Lengkap)
                    </h3>
                </div>
                <div class="results-body">
                    <div class="search-results">
                        <?php
                        $strukturStatis = buildGenreTree();
                        foreach ($strukturStatis as $namaGenre => $dataGenre):
                        ?>
                            <div class="result-item level-1">
                                <div class="result-header">
                                    <div class="result-title">
                                        <i class="fas fa-folder"></i>
                                        <?= htmlspecialchars($namaGenre) ?>
                                    </div>
                                    <div class="result-score">
                                        <?= $dataGenre['book_count'] ?> buku
                                    </div>
                                </div>
                                <div class="result-meta">
                                    <strong>Tipe:</strong> Genre | 
                                    <strong>Subgenre:</strong> <?= count($dataGenre['subgenres']) ?> kategori
                                </div>
                            </div>

                            <?php foreach ($dataGenre['subgenres'] as $namaSubgenre => $dataSubgenre): ?>
                                <div class="result-item level-2">
                                    <div class="result-header">
                                        <div class="result-title">
                                            <i class="fas fa-folder-open"></i>
                                            <?= htmlspecialchars($namaSubgenre) ?>
                                        </div>
                                        <div class="result-score">
                                            <?= $dataSubgenre['book_count'] ?> buku
                                        </div>
                                    </div>
                                    <div class="result-meta">
                                        <strong>Tipe:</strong> Subgenre | 
                                        <strong>Parent:</strong> <?= htmlspecialchars($namaGenre) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate result items on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.result-item, .stat-card').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'all 0.5s ease';
            observer.observe(item);
        });

        // Add sequential animation for search results
        if (document.querySelector('.search-results')) {
            const resultItems = document.querySelectorAll('.search-results .result-item');
            resultItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }

        // Add loading state to algorithm buttons
        document.querySelectorAll('.algorithm-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Add loading state
                const originalContent = this.innerHTML;
                this.innerHTML = `
                    <div class="algorithm-icon">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="algorithm-name">Memproses...</div>
                    <div class="algorithm-description">Sedang menjalankan algoritma traversal...</div>
                `;
                this.style.pointerEvents = 'none';
                
                // Submit form after short delay
                setTimeout(() => {
                    this.closest('form').submit();
                }, 500);
            });
        });
    </script>
</body>

</html>
