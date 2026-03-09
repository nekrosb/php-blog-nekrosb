<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>edit post</title>
</head>
<?php include "header.php"; ?>

<div class="menu-container">
    <form action="" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">Save</button>
        <button type="button" aria-label="cancel editing">Cancel</button>
    </form>
</div>

<body>

</body>

</html>