<?php
declare(strict_types=1);
namespace Task1\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Throwable;
use RuntimeException;
use Dotenv\Dotenv;
use Task1\Redis\RedisConnection;

final class TokenService{

    public function __construct(private readonly RedisConnection $redisConnection )
    {
    }
    public function createToken($user){
        $issuedAt = time();
        $ttl = (int) ($_ENV['JWT_EXPIRES_IN'] ?? 3600);
        $expirationTime = $issuedAt + $ttl;
        $payload = [
            'iss' => $_ENV['JWT_ISSUER'] ?? 'kayan_users',
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'nbf' => $issuedAt,
            'aud' => $_ENV['JWT_AUDIENCE'] ?? 'kayan_users',
            'data' => [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        ];
        return JWT::encode(
            $payload,
            $_ENV['JWT_SECRET'],
            'HS256'
        );
    }
    public function storeTokenSession(string $userId, string $token, int $ttl): void
    {
        $redisClient = $this->redisConnection->getClient();

        $redisClient->setex("user:token:{$userId}", $ttl, $token);
    }
    public function validateSession(string $jwt):?object{

        $key = $_ENV['JWT_SECRET'];
        try{

        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        if ($decoded->iss !== $_ENV['JWT_ISSUER'] ||
            $decoded->aud !== $_ENV['JWT_AUDIENCE']){
            return null;
        }
        $userId = $decoded->data->user_id ?? null;
        if ($userId === null) {
            return null;
        }
        $storedToken = $this->redisConnection->getClient()->get("user:token:{$userId}");
        if($storedToken !== $jwt){
            return null;
        }
        }catch (Throwable $e){
            error_log("JWT Validation Failed: " . $e->getMessage());
            return null;
        }
        return $decoded->data;
    }
}