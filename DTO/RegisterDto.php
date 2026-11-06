<?php
declare(strict_types=1);

namespace Task1\DTO;
use InvalidArgumentException;
use Task1\Repository\UserRepository;

final readonly class RegisterDto{
    public function __construct(public string $username,
                                public string $email,
                                public string $password,
                                public int $age)
    {
        if (trim($this->username) === '') {
            throw new InvalidArgumentException("Username cannot be empty in DTO.");
        }
        if (trim($this->email) === '') {
            throw new InvalidArgumentException("Email cannot be empty in DTO.");
        }
        if (strlen($this->password) < 12) {
            throw new InvalidArgumentException("Password must be at least 12 characters.");
        }
        if ($this->age <= 0) {
            throw new InvalidArgumentException("Invalid age provided.");
        }
    }
    // This function used: to clean the data before it enter to the Service layer.
    public static function fromArray(array $payload): self
    {
        return new self(
            username: $payload['username'] ?? '',
            email: $payload['email'] ?? '',
            password: $payload['password'] ?? '',
            age: (int) ($payload['age'] ?? 0)
        );
    }
}