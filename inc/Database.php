<?php
declare(strict_types=1);

// inc/Database.php

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(): \PDO
    {
    if (self::$pdo === null) {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // --- FIX DI SINI ---
        self::$pdo = new \PDO(
            'mysql:host=localhost;dbname=tugas1_crud;charset=utf8',
            DB_USER,
            DB_PASS,
            $options
        );
    }
    return self::$pdo;
}
}