<?php
session_start();
require_once __DIR__ . "/../src/classes/upload-and-load-file.php";
require_once __DIR__ . "/../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";
if (!User::checkSession()) {
    $_SESSION["flash_error"] = "You must be logged in to edit a post";
    header("Location: /login.php");
    exit();
}
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
    } else {
        $_SESSION["flash_error"] = "Post not found";
        header("Location: /");
        exit();
    }
} else {
    $_SESSION["flash_error"] = "Invalid post ID";
    header("Location: /");
    exit();
}

if ((int)$_SESSION['id'] !== (int)$authorId) {
    $_SESSION["flash_error"] = "You are not authorized to edit this post";
    header("Location: /");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleField = filter_input(INPUT_POST, 'title');
    $title = trim($titleField);
    $contentField = filter_input(INPUT_POST, 'content');
    $content = trim($contentField);

    if (empty($title) || empty($content)) {
        $_SESSION["flash_error"] = "title and content are required";
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
            $_SESSION["flash_error"] = $e->getMessage();
            header("location: /post-edition.php?id=" . $id);
            exit();
        }
    }


    $db->updatePost($id, $title, $content, $path);

    header("Location: /");
    exit();
}

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
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>">

            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
            <label for="upload-file">Upload your file</label>
            <input type="file" id="upload-file" name="image">
            <button type="submit">edit Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>