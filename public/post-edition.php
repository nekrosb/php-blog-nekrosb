<?php
session_start();
require_once __DIR__ . '/../src/classes/csrf.php';
Csrf::generateToken();
require_once __DIR__ . "/../src/classes/upload-and-load-file.php";
require_once __DIR__ . "/../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";
User::requireLogin('/login.php', 'You must be logged in to edit a post');
$db = Database::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$post = null;
$title = '';
$content = '';
$path = null;

if ($id) {
    $post = $db->getPostById($id);
    if ($post) {
        $title = $post['title'];
        $content = $post['content'];
        $path = $post['image'];
        $authorId = $post['author_id'];
        $currentCategoryId = $post['category_id'];
    } else {
        $_SESSION["flash_msg"] = "Post not found";
        header("Location: /");
        exit();
    }
} else {
    $_SESSION["flash_msg"] = "Invalid post ID";
    header("Location: /");
    exit();
}

if (!User::canEdit((int)$authorId)) {
    $_SESSION["flash_msg"] = "You are not authorized to edit this post";
    header("Location: /");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validateToken("/post-edition.php?id=" . $id);

    $titleField = filter_input(INPUT_POST, 'title');
    $title = trim($titleField);
    $contentField = filter_input(INPUT_POST, 'content');
    $content = trim($contentField);
    $categoryId = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);

    if (empty($title) || empty($content) || !$categoryId) {
        $_SESSION["flash_msg"] = "title, content and category are required";
        header("location: /post-edition.php?id=" . $id);
        exit();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        try {
            $olderPath = $path;
            if (!empty($olderPath) && file_exists($olderPath)) {
                unlink($olderPath);
            }

            $files = new File();
            $files->fileCheck($_FILES);
            $path = $files->uploadFile($_FILES);
        } catch (Exception $e) {
            $_SESSION["flash_msg"] = $e->getMessage();
            header("location: /post-edition.php?id=" . $id);
            exit();
        }
    }


    $db->updatePost($id, $title, $content, $path, $categoryId);
    $_SESSION["flash_msg"] = "Post updated successfully";
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
    <title>edit post</title>
</head>

<body>
    <?php
    include "flashMsg.php";
    ?>



    <?php include "header.php"; ?>
    <div class="menu-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>">

            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= ($category['id'] == $currentCategoryId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="upload-file">Upload your file</label>
            <input type="file" id="upload-file" name="image">
            <button type="submit">edit Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>