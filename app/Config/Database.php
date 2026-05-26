<?php

class Database
{
    private static ?PDO $instance = null;

    private static string $host   = 'localhost';
    private static string $dbname = 'pdv_master';
    private static string $user   = 'root';
    private static string $pass   = '';         // padrão XAMPP
    private static string $charset = 'utf8mb4';

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$host,
                self::$dbname,
                self::$charset
            );

            self::$instance = new PDO($dsn, self::$user, self::$pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instance;
    }

    // Impede clonagem e instanciação direta
    private function __construct() {}
    private function __clone() {}
}
