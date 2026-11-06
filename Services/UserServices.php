<?php
declare(strict_types=1);

namespace Task1\Services;
use Exception;
use RuntimeException;
use Task1\Repository\UserRepository;
use Task1\Dto\RegisterDto;
use DateTimeImmutable;
use Task1\Models\User;


final class UserServices{
    public function __construct(private readonly UserRepository $userRepository )
    {
    }
    public function register(RegisterDto $dto): string {
        RegisterDto::fromArray($dto);

        $hashedPassword = password_hash($dto->password, PASSWORD_DEFAULT);

        $user = new User(
          username: $dto->username,
          passwordHash: $hashedPassword,
          email: $dto->email,
          age: $dto->age,
          createdAt: new DateTimeImmutable()
        );
        $saved = $this->userRepository->saveUser($user);

        if (!$saved) {
            throw new RuntimeException("Failed to register the user.");
        }

        return $user->getId();
    }
    public function LogIn(){

    }
}
