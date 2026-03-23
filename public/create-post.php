<?php
session_start();
require __DIR__ . "/../src/classes/upload-and-load-file.php";
require __DIR__ . "/../src/classes/working-with-db.php";
require __DIR__ . "/../src/classes/user.php";
if (!User::checkSession($_SESSION["id"])) {
    $_SESSION["flash_error"] = "You must be logged in to edit a post";
    header("Location: /login.php");
    exit();
}

$db = Database::getInstance();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = $_SESSION["id"];
    $titleField = filter_input(INPUT_POST, 'title');
    $title = trim($titleField);
    $contentField = filter_input(INPUT_POST, 'content');
    $content = trim($contentField);
    $path = null;

    if (empty($title) || empty($content)) {
        $_SESSION["flash_error"] = "title and content are required";
        header("location: /create-post.php");
        exit();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        try {
            $files = new File();
            $files->fileCheck($_FILES);
            $path = $files->uploadFile($_FILES);
        } catch (Exception $e) {
            $_SESSION["flash_error"] = $e->getMessage();
            header("location: /create-post.php");
            exit();
        }
    }


    $db->createPost($title, $content, $path, $id);

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
    <title>create post</title>
</head>

<body>

    <?php include "header.php"; ?>
    <div class="menu-container">
        <?php include "flashMsg.php" ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>
            <label for="upload-file">Upload your file</label>
            <input type="file" id="upload-file" name="image">
            <button type="submit">Create Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>