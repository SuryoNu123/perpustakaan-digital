<?php
require_once 'includes/functions.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_genre':
            $genre = sanitize($_POST['genre'] ?? '');
            if ($genre && addGenre($genre)) {
                $message = 'Genre berhasil ditambahkan!';
                $messageType = 'success';
            } else {
                $message = 'Gagal menambahkan genre atau genre sudah ada.';
                $messageType = 'danger';
            }
            break;
        case 'add_subgenre':
            $genre = sanitize($_POST['parent_genre'] ?? '');
            $subgenre = sanitize($_POST['subgenre'] ?? '');
            if ($genre && $subgenre && addSubgenre($genre, $subgenre)) {
                $message = 'Subgenre berhasil ditambahkan!';
                $messageType = 'success';
            } else {
                $message = 'Gagal menambahkan subgenre atau subgenre sudah ada.';
                $messageType = 'danger';
            }
            break;
            
        case 'delete_genre':
            $genre = sanitize($_POST['delete_genre'] ?? '');
            if ($genre && deleteGenre($genre)) {
                $message = 'Genre berhasil dihapus!';
                $messageType = 'success';
            } else {
                $message = 'Gagal menghapus genre.';
                $messageType = 'danger';
            }
            break;
            
        case 'delete_subgenre':
            $genre = sanitize($_POST['parent_genre_delete'] ?? '');
            $subgenre = sanitize($_POST['delete_subgenre'] ?? '');
            if ($genre && $subgenre && deleteSubgenre($genre, $subgenre)) {
                $message = 'Subgenre berhasil dihapus!';
                $messageType = 'success';
            } else {
                $message = 'Gagal menghapus subgenre.';
                $messageType = 'danger';
            }
            break;
        case 'update_book_count':
            $targetGenre = sanitize($_POST['target_genre'] ?? '');
            $targetSubgenre = sanitize($_POST['target_subgenre'] ?? '');
            $bookCount = (int)($_POST['book_count'] ?? 0);
            
            if ($targetGenre && updateBookCount($targetGenre, $bookCount, $targetSubgenre)) {
                $message = 'Jumlah buku berhasil diperbarui!';
                $messageType = 'success';
            } else {
                $message = 'Gagal memperbarui jumlah buku.';
                $messageType = 'danger';
            }
            break;
    }
}

