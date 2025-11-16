<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/Database.php';
require_once __DIR__ . '/../class/ProductRepository.php';

$db = Database::getConnection();
$repo = new ProductRepository($db);

$id = (int)($_GET['id'] ?? 0);
$product = $repo->find($id);

if (!$product) {
    die('Produk tidak ditemukan.');
}

// hapus file terkait (opsional)
if (!empty($product['image_path']) && file_exists(__DIR__ . '/' . $product['image_path'])) {
    @unlink(__DIR__ . '/' . $product['image_path']);
}

// hapus record dari DB
$repo->delete($id);

header('Location: index.php');
exit;
