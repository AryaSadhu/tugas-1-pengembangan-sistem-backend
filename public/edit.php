<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/Database.php';
require_once __DIR__ . '/../class/ProductRepository.php';

$db = Database::getConnection();
$repo = new ProductRepository($db);

$errors = [];
$id = (int)($_GET['id'] ?? 0);

$product = $repo->find($id);
if (!$product) {
    die('Produk tidak ditemukan.');
}

$old = $product;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $priceRaw = $_POST['price'] ?? '';

$priceClean = str_replace(['.', ','], '', $priceRaw);

if (!is_numeric($priceClean)) {
    $errors['price'] = 'Harga harus numerik.';
}

$price = number_format((float)$priceClean, 2, '.', '');

    $stock = $_POST['stock'] ?? '';
    $status = $_POST['status'] ?? 'inactive';

    $old = compact('name','category','price','stock','status');
    $old['image_path'] = $product['image_path'];

    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if ($category === '') $errors[] = 'Kategori wajib dipilih.';
    // if (!is_numeric($price)) $errors[] = 'Harga harus numerik.';
    if (!is_numeric($stock) || (int)$stock < 0) $errors[] = 'Stok harus bilangan bulat >= 0.';

    $imagePath = $product['image_path']; // default: pertahankan file lama

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

    if (empty($errors) && !empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
        $dest = UPLOAD_DIR . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $errors[] = 'Gagal memindahkan file upload.';
        } else {
            $imagePath = 'uploads/' . $filename;
            // opsional: hapus file lama
            if (!empty($product['image_path']) && file_exists(__DIR__ . '/../' . $product['image_path'])) {
    @unlink(__DIR__ . '/../' . $product['image_path']);
}

        }
    }

    if (empty($errors)) {
        $repo->update($id, [
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
  <title>Edit Produk</title>
</head>
<body>
<h1>Edit Produk</h1>
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
      <input type="number" name="stock" value="<?= ($old['stock'] ?? '') ?>" min="0" required>
    </label>
  </p>
  <p>
    <label>Gambar Produk:<br>
      <input type="file" name="image" accept="image/jpeg,image/png">
      <?php if (!empty($old['image_path'])): ?>
        <br><img src="<?= htmlspecialchars($old['image_path']) ?>" alt="" width="80">
      <?php endif; ?>
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
    <button type="submit">Simpan Perubahan</button>
  </p>
</form>
<script src="assets/js/script.js"></script>
</body>
</html>