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

            $host = $_ENV['Redis_Host'] ?? '127.0.0.1';
            $port = (int)($_ENV['Redis_Port'] ?? 6379);
            $scheme = $_ENV['Redis_scheme'] ?? 'tcp';
            $this->client = new PredisClient([
                'scheme' => $scheme,
                'host' => $host,
                'port' => $port,]);

    }

    public function getClient(): PredisClient
    {
        return $this->client;
    }
}
