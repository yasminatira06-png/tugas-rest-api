<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Coba ambil dari berbagai sumber ENV
        $host = getenv('DB_HOST')
            ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? null));

        $port   = getenv('DB_PORT')
            ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? '5432'));

        $dbname = getenv('DB_NAME')
            ?: ($_ENV['DB_NAME'] ?? ($_SERVER['DB_NAME'] ?? 'neondb'));

        $user   = getenv('DB_USER')
            ?: ($_ENV['DB_USER'] ?? ($_SERVER['DB_USER'] ?? 'neondb_owner'));

        $pass   = getenv('DB_PASS')
            ?: ($_ENV['DB_PASS'] ?? ($_SERVER['DB_PASS'] ?? 'npg_6khoO4sweifm'));

        $sslmode = getenv('DB_SSLMODE')
            ?: ($_ENV['DB_SSLMODE'] ?? ($_SERVER['DB_SSLMODE'] ?? 'require'));

        // Fallback terakhir: kalau host masih kosong, pakai langsung Neon-mu
        if (!$host) {
            $host = 'ep-aged-band-a1g141gf.ap-southeast-1.aws.neon.tech';
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return self::$pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Koneksi gagal: ' . $e->getMessage(),
                // bisa di-comment kalau sudah beres:
                // 'debug' => [
                //     'host' => $host,
                //     'port' => $port,
                //     'dbname' => $dbname,
                //     'user' => $user,
                // ]
            ]);
            exit;
        }
    }
}