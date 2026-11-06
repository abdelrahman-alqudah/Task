<?php
declare(strict_types=1);

namespace Task1\Controller;

use Task1\DTO\LoginDto;
use Exception;
use Task1\DTO\RegisterDto;
use Task1\Services\AuthService;

final readonly class AuthController
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginDto $dto): void
    {
        try {
            $token = $this->authService->login($dto);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'token' => $token
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function register(RegisterDto $dto): void
    {
        try {
            $this->authService->register($dto);

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);

            echo json_encode([
                'status' => 'success',
                'user' => $dto
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}