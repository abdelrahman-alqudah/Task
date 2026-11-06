<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Task1\Controller\AuthController;
use Task1\DTO\LoginDto;
use Task1\Services\AuthService;
use Task1\Services\TokenService;
use Task1\Repository\UserRepository;
use Task1\Database\DbConnection;
use Task1\Redis\RedisConnection;
use Task1\DTO\RegisterDto;
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/api/login' && $method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?? [];

    try {
        $loginDto = LoginDto::fromArray($data);

        $dbConnection = new DbConnection();
        $userRepository = new UserRepository($dbConnection);

        $redisConnection = new RedisConnection();
        $tokenService = new TokenService($redisConnection);

        $authService = new AuthService($userRepository, $tokenService);
        $authController = new AuthController($authService);

        $authController->login($loginDto);

    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

else if ($path === '/api/register' && $method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?? [];

    try {
        $register = RegisterDto::fromArray($data);


        $dbConnection = new DbConnection();
        $userRepository = new UserRepository($dbConnection);
        $redisConnection = new RedisConnection();
        $tokenService = new TokenService($redisConnection);
        $authService = new AuthService($userRepository, $tokenService);
        $authController = new AuthController($authService);

        $authController->register($register);

    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed'
    ]);
}

