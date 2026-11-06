<?php
declare(strict_types=1);

namespace Task1\DTO;
use RuntimeException;

final readonly class LoginDto{

    public function __construct(public string $email,public string $password){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new RuntimeException("Invalid email or password");
        }
        if(trim($password) === ""){
            throw new RuntimeException("Invalid email or password");
        }
    }
    public static function fromArray(array $payload):self{
        return new self($payload['email'] ?? "",
            $payload['password'] ?? ""
        );
    }
}