<?php
declare(strict_types=1);


namespace Task1\Database;
use PDO;
use RuntimeException;
use Exception;
use PDOException;

final class DBConnection{
    private PDO $connection;
    public function __construct(){

        $host     = $_ENV['DB_HOST'] ?? 'localhost';
        $port     = $_ENV['DB_PORT'] ?? '5432';
        $username = $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $database = $_ENV['DB_DATABASE'] ?? '';

        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

        $options = [

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try{
            $this->connection = new PDO($dsn, $username, $password, $options);
        }
        catch(PDOException $e){
            error_log("Database Connection Error: " . $e->getMessage());
            throw new RuntimeException('Unable to connect to database');
        }
    }
        public function getConnection():PDO {
            return $this->connection;
        }
}