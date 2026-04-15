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

    public function ensureDBExists(): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='posts'"
        );
        $stmt->execute();

        if (!$stmt->fetch()) {
            $this->createDB();
            $this->createUser("admin", "admin@gmail.com", "Admin123@", "admin");
        }
    }

    public function createDB(): void
    {
        $sqlFile = __DIR__ . "/../dataBase/schema.sql";

        $sql = file_get_contents($sqlFile);
        $sqlCommands = explode(";", $sql);

        foreach ($sqlCommands as $command) {
            $trimmedCommand = trim($command);
            if (!empty($trimmedCommand)) {
                $this->pdo->exec($trimmedCommand);
            }
        }
    }

    // --- Posts Management ---

    public function getTotalNumberOfPosts(): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getPosts(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.title, p.image, p.content, p.author_id, p.created_at, p.category_id, u.name, cat.name as category_name,
                   (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count
            FROM posts p
            JOIN users u ON p.author_id = u.id
            LEFT JOIN categories cat on p.category_id = cat.id
            ORDER BY created_at DESC
            LIMIT :limit 
            OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.title, p.content, p.created_at, p.image, p.category_id, p.author_id, u.name, cat.name as category_name
            FROM posts p 
            JOIN users u ON p.author_id = u.id
            LEFT JOIN categories cat on p.category_id = cat.id
            WHERE p.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ?: null;
    }

    public function createPost(string $title, string $content, ?string $imagePath, int $authorId, ?int $categoryId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO posts (title, content, image, author_id, category_id)
            VALUES (:title, :content, :image, :author_id, :category_id)
        ");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function updatePost(int $id, string $title, string $content, ?string $imagePath, ?int $categoryId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE posts
            SET title = :title, content = :content, image = :image, category_id = :category_id
            WHERE id = :id
        ");

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }

    // --- Users Management ---

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

    public function createUser(string $username, string $email, string $password, string $role = 'user'): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, role, password)
            VALUES (:username, :email, :role, :password)
        ");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));

        $stmt->execute();
    }

    public function takeUser(string $email, string $password): array // returns [id, role]
    {
        $stmt = $this->pdo->prepare("SELECT id, role, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        return [(int)$user['id'], $user['role']];
    }

    public function getUserById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT name, email, password FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    // --- Comments Management ---

    public function getPostsComments(int $postId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.content, c.created_at, u.name 
            FROM comments c
            JOIN users u ON c.author_id = u.id
            WHERE c.post_id = :postId
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createComment(int $postId, int $authorId, string $content): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (post_id, author_id, content)
            VALUES (:postId, :authorId, :content)
        ");

        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':authorId', $authorId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function changeName(int $userId, string $newName): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :newName WHERE id = :userId");
        $stmt->bindParam(':newName', $newName, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function changeEmail(int $userId, string $newEmail): void
    {
        $this->checkMailExistsForRegistration($newEmail);
        $stmt = $this->pdo->prepare("UPDATE users SET email = :newEmail WHERE id = :userId");
        $stmt->bindParam(':newEmail', $newEmail, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function
    changePassword(int $userId, string $newPassword): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :newPassword WHERE id = :userId");
        $stmt->bindValue(':newPassword', password_hash($newPassword, PASSWORD_DEFAULT));
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public
    function getAllCategories(): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM categories");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
