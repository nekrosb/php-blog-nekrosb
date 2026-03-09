<?php
function createDB(): void
{
    $sqlFile = __DIR__ . "/../dataBase/schema.sql";
    $dbPath = __DIR__ . "/../dataBase/data.db";
    try {
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    $sql = file_get_contents($sqlFile);
    $sqlCommand = explode(";", $sql);
    foreach ($sqlCommand as $command) {
        $trimmedCommand = trim($command);
        if (!empty($trimmedCommand)) {
            $pdo->exec($trimmedCommand);
        }
    }
}

function createPost(string $title, string $content, ?string $imagePath): void
{
    $dbPath = __DIR__ . "/../dataBase/data.db";
    $authorId = 1;
    try {
        $pdo = new PDO("sqlite:" . $dbPath);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, author_id) VALUES (:title, :content, :image, :author_id)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':image', $imagePath);
    $stmt->bindParam(':author_id', $authorId);
    $stmt->execute();
}
