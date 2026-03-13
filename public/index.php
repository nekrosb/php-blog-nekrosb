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
        <?php foreach ($posts as $post): ?>

            <div class="post">

                <h2><?= $post['title'] ?></h2>

                <img src="<?= $post['image'] ?>" class="post-image">

                <div class="content">
                    <?= nl2br($post['content']) ?>
                </div>

                <button class="toggle-btn">read more</button>

            </div>

        <?php endforeach; ?>

        <script>
            document.querySelectorAll(".toggle-btn").forEach(button => {

                        button.addEventListener("click", function() {

                                const content = this.previousElementSibling;

                                content.classList.toggle("open");

                                if (content.classList.contains("open")) {
                                    this.textContent = "read less";
                                } else {
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