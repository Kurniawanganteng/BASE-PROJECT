<?php
// ==========================================================================
// Index - Halaman Utama (Read + Search)
// Project: Product Manager
// Author: Zahwa Aldi Sabhita
// ==========================================================================

require_once "functions.php";
require_once "db.php";

$csrfToken = generateCsrfToken();
$flash = getFlashMessage();

// --- Search / Filter ---
$q = trim($_GET["q"] ?? "");

if ($q !== "") {
    $sql = "SELECT * FROM products WHERE nama LIKE :q OR kategori LIKE :q ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["q" => "%$q%"]);
} else {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$products = $stmt->fetchAll();
$totalNilaiStok = hitungTotalNilaiStok($products);
$totalProduk = count($products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Manager — Tri Jaka Kurniawan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Header -->
    <header class="navbar">
        <div class="nav-container">
            <div class="brand-section">
                <div class="brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </div>
                <div class="brand-titles">
                    <h1>Product Manager</h1>
                    <p>Sistem Informasi Manajemen Inventaris</p>
                </div>
            </div>

            <!-- Author Identity -->
            <div class="user-badge">
                <div class="user-avatar">T</div>
                <div class="user-info">
                    <div class="user-name">Tri Jaka Kurniawan</div>
                    <div class="user-role">Project Owner</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">

        <!-- Flash Message Notification -->
        <?php if ($flash): ?>
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
                <?php if ($flash['type'] === 'success'): ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                <?php else: ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                <?php endif; ?>
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <!-- Key Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div>
                    <div class="metric-label">Total Produk Terdata</div>
                    <div class="metric-value"><?= number_format($totalProduk, 0, ',', '.') ?></div>
                </div>
                <div class="metric-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Total Nilai Aset Gudang</div>
                    <div class="metric-value"><?= formatRupiah($totalNilaiStok) ?></div>
                </div>
                <div class="metric-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Toolbar / Action Bar -->
        <div class="toolbar">
            <form action="index.php" method="GET" class="search-box">
                <div class="search-input-wrapper">
                    <span class="search-icon-inside">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text" name="q" class="search-input"
                           placeholder="Cari nama produk atau kategori..."
                           value="<?= sanitize($q) ?>">
                </div>
                <button type="submit" class="btn btn-dark">Cari</button>
                <?php if ($q !== ""): ?>
                    <a href="index.php" class="btn btn-subtle">Reset</a>
                <?php endif; ?>
            </form>

            <a href="create.php" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Produk
            </a>
        </div>

        <!-- Section Title -->
        <div class="section-header">
            <div>
                <h2 class="section-title">Daftar Produk</h2>
                <p class="section-subtitle">
                    <?php if ($q !== ""): ?>
                        Menampilkan hasil pencarian untuk "<strong><?= sanitize($q) ?></strong>" (<?= $totalProduk ?> ditemukan)
                    <?php else: ?>
                        Katalog inventaris aktif dalam sistem
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <?php if (empty($products)): ?>
            <div class="empty-container">
                <div class="empty-icon">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </div>
                <div class="empty-title">Tidak Ada Produk Ditemukan</div>
                <p class="empty-text">
                    <?php if ($q !== ""): ?>
                        Tidak ada produk yang cocok dengan kata kunci pencarian Anda.
                    <?php else: ?>
                        Belum ada data produk yang tersimpan dalam sistem.
                    <?php endif; ?>
                </p>
                <a href="<?= $q !== '' ? 'index.php' : 'create.php' ?>" class="btn btn-primary btn-sm">
                    <?= $q !== '' ? 'Lihat Semua Produk' : 'Tambah Produk Baru' ?>
                </a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $produk): ?>
                    <div class="product-card">
                        <div class="card-top">
                            <h3 class="product-name"><?= sanitize($produk["nama"]) ?></h3>
                            <span class="pill-badge badge-category">
                                <?= sanitize($produk["kategori"]) ?>
                            </span>
                        </div>

                        <div class="card-info">
                            <div class="product-price"><?= formatRupiah($produk["harga"]) ?></div>
                            <div class="stock-indicator">
                                <span class="stock-dot <?= isStokKritis($produk["stok"]) ? 'critical' : '' ?>"></span>
                                <span>Stok: <?= (int)$produk["stok"] ?> unit</span>
                                <?php if (isStokKritis($produk["stok"])): ?>
                                    <span class="pill-badge badge-critical">Stok Kritis</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-actions-wrapper">
                            <a href="edit.php?id=<?= (int)$produk["id"] ?>" class="btn btn-edit btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit
                            </a>

                            <form action="delete.php" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dipulihkan.');">
                                <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                                <input type="hidden" name="id" value="<?= (int)$produk["id"] ?>">
                                <button type="submit" class="btn btn-delete btn-sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div>
                Sistem Manajemen Inventaris &bull; Tugas Akhir Pemrograman Web
            </div>
            <div>
                Dikembangkan oleh <span class="footer-author">Tri Jaka Kurniawan</span>
            </div>
        </div>
    </footer>

</body>
</html>
