<?php
session_start();
require_once __DIR__ . '/../src/classes/csrf.php';
Csrf::generateToken();
require_once "../src/classes/working-with-db.php";
$db = Database::getInstance();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "Invalid post ID. post not found.";
    header("Location: index.php");
    exit();
}

$postId = (int)$_GET['id'];
$post = $db->getPostById($postId);
if (!$post) {
    $_SESSION['flash_message'] = "Post not found.";
    header("Location: index.php");
    exit();
}

$comments = $db->getPostsComments($postId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validateToken("comments.php?id=" . $postId);

    if (!isset($_SESSION['id'])) {
        $_SESSION['flash_message'] = "You must be logged in to post a comment.";
        header("Location: login.php");
        exit();
    }

    $content = trim($_POST['content']);
    if (empty($content)) {
        $_SESSION['flash_message'] = "Comment content cannot be empty.";
        header("Location: comments.php?id=" . $postId);
        exit();
    }

    $db->createComment($postId, $_SESSION['id'], $content);
    $_SESSION['flash_message'] = "Comment added successfully.";
    header("Location: comments.php?id=" . $postId);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>post`s comments</title>
</head>

<body>
    <?php include "header.php"; ?>

    <div class="post-container">
        <div class="post">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <hr>
            <strong><?php echo htmlspecialchars($post['name']); ?></strong>
            <p>create at <?php echo htmlspecialchars($post['created_at']); ?></p>

            <?php if ($post['image']): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
            <?php endif; ?>

            <div class="content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>

        <?php if (isset($_SESSION['id'])): ?>
            <form action="comments.php?id=<?php echo $postId; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                <textarea name="content" placeholder="Write a comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Log in</a> to post a comment
            </p>
        <?php endif; ?>


        <div class="comments-section">
            <h2>Comments</h2>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?php echo htmlspecialchars($comment['name']); ?></strong>
                        <p>create at <?php echo htmlspecialchars($comment['created_at']); ?></p>
                        <div class="content">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>

</body>

</html>