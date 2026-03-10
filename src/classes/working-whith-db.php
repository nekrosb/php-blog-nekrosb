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
}
