<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/Database.php';
require_once __DIR__ . '/../class/ProductRepository.php';


$db = Database::getConnection();
$repo = new ProductRepository($db);
$products = $repo->all();
function formatPrice($value) {
    return number_format((float)$value, 0, ',', '.');
}

?>
<!doctype html>
<html>
<head>
  <link rel="stylesheet" href="assets/css/style.css">
  <meta charset="utf-8">
  <title>Produk - Daftar</title>
</head>
<body>
  <h1>Daftar Produk</h1>
  <p><a href="create.php">Tambah Produk</a></p>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Gambar</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= ($p['id']) ?></td>
          <td><?= ($p['name']) ?></td>
          <td><?= ($p['category']) ?></td>
          <td><?= htmlspecialchars(formatPrice($p['price'])) ?></td>



          <td><?= ($p['stock']) ?></td>
          <td>
            <?php if (!empty($p['image_path'])): ?>
              <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="" width="80">
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($p['status']) ?></td>
          <td>
            <a href="edit.php?id=<?= $p['id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <script src="assets/js/script.js"></script>

</body>
</html>