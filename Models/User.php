<?php
declare(strict_types=1);
namespace Task1\Models;
use DateTimeImmutable;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;


final class User{
    private UuidInterface $id;
    public function __construct(private string $username,
                                private string $passwordHash,
                                private int $age,
                               private string $email,
                               private readonly DateTimeImmutable $createdAt,
                                private  ?DateTimeImmutable $updatedAt = null,
                                private  ?DateTimeImmutable $deletedAt = null,
                                ?UuidInterface $id = null
    )
    {
        $this->id = $id ?? Uuid::uuid7();
        if(trim($username) ===''){
            throw new InvalidArgumentException(
                'Username cannot be empty.');
        }
        if(trim($passwordHash) ===''){
            throw new InvalidArgumentException(
                'Password cannot be empty.');
        }
        if($age <=0){
            throw new InvalidArgumentException("Age cannot be less than or 0.");
        }
        if($email ===''){
            throw new InvalidArgumentException("Email cannot be empty.");
        }
    }
    public function getUsername():string{
        return $this->username;
    }
    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getEmail():string{
        return $this->email;
    }

    public function changeUsername(string $username):void{
        $username = trim($username);
        if($username ===''){
            throw new InvalidArgumentException("Username cannot be empty.");
        }
        $this -> updatedAt = new DateTimeImmutable();
        $this->username = trim($username);
    }
    public function changePassword(string $password):void{
        if($password ===''){
            throw new InvalidArgumentException("Password cannot be empty.");
        }
        if (strlen($password) < 12){
            throw new InvalidArgumentException("Password cannot be less than 12 characters.");
        }
        $this->updatedAt = new DateTimeImmutable();
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);    }
    public function changeAge(int $age):void{
        if($age <= 0){
            throw new InvalidArgumentException("Age cannot be less than or 0.");
        }
        $this->updatedAt = new DateTimeImmutable();
        $this->age = $age;
    }

    public function getCreatedAt():DateTimeImmutable{
        return $this->createdAt;
    }

    public function getAge():int{
        return $this->age;
    }
    public function softDelete(): void
    {
        if($this->deletedAt !== null){
            return;
        }
        $this->deletedAt = new DateTimeImmutable();
    }
    public function getHashPassword():string{
        return $this->passwordHash;
    }
}