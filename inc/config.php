<?php
declare(strict_types=1);

// inc/config.php

// Sesuaikan nilai ini dengan environment-mu
const DB_HOST = '127.0.0.1';
const DB_NAME = 'tugas1_crud';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// BASE URL relatif ke built-in server
const BASE_URL = '/'; // jika menjalankan di root public

// Upload directory (pastikan writable)
const UPLOAD_DIR = __DIR__ . '/../public/uploads/';
const UPLOAD_MAX_SIZE = 2 * 1024 * 1024; // 2 MB
const UPLOAD_ALLOWED = ['image/jpeg', 'image/png'];

// PDO DSN helper
function getDsn(): string {
    return sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
}