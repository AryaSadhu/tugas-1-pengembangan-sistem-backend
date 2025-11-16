<?php
declare(strict_types=1);

class Product
{
    public ?int $id;
    public string $name;
    public string $category;
    public float $price;
    public int $stock;
    public ?string $image_path;
    public string $status;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->price = isset($data['price']) ? (float)$data['price'] : 0.0;
        $this->stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        $this->image_path = $data['image_path'] ?? null;
        $this->status = $data['status'] ?? 'active';
    }
}