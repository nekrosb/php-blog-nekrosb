<?php
session_start();
require __DIR__ . "/../src/classes/apload-and-load-filed.php";
require __DIR__ . "/../src/classes/working-whith-db.php";
$db = Database::getInstance();

if (isset($_SESSION["flash_error"])) {
    echo $_SESSION["flash_error"];
    unset($_SESSION["flash_error"]);
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$post = null;
$title = '';
$content = '';
$path;

if ($id) {
    $post = $db->getPostById($id);
    if ($post) {
        $title = $post['title'];
        $content = $post['content'];
        $path = $post['image'];
    } else {
        $_SESSION["flash_error"] = "Post not found";
        header("Location: /");
        exit();
    }
}






if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleF = filter_input(INPUT_POST, 'title');
    $title = trim($titleF);
    $contentF = filter_input(INPUT_POST, 'content');
    $content = trim($contentF);

    $path = null;

    if (empty($title) || empty($content)) {
        $_SESSION["flash_error"] = "title and content are required";
        header("location: /post-edition.php?id=" . $id);
        exit();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        try {
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

    <?php include "header.php"; ?>
    <div class="menu-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required value="<?= $title ?>">

            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?= $content ?></textarea>
            <label for="upload-file">Upload your file</label>
            <input type="file" id="upload-file" name="image">
            <button type="submit">edit Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>