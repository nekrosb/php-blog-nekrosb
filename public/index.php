<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once "../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";

$db = Database::getInstance();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit = 10; // Number of posts per page
$offset = ($page - 1) * $limit;
$numberOfPages = ceil($db->getTotalNumberOfPosts() / $limit);
$posts = $db->getPosts($limit, $offset);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>home page</title>
</head>

<body>

    <?php include 'header.php'; ?>


    <div class="post-container">
        <?php
        include "flashMsg.php";

        ?>

        <?php foreach ($posts as $index => $post): ?>

            <div class="post">

                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <p>by <?php echo htmlspecialchars($post['name']); ?></p>
                <p>create at <?php echo htmlspecialchars($post['created_at']); ?></p>


                <?php if ($post['image']): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                <?php endif; ?>

                <div class="content" id="content-<?php echo $index; ?>">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <button class="toggle-btn" aria-expanded="false" aria-controls="content-<?php echo $index; ?>">read more</button>


                <?php if (User::canEdit((int)$post['author_id'])): ?>
                    <form action="/post-edition.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                        <button type="submit">Edit Post</button>
                    </form>
                <?php endif; ?>

                <a href="comments.php?id=<?php echo $post['id']; ?>">View Comments (<?php echo $post['comment_count']; ?>)</a>

            </div>

        <?php endforeach; ?>


        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".toggle-btn").forEach(button => {
                    const content = button.parentElement.querySelector(".content");

                    // Reliably check if content overflows the clamped 3 lines
                    // Momentarily remove the max-height and clamp to get true height
                    const originalMaxHeight = content.style.maxHeight;
                    content.classList.add("open");
                    const fullHeight = content.scrollHeight;
                    content.classList.remove("open");

                    // If the unabridged height is basically the same as the clamped height 
                    // (with a small margin of error for line heights), hide the button.
                    if (fullHeight <= content.clientHeight + 5) {
                        button.style.display = 'none';
                    }

                    button.addEventListener("click", function() {
                        const isOpen = content.classList.contains("open");

                        if (!isOpen) {
                            // Expand
                            content.style.maxHeight = content.clientHeight + "px";
                            content.classList.add("open");
                            content.offsetHeight; // Force layout reflow
                            content.style.maxHeight = content.scrollHeight + "px";

                            this.textContent = "read less";
                            this.setAttribute("aria-expanded", "true");

                            // Cleanup explicitly set max-height after transition
                            setTimeout(() => {
                                if (content.classList.contains("open")) {
                                    content.style.maxHeight = "none";
                                }
                            }, 350);
                        } else {
                            // Collapse
                            content.style.maxHeight = content.scrollHeight + "px";
                            content.offsetHeight; // Force layout reflow
                            content.classList.remove("open");
                            content.style.maxHeight = "4.8rem";

                            this.textContent = "read more";
                            this.setAttribute("aria-expanded", "false");
                        }
                    });
                });
            });
        </script>

    </div>

    <nav>
        <ul>
            <?php for ($i = 1; $i <= $numberOfPages; $i++): ?>
                <li>
                    <?php
                    if ($i === $page) {
                        echo "<strong>$i</strong>";
                    } else {
                        echo "<a href=\"?page=$i\">$i</a>";
                    } ?>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>


    <footer>
        <p>&copy; 2023 My Blog. All rights reserved.</p>
    </footer>
</body>

</html>