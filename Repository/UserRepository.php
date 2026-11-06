<?php
declare(strict_types=1);

namespace Task1\Repository;
use Task1\Database\DbConnection;
use DateTimeImmutable;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Task1\Models\User;
final class UserRepository
{
    private PDO $pdo;

    public function __construct(DbConnection $dbConnection)
    {
        $this->pdo = $dbConnection->getConnection();
    }

    public function saveUser(User $user)
    {

        $sql = "INSERT INTO users (id, username, email, password, age, created_at)
                VALUES (:id, :username, :email, :password, :age, :created_at)";

        try {
            $existEmail = $this->findByEmail($user->getEmail());
            if($existEmail !== null){
                throw new RuntimeException("Email is already exist");
            }

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':id' => $user->getId(),
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':password' => $user->getHashPassword(),
                ':age' => $user->getAge(),
                ':created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Database error while saving user: " . $e->getMessage());
            throw new RuntimeException("Could not save the user.");
        }
    }

    /**
     *
     * @return \Generator<int, User>
     */
    // this function return the active users
    public function getAllUsers(): \Generator
    {
        $batchSize = 100;
        $lastId = null;
        try {
            while (true) {
                if ($lastId === null) {

                    $sql = "SELECT id, username, password, age, email, created_at, updated_at, deleted_at 
                            FROM users 
                            WHERE deleted_at IS NULL
                            ORDER BY id ASC 
                            LIMIT :limit";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(':limit', $batchSize, PDO::PARAM_INT);
                } else {
                    $sql = "SELECT id, username, password, age, email, created_at, updated_at, deleted_at 
                            FROM users 
                            WHERE id > :last_id AND deleted_at IS NULL
                            ORDER BY id ASC 
                            LIMIT :limit";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(':last_id', $lastId, PDO::PARAM_STR);
                    $stmt->bindValue(':limit', $batchSize, PDO::PARAM_INT);

                }
                $stmt->execute();
                $rows = $stmt->fetchAll();
                if (empty($rows)) {
                    break;
                }

                foreach ($rows as $row) {
                    $lastId = $row['id'];
                    yield new User(
                        username: $row['username'],
                        passwordHash: $row['password'],
                        age: (int)$row['age'],
                        email: $row['email'],
                        createdAt: new DateTimeImmutable($row['created_at']),
                        updatedAt: isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null,
                        deletedAt: isset($row['deleted_at']) ? new DateTimeImmutable($row['deleted_at']) : null,
                        id: Uuid::fromString($row['id'])
                    );
                }

            }
        } catch (PDOException $e) {
            error_log("Database error while retrieving user: " . $e->getMessage());
            throw new RuntimeException("Could not retrieve users from the database.");
        }
    }

    public function deleteUser(string $id): bool
    {

        if ($id === "") {
            error_log("Database error while deleting user: " . $id);
            throw new RuntimeException("User id is not set.");
        }

        $sqlSelect = "SELECT id, username, password, age, email, created_at, updated_at, deleted_at 
                      FROM users 
                      WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->pdo->prepare($sqlSelect);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new RuntimeException("User not found or already deleted.");
        }
        $sqlUpdate = "UPDATE users SET deleted_at = :deleted_at WHERE id = :id";
        $updateStmt = $this->pdo->prepare($sqlUpdate);
        return $updateStmt->execute([
            ':deleted_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ':id' => $id
        ]);
    }

    public function updateUser(User $user): bool
    {
        $sql = "UPDATE users 
                SET username = :username, 
                    email = :email, 
                    password = :password, 
                    age = :age, 
                    updated_at = :updated_at 
                WHERE id = :id AND deleted_at IS NULL";
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare($sql);

            $result = $stmt->execute([
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':password' => $user->getHashPassword(),
                ':age' => $user->getAge(),
                ':updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                ':id' => $user->getId()
            ]);

            $this->pdo->commit();
            return $result;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Database error while updating user: " . $e->getMessage());
            throw new RuntimeException("Could not update the user.");

        }
    }

    public function findByEmail(string $email): ?User
    {
        if (trim($email) === "") {
            throw new RuntimeException("Email cannot be empty");
        }

        $sql = "SELECT id, username, password, age, email, created_at, updated_at, deleted_at 
            FROM users 
            WHERE email = :email AND deleted_at IS NULL";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new User(
            username: $row['username'],
            passwordHash: $row['password'],
            age: (int)$row['age'],
            email: $row['email'],
            createdAt: new DateTimeImmutable($row['created_at']),
            updatedAt: isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null,
            deletedAt: isset($row['deleted_at']) ? new DateTimeImmutable($row['deleted_at']) : null,
            id: Uuid::fromString($row['id'])
        );
    }
}
