<?php

namespace Task1\Redis;
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use Exception;
use Predis\Client as PredisClient;
use RuntimeException;

require 'vendor/autoload.php';

final class RedisConnection
{

    private PredisClient $client;

    public function __construct()
    {

        $rootPath = dirname(__DIR__, 2);
        $dotenv = Dotenv::createImmutable($rootPath);
        try {
            $dotenv->safeLoad();
            $host = $_ENV['Redis_Host'] ?? '127.0.0.1';
            $port = (int)($_ENV['Redis_Port'] ?? 6379);
            $scheme = $_ENV['Redis_scheme'] ?? 'tcp';
            $this->client = new PredisClient([
                'scheme' => $scheme,
                'host' => $host,
                'port' => $port,]);
        } catch (Exception $e) {
            error_log("Couldn't load Dot env: " . $e->getMessage());
            throw new RuntimeException("Couldn't load Dot env: " . $e->getMessage());
        }
    }

    public function getClient(): PredisClient
    {
        return $this->client;
    }
}
