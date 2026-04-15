<?php
session_start();
require_once __DIR__ . '/../src/classes/csrf.php';
Csrf::generateToken();
require_once __DIR__ . "/../src/classes/upload-and-load-file.php";
require_once __DIR__ . "/../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";
User::requireLogin('/login.php', 'You must be logged in to edit a post');

$db = Database::getInstance();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validateToken("/create-post.php");

    $id  = $_SESSION["id"];
    $titleField = filter_input(INPUT_POST, 'title');
    $title = trim($titleField);
    $contentField = filter_input(INPUT_POST, 'content');
    $content = trim($contentField);
    $categoryId = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
    $path = null;

    if (empty($title) || empty($content) || !$categoryId) {
        $_SESSION["flash_msg"] = "title, content and category are required";
        header("location: /create-post.php");
        exit();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        try {
            $files = new File();
            $files->fileCheck($_FILES);
            $path = $files->uploadFile($_FILES);
        } catch (Exception $e) {
            $_SESSION["flash_msg"] = $e->getMessage();
            header("location: /create-post.php");
            exit();
        }
    }


    $db->createPost($title, $content, $path, $id, $categoryId);
    $_SESSION["flash_msg"] = "Post created successfully";
    header("Location: /");
    exit();
}

$categories = $db->getAllCategories();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>create post</title>
</head>

<body>

    <?php include "header.php"; ?>
    <div class="menu-container">
        <?php include "flashMsg.php" ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="upload-file">Upload your file</label>
            <input type="file" id="upload-file" name="image">
            <button type="submit">Create Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>