<?php
// ============================================
// Delete - Hapus Produk
// ============================================

require_once "functions.php";
require_once "db.php";

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Method tidak diizinkan.');
    header("Location: index.php");
    exit;
}

// Validasi CSRF token
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Token CSRF tidak valid. Silakan coba lagi.');
    header("Location: index.php");
    exit;
}

// Ambil ID produk
$id = $_POST['id'] ?? null;

if (!$id) {
    setFlashMessage('error', 'ID produk tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Cek apakah produk ada
$stmt = $pdo->prepare("SELECT nama FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    setFlashMessage('error', 'Produk tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Hapus produk
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);

// Reset CSRF token
unset($_SESSION['csrf_token']);

// Redirect dengan flash message
setFlashMessage('success', 'Produk "' . $product['nama'] . '" berhasil dihapus!');
header("Location: index.php");
exit;
