<?php
// ============================================
// Functions - Helper untuk Product Manager
// ============================================

session_start();

// ---- Fungsi Asli (dari base project) ----

// hitung total nilai stok semua produk (harga x stok)
function hitungTotalNilaiStok($products) {
    $total = 0;
    foreach ($products as $produk) {
        $total += $produk["harga"] * $produk["stok"];
    }
    return $total;
}

// cek apakah stok kritis (kurang dari 3)
function isStokKritis($stok) {
    if ($stok < 3) {
        return true;
    }
    return false;
}

// format angka jadi rupiah
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ",", ".");
}

// ---- Fungsi Baru ----

/**
 * Sanitize output untuk mencegah XSS
 */
function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token dan simpan di session
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validasi data produk
 * Return array of errors (kosong = valid)
 */
function validateProduct($data, $pdo, $excludeId = null) {
    $errors = [];

    // Validasi nama: minimal 3 karakter
    $nama = trim($data['nama'] ?? '');
    if (strlen($nama) < 3) {
        $errors[] = "Nama produk minimal 3 karakter.";
    } else {
        // Cek nama unik di database
        if ($excludeId !== null) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE nama = :nama AND id != :id");
            $stmt->execute(['nama' => $nama, 'id' => $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE nama = :nama");
            $stmt->execute(['nama' => $nama]);
        }
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Nama produk \"$nama\" sudah digunakan.";
        }
    }

    // Validasi kategori
    $kategori = trim($data['kategori'] ?? '');
    if (empty($kategori)) {
        $errors[] = "Kategori harus diisi.";
    }

    // Validasi harga: harus lebih dari 0
    $harga = $data['harga'] ?? '';
    if (!is_numeric($harga) || $harga <= 0) {
        $errors[] = "Harga harus lebih dari 0.";
    }

    // Validasi stok: harus >= 0
    $stok = $data['stok'] ?? '';
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok tidak boleh negatif (minimal 0).";
    }

    return $errors;
}

/**
 * Set flash message di session
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Ambil dan hapus flash message dari session
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
