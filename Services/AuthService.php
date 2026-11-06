<?php
declare(strict_types=1);

namespace Task1\Services;
use Exception;
use RuntimeException;
use Task1\Repository\UserRepository;
use Task1\DTO\RegisterDto;
use Task1\DTO\LoginDto;
use DateTimeImmutable;
use Task1\Models\User;


final class AuthService{
    public function __construct(private readonly UserRepository $userRepository,private readonly TokenService $tokenService )
    {
    }
    public function register(RegisterDto $dto): string {

        if(trim($dto->email) === ""){
            throw new RuntimeException("Email cannot be empty");
        }

        $user = $this->userRepository->findByEmail($dto->email);
        if ($user !== null) {
            throw new RuntimeException('Email already exists');
        }

        $hashedPassword = password_hash($dto->password, PASSWORD_DEFAULT);

        $user = new User(
          username: $dto->username,
          passwordHash: $hashedPassword,
          age: $dto->age,
          email: $dto->email,
          createdAt: new DateTimeImmutable()
        );
        $saved = $this->userRepository->saveUser($user);

        if (!$saved) {
            throw new RuntimeException("Failed to register the user.");
        }

        return $user->getId();
    }
    public function login(LoginDto $loginDto){
        if($loginDto->email === ""){
            throw new RuntimeException("Email address is required.");
        }
        if($loginDto->password === ""){
            throw new RuntimeException("Password is required.");
        }
        $user= $this->userRepository->findByEmail($loginDto->email);
        if($user == null){
            throw new RuntimeException("User not found.");
        }

        $userHashedPassword = $user->getHashPassword();
        $IsValidPassword = password_verify($loginDto->password, $userHashedPassword);
        if(!$IsValidPassword){
            throw new RuntimeException("Wrong password.");
        }
        return $this->tokenService->createToken($user);
    }
}
