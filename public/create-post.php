<?php
require __DIR__ . "/../src/classes/apload-and-load-filed.php";
require __DIR__ . "/../src/classes/working-whith-db.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleF = filter_input(INPUT_POST, 'title');
    $title = trim($titleF);
    $contentF = filter_input(INPUT_POST, 'content');
    $content = trim($contentF);
    $path = null;

    if (empty($title) || empty($content)) {
        header("location: /create-post.php");
        exit();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        try {
            $files = new File();
            $files->fileCheck($_FILES);
            $path = $files->uploadFile($_FILES);
        } catch (Exception $e) {
            exit();
        }
    }

    createDB();
    createPost($title, $content, $path);

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