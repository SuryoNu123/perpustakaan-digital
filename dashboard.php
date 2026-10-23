<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stats = getStats();
$recentBooks = getRecentBooks(5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perpustakaan Digital</title>
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
            transition: all 0.3s ease;
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
            justify-content: between;
            align-items: center;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--gray-900);
            margin: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--gradient-main);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .content-area {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 600;
            color: #10B981;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .card-body {
            padding: 2rem;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--gradient-main);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-purple);
            color: var(--primary-purple);
        }

        .btn-outline-primary:hover {
            background: var(--primary-purple);
            border-color: var(--primary-purple);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            border: none;
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-info {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            text-decoration: none;
            color: inherit;
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
            background: var(--gradient-main);
        }

        .action-title {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .action-description {
            font-size: 0.875rem;
            color: var(--gray-600);
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
        }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open me-2"></i>
                Perpustakaan Digital
            </a>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link active">
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
                <a href="tree_classification.php" class="nav-link">
                    <i class="fas fa-sitemap"></i>
                    Struktur Tree
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


    <div class="main-content">
        
        <div class="topbar">
            <h1 class="page-title">Dashboard</h1>
        </div>

        
        <div class="content-area">
          
            <div class="quick-actions">
                <a href="edit_genre.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="action-title">Kelola Genre</div>
                    <div class="action-description">Atur kategori dan subgenre perpustakaan</div>
                </a>
                <a href="tree_classification.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="action-title">Pencarian Genre</div>
                    <div class="action-description">Cari genre dengan algoritma tree</div>
                </a>
                <a href="tree_classification.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="action-title">Struktur Tree</div>
                    <div class="action-description">Visualisasi struktur genre dalam bentuk tree</div>
                </a>
            </div>

            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number"><?= $stats['total_books'] ?></div>
                            <div class="stat-label">Total Buku</div>
                            <div class="stat-change">+12% dari bulan lalu</div>
                        </div>
                        <div class="stat-icon books">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number"><?= $stats['total_genres'] ?></div>
                            <div class="stat-label">Genre Tersedia</div>
                            <div class="stat-change">+3 genre baru</div>
                        </div>
                        <div class="stat-icon genres">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number"><?= $stats['active_users'] ?></div>
                            <div class="stat-label">Pengguna Aktif</div>
                            <div class="stat-change">+8% minggu ini</div>
                        </div>
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-number"><?= $stats['books_borrowed'] ?></div>
                            <div class="stat-label">Buku Dipinjam</div>
                            <div class="stat-change">+15% hari ini</div>
                        </div>
                        <div class="stat-icon borrowed">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Books -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Buku Terbaru</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Penulis</th>
                                            <th>Genre</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBooks as $book): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($book['title']) ?></div>
                                                <small class="text-muted">ISBN: <?= htmlspecialchars($book['isbn']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($book['author']) ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= htmlspecialchars($book['genre']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">Tersedia</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktivitas Terbaru</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon genres me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Pencarian genre dilakukan</div>
                                    <small class="text-muted">2 jam yang lalu</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon books me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Tree structure diakses</div>
                                    <small class="text-muted">5 jam yang lalu</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon users me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Genre dikelola</div>
                                    <small class="text-muted">1 hari yang lalu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setInterval(function() {
            fetch('get_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.stat-number').forEach((element, index) => {
                        const stats = [data.total_books, data.total_genres, data.active_users, data.books_borrowed];
                        if (stats[index] !== undefined) {
                            element.textContent = stats[index];
                        }
                    });
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000);
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.stat-number').forEach(element => {
                const finalNumber = parseInt(element.textContent);
                animateNumber(element, 0, finalNumber, 1500);
            });
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
