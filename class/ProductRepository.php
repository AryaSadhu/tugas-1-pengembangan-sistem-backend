<?php
declare(strict_types=1);

class ProductRepository
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data === false ? null : $data;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO products
            (name, category, price, stock, image_path, status)
            VALUES (:name, :category, :price, :stock, :image_path, :status)");
        $stmt->execute([
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':image_path' => $data['image_path'],
            ':status' => $data['status']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET
            name = :name,
            category = :category,
            price = :price,
            stock = :stock,
            image_path = :image_path,
            status = :status
            WHERE id = :id");
        $params = [
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':image_path' => $data['image_path'],
            ':status' => $data['status'],
            ':id' => $id
        ];
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}