<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/Database.php';
require_once __DIR__ . '/../class/ProductRepository.php';

$db = Database::getConnection();
$repo = new ProductRepository($db);

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil dan validasi
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $status = $_POST['status'] ?? 'inactive';

    $old = compact('name','category','price','stock','status');

    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if ($category === '') $errors[] = 'Kategori wajib dipilih.';
    // if (!is_numeric($price)) $errors[] = 'Harga harus numerik.';
    if (!is_numeric($stock) || (int)$stock < 0) $errors[] = 'Stok harus bilangan bulat >= 0.';

    // handle file upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload gambar gagal.';
        } else {
            if ($file['size'] > UPLOAD_MAX_SIZE) $errors[] = 'Ukuran gambar maksimal 2 MB.';
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, UPLOAD_ALLOWED, true)) $errors[] = 'Tipe file tidak diizinkan. Hanya JPG/PNG.';
        }
    }

    if (empty($errors)) {
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            $dest = UPLOAD_DIR . $filename;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $errors[] = 'Gagal memindahkan file upload.';
            } else {
                // simpan path relatif ke public
                $imagePath = 'uploads/' . $filename;
            }
        }
    }

    if (empty($errors)) {
        $repo->create([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => (int)$stock,
            'image_path' => $imagePath,
            'status' => $status
        ]);
        header('Location: index.php');
        exit;
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Tambah Produk</title>
</head>
<body>
  <h1>Tambah Produk</h1>
  <p><a href="index.php">Kembali ke daftar</a></p>

  <?php if (!empty($errors)): ?>
    <div style="color:red;">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data">
    <p>
      <label>Nama Produk:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
      </label>
    </p>
    <p>
      <label>Kategori:<br>
        <select name="category" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Elektronik" <?= isset($old['category']) && $old['category'] === 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
          <option value="Pakaian" <?= isset($old['category']) && $old['category'] === 'Pakaian' ? 'selected' : '' ?>>Pakaian</option>
          <option value="Makanan" <?= isset($old['category']) && $old['category'] === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
        </select>
      </label>
    </p>
    <p>
      <label>Harga:<br>
        <input type="text" name="price" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required>

      </label>
    </p>
    <p>
      <label>Stok:<br>
        <input type="number" name="stock" value="<?= htmlspecialchars($old['stock'] ?? '') ?>" min="0" required>
      </label>
    </p>
    <p>
      <label>Gambar Produk:<br>
        <input type="file" name="image" accept="image/jpeg,image/png">
      </label>
    </p>
    <p>
      <label>Status:<br>
        <select name="status">
          <option value="active" <?= isset($old['status']) && $old['status'] === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= isset($old['status']) && $old['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </label>
    </p>
    <p>
      <button type="submit">Simpan</button>
    </p>
  </form>
  <script src="assets/js/script.js"></script>
</body>
</html>
