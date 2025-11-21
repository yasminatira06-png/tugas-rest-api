<?php

class Database
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Ambil ENV dari Vercel
        $host = getenv("DB_HOST");
        $port = getenv("DB_PORT") ?: 5432;
        $dbname = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $pass = getenv("DB_PASS");
        $sslmode = getenv("DB_SSLMODE") ?: "require";

        if (!$host) {
            http_response_code(500);
            echo json_encode(["error" => "ENV tidak terbaca di Database.php"]);
            exit;
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            return self::$pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Koneksi gagal: " . $e->getMessage()
            ]);
            exit;
        }
    }
}