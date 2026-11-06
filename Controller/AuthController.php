<?php
declare(strict_types=1);

namespace Task1\Controller;

use Task1\DTO\LoginDto;
use Exception;
use Task1\Services\AuthService;

final class AuthController{
    public function __construct(private readonly AuthService $authService){}
    public function login(LoginDto $dto):void{
         header('Content-Type: application/json');
         try{
             $token = $this->authService->login($dto);
             echo json_encode($token);
             http_response_code(200);
             echo json_encode([
                 'status' => 'success',
                 'token' => $token
             ]);
         }catch (Exception $e){
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
         }
    }
}