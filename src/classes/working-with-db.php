<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dbPath = __DIR__ . "/../dataBase/data.db";

        try {
            $this->pdo = new PDO("sqlite:" . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Failed to connect to the database: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
            self::$instance->ensureDBExists();
        }

        return self::$instance;
    }

    public function createDB(): void
    {
        $sqlFile = __DIR__ . "/../dataBase/schema.sql";

        $sql = file_get_contents($sqlFile);
        $sqlCommand = explode(";", $sql);

        foreach ($sqlCommand as $command) {
            $trimmedCommand = trim($command);
            if (!empty($trimmedCommand)) {
                $this->pdo->exec($trimmedCommand);
            }
        }
    }

    public function createPost(string $title, string $content, ?string $imagePath): void
    {
        $authorId = 1;

        $stmt = $this->pdo->prepare("
            INSERT INTO posts (title, content, image, author_id)
            VALUES (:title, :content, :image, :author_id)
        ");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':author_id', $authorId);

        $stmt->execute();
    }


    public function ensureDBExists(): void
    {

        $result = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='posts'"
        );

        if (!$result->fetch()) {

            $this->createDB();
        }
    }

    public function getPosts(): array
    {
        $stmt = $this->pdo->query("SELECT id, title, image, content FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePost(int $id, string $title, string $content, ?string $imagePath): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE posts
            SET title = :title, content = :content, image = :image
            WHERE id = :id
        ");

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }
    public function getPostById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT  title, content, image FROM posts WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ?: null;
    }

    public function checkMailExistsForRegistration(string $email): void
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            throw new Exception("Email already exists");
        }
    }

    public function createUser(string $username, string $email, string $password): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:username, :email, :password)
        ");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));

        $stmt->execute();
    }

    public function takeUser(string $email, string $password): int
    {
        $stmt = $this->pdo->prepare("SELECT id, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        return (int)$user['id'];
    }
}
