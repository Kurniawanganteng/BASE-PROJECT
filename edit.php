<?php
// ==========================================================================
// Edit - Update Produk
// Project: Product Manager
// Author: Zahwa Aldi Sabhita
// ==========================================================================

require_once "functions.php";
require_once "db.php";

$csrfToken = generateCsrfToken();
$errors = [];

// Ambil ID dari GET parameter
$id = $_GET['id'] ?? null;

if (!$id) {
    setFlashMessage('error', 'ID produk tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Ambil data produk berdasarkan ID (prepared statement)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    setFlashMessage('error', 'Produk tidak ditemukan dalam basis data.');
    header("Location: index.php");
    exit;
}

// Data awal untuk form (pre-filled)
$old = [
    'nama'     => $product['nama'],
    'kategori' => $product['kategori'],
    'harga'    => $product['harga'],
    'stok'     => $product['stok'],
];

// Proses form saat POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi CSRF
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Token keamanan CSRF tidak valid. Silakan muat ulang halaman.";
    }

    // Ambil data dari form
    $old = [
        'nama'     => trim($_POST['nama'] ?? ''),
        'kategori' => trim($_POST['kategori'] ?? ''),
        'harga'    => $_POST['harga'] ?? '',
        'stok'     => $_POST['stok'] ?? '',
    ];

    // Validasi data (exclude current ID untuk cek nama unik)
    if (empty($errors)) {
        $errors = validateProduct($old, $pdo, $id);
    }

    // Jika tidak ada error, update di database
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE products SET nama = :nama, kategori = :kategori, harga = :harga, stok = :stok WHERE id = :id"
        );
        $stmt->execute([
            'nama'     => $old['nama'],
            'kategori' => $old['kategori'],
            'harga'    => $old['harga'],
            'stok'     => $old['stok'],
            'id'       => $id,
        ]);

        // Reset CSRF token setelah berhasil
        unset($_SESSION['csrf_token']);

        // PRG Pattern: redirect untuk hindari duplicate submit
        setFlashMessage('success', 'Produk "' . $old['nama'] . '" berhasil diperbarui.');
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk #<?= (int)$id ?> — Tri Jaka Kurniawan</title>
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
        <div class="form-panel-wrapper">

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert-box">
                    <div class="alert-heading">Terdapat kesalahan pengisian formulir:</div>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= sanitize($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-panel">
                <div class="form-header">
                    <h2>Edit Data Produk</h2>
                    <p>Memperbarui rincian untuk produk ID #<?= (int)$id ?></p>
                </div>

                <form action="edit.php?id=<?= (int)$id ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">

                    <div class="form-field">
                        <label for="nama" class="form-label">Nama Produk *</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                               value="<?= sanitize($old['nama']) ?>"
                               placeholder="Nama produk" required>
                        <span class="field-hint">Minimal 3 karakter.</span>
                    </div>

                    <div class="form-field">
                        <label for="kategori" class="form-label">Kategori *</label>
                        <input type="text" id="kategori" name="kategori" class="form-control"
                               value="<?= sanitize($old['kategori']) ?>"
                               placeholder="Kategori produk" required>
                    </div>

                    <div class="form-field">
                        <label for="harga" class="form-label">Harga Satuan (Rp) *</label>
                        <input type="number" id="harga" name="harga" class="form-control"
                               value="<?= sanitize($old['harga']) ?>"
                               placeholder="Harga satuan" min="1" step="any" required>
                        <span class="field-hint">Harus bernilai lebih dari 0.</span>
                    </div>

                    <div class="form-field">
                        <label for="stok" class="form-label">Jumlah Stok *</label>
                        <input type="number" id="stok" name="stok" class="form-control"
                               value="<?= sanitize($old['stok']) ?>"
                               placeholder="Stok produk" min="0" required>
                        <span class="field-hint">Nilai di bawah 3 akan otomatis ditandai stok kritis.</span>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Perbarui Data
                        </button>
                        <a href="index.php" class="btn btn-subtle">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
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