$genres = getGenres();
$genreStats = getGenreStats();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Genre - Perpustakaan Digital</title>
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

        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }

        .card-header {
            background: var(--gradient-light);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
            border-radius: 20px 20px 0 0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-purple);
            margin: 0;
        }

        .card-body {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            background: var(--gradient-main);
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
            background: var(--white);
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

        .btn-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
        }

        .genre-tree {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .genre-item {
            background: var(--white);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .genre-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .genre-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .genre-name {
            font-weight: 700;
            color: var(--primary-purple);
            font-size: 1.1rem;
        }

        .subgenre-list {
            margin-left: 1.5rem;
            margin-top: 0.75rem;
        }

        .subgenre-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .subgenre-item:last-child {
            border-bottom: none;
        }

        .subgenre-name {
            color: var(--gray-700);
            font-weight: 500;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-primary {
            background: var(--gradient-light);
            color: var(--dark-purple);
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
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="edit_genre.php" class="nav-link active">
                    <i class="fas fa-tags"></i>
                    Kelola Genre
                </a>
            </div>
            <div class="nav-item">
                <a href="tree_classification.php" class="nav-link">
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
    <div class="main-content">
        <div class="topbar">
            <h1 class="page-title">Kelola Genre</h1>
        </div>
        <div class="content-area">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-number"><?= count($genres) ?></div>
                    <div class="stat-label">Total Genre</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="stat-number"><?= array_sum(array_map('count', $genres)) ?></div>
                    <div class="stat-label">Total Subgenre</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-number"><?= array_sum($genreStats) ?></div>
                    <div class="stat-label">Total Buku</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus-circle me-2"></i>
                                Tambah Genre Baru
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="add_genre">
                                <div class="mb-3">
                                    <label for="genre" class="form-label">Nama Genre</label>
                                    <input type="text" class="form-control" id="genre" name="genre" required 
                                           placeholder="Masukkan nama genre">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Tambah Genre
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tag me-2"></i>
                                Tambah Subgenre
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="add_subgenre">
                                <div class="mb-3">
                                    <label for="parent_genre" class="form-label">Genre Induk</label>
                                    <select class="form-select" id="parent_genre" name="parent_genre" required>
                                        <option value="">Pilih Genre</option>
                                        <?php foreach ($genres as $genre => $subgenres): ?>
                                            <option value="<?= htmlspecialchars($genre) ?>"><?= htmlspecialchars($genre) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="subgenre" class="form-label">Nama Subgenre</label>
                                    <input type="text" class="form-control" id="subgenre" name="subgenre" required 
                                           placeholder="Masukkan nama subgenre">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Tambah Subgenre
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-medical me-2"></i>
                        Kelola Jumlah Buku per Genre/Subgenre
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="bookCountForm">
                        <input type="hidden" name="action" value="update_book_count">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="target_genre" class="form-label">Pilih Genre</label>
                                    <select class="form-select" id="target_genre" name="target_genre" required onchange="updateTargetSubgenres()">
                                        <option value="">Pilih Genre</option>
                                        <?php foreach ($genres as $genre => $subgenres): ?>
                                            <option value="<?= htmlspecialchars($genre) ?>"><?= htmlspecialchars($genre) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="target_subgenre" class="form-label">Pilih Subgenre (Opsional)</label>
                                    <select class="form-select" id="target_subgenre" name="target_subgenre" onchange="updateCurrentBookCount()">
                                        <option value="">Pilih Subgenre</option>
                                    </select>
                                    <small class="text-muted">Kosongkan untuk mengatur genre utama</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="book_count" class="form-label">Jumlah Buku</label>
                                    <input type="number" class="form-control" id="book_count" name="book_count" 
                                           min="0" max="9999" required placeholder="Masukkan jumlah buku">
                                    <small class="text-muted" id="current_count_info"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Jumlah Buku
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetBookCountForm()">
                                        <i class="fas fa-undo me-2"></i>Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sitemap me-2"></i>
                        Struktur Genre & Jumlah Buku
                    </h3>
                </div>
                <div class="card-body">
                    <div class="genre-tree">
                        <?php 
                        $bookCounts = getAllBookCounts();
                        foreach ($genres as $genre => $subgenres): 
                            $genreBookCount = isset($bookCounts[$genre]) ? $bookCounts[$genre]['genre_count'] : 0;
                        ?>
                            <div class="genre-item">
                                <div class="genre-header">
                                    <div class="d-flex align-items-center">
                                        <span class="genre-name">
                                            <i class="fas fa-folder me-2"></i>
                                            <?= htmlspecialchars($genre) ?>
                                        </span>
                                        <span class="badge badge-primary ms-2">
                                            <?= count($subgenres) ?> subgenre
                                        </span>
                                        <span class="badge" style="background: var(--gradient-main); color: white;" title="Jumlah buku dalam genre">
                                            <i class="fas fa-book me-1"></i><?= $genreBookCount ?> buku
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                onclick="editBookCount('<?= htmlspecialchars($genre) ?>', '', <?= $genreBookCount ?>)"
                                                title="Edit jumlah buku genre">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="" style="display: inline;" 
                                              onsubmit="return confirm('Yakin ingin menghapus genre ini? Semua subgenre akan ikut terhapus.')">
                                            <input type="hidden" name="action" value="delete_genre">
                                            <input type="hidden" name="delete_genre" value="<?= htmlspecialchars($genre) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <?php if (!empty($subgenres)): ?>
                                    <div class="subgenre-list">
                                        <?php foreach ($subgenres as $subgenre): 
                                            $subgenreBookCount = isset($bookCounts[$genre]['subgenres'][$subgenre]) ? $bookCounts[$genre]['subgenres'][$subgenre] : 0;
                                        ?>
                                            <div class="subgenre-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="subgenre-name">
                                                        <i class="fas fa-file me-2"></i>
                                                        <?= htmlspecialchars($subgenre) ?>
                                                        <span class="badge ms-2" style="background: var(--gradient-secondary); color: white;" title="Jumlah buku dalam subgenre">
                                                            <i class="fas fa-book me-1"></i><?= $subgenreBookCount ?> buku
                                                        </span>
                                                    </span>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                onclick="editBookCount('<?= htmlspecialchars($genre) ?>', '<?= htmlspecialchars($subgenre) ?>', <?= $subgenreBookCount ?>)"
                                                                title="Edit jumlah buku subgenre">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" action="" style="display: inline;" 
                                                              onsubmit="return confirm('Yakin ingin menghapus subgenre ini?')">
                                                            <input type="hidden" name="action" value="delete_subgenre">
                                                            <input type="hidden" name="parent_genre_delete" value="<?= htmlspecialchars($genre) ?>">
                                                            <input type="hidden" name="delete_subgenre" value="<?= htmlspecialchars($subgenre) ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Harap lengkapi semua field yang wajib diisi!');
                }
            });
        });

        document.querySelectorAll('.form-control, .form-select').forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.stat-number').forEach(element => {
                const finalNumber = parseInt(element.textContent);
                animateNumber(element, 0, finalNumber, 1000);
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

        // Update target subgenres for book count management
        function updateTargetSubgenres() {
            const genreSelect = document.getElementById('target_genre');
            const subgenreSelect = document.getElementById('target_subgenre');
            const selectedGenre = genreSelect.value;

            // Clear subgenre options
            subgenreSelect.innerHTML = '<option value="">Pilih Subgenre</option>';
            
            // Clear book count input and info
            document.getElementById('book_count').value = '';
            document.getElementById('current_count_info').textContent = '';

            if (selectedGenre && genresData[selectedGenre]) {
                genresData[selectedGenre].forEach(subgenre => {
                    const option = document.createElement('option');
                    option.value = subgenre;
                    option.textContent = subgenre;
                    subgenreSelect.appendChild(option);
                });
                
                // Show current genre book count
                updateCurrentBookCount();
            }
        }

        // Update current book count info
        function updateCurrentBookCount() {
            const genreSelect = document.getElementById('target_genre');
            const subgenreSelect = document.getElementById('target_subgenre');
            const bookCountInput = document.getElementById('book_count');
            const currentCountInfo = document.getElementById('current_count_info');
            
            const selectedGenre = genreSelect.value;
            const selectedSubgenre = subgenreSelect.value;
            
            if (selectedGenre) {
                // Get current count via AJAX or from bookCountsData
                if (bookCountsData[selectedGenre]) {
                    let currentCount = 0;
                    if (selectedSubgenre && bookCountsData[selectedGenre].subgenres[selectedSubgenre] !== undefined) {
                        currentCount = bookCountsData[selectedGenre].subgenres[selectedSubgenre];
                        currentCountInfo.textContent = `Jumlah saat ini: ${currentCount} buku`;
                    } else if (!selectedSubgenre) {
                        currentCount = bookCountsData[selectedGenre].genre_count || 0;
                        currentCountInfo.textContent = `Jumlah saat ini: ${currentCount} buku`;
                    }
                    bookCountInput.value = currentCount;
                }
            }
        }

        // Edit book count function
        function editBookCount(genre, subgenre, currentCount) {
            document.getElementById('target_genre').value = genre;
            updateTargetSubgenres();
            
            if (subgenre) {
                setTimeout(() => {
                    document.getElementById('target_subgenre').value = subgenre;
                    updateCurrentBookCount();
                }, 100);
            } else {
                updateCurrentBookCount();
            }
            
            // Scroll to form
            document.getElementById('bookCountForm').scrollIntoView({ behavior: 'smooth' });
            
            // Focus on book count input
            setTimeout(() => {
                document.getElementById('book_count').focus();
                document.getElementById('book_count').select();
            }, 200);
        }

        // Reset book count form
        function resetBookCountForm() {
            document.getElementById('bookCountForm').reset();
            document.getElementById('current_count_info').textContent = '';
            updateTargetSubgenres();
        }

        // Add book counts data for JavaScript
        const bookCountsData = <?= json_encode(getAllBookCounts()) ?>;

        // Enhanced form validation for book count
        document.getElementById('bookCountForm').addEventListener('submit', function(e) {
            const bookCount = parseInt(document.getElementById('book_count').value);
            
            if (isNaN(bookCount) || bookCount < 0) {
                e.preventDefault();
                alert('Jumlah buku harus berupa angka positif!');
                return false;
            }
            
            if (bookCount > 9999) {
                e.preventDefault();
                alert('Jumlah buku tidak boleh lebih dari 9999!');
                return false;
            }
        });
            const genresData = <?= json_encode($genres) ?>;
    </script>
</body>
</html>
