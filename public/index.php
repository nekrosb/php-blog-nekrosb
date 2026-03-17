<?php
require "../src/classes/working-whith-db.php";
$db = Database::getInstance();
$posts = $db->getPosts();


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
        <?php foreach ($posts as $index => $post): ?>

            <div class="post">

                <h2><?= htmlspecialchars($post['title']) ?></h2>

                php if ($post['image']): ?>
                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">

                <div class="content" id="content-<?= $index ?>">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <button class="toggle-btn" aria-expanded="false" aria-controls="content-<?= $index ?>">read more</button>

                <form action="/post-edition.php" method="GEt">
                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                    <button type="submit">Edit Post</button>
                </form>

            </div>

        <?php endforeach; ?>

        <script>
            document.querySelectorAll(".toggle-btn").forEach(button => {

                button.addEventListener("click", function() {

                    const content = this.parentElement.querySelector(".content");

                    const isOpen = content.classList.contains("open");

                    if (!isOpen) {

                        const fullHeight = content.scrollHeight;

                        content.style.height = content.clientHeight + "px";

                        requestAnimationFrame(() => {
                            content.style.height = fullHeight + "px";
                        });

                        content.classList.add("open");

                        this.textContent = "read less";

                    } else {


                        const currentHeight = content.scrollHeight;

                        content.style.height = currentHeight + "px";

                        requestAnimationFrame(() => {
                            content.style.height = "4.5em";
                        });

                        content.classList.remove("open");

                        this.textContent = "read more";
                    }

                });

            });
        </script>


    </div>
    <footer>
        <p>&copy; 2023 My Blog. All rights reserved.</p>
    </footer>
</body>

</html>