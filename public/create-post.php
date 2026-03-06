<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>create post</title>
</head>

<body>
    <header>
        <h1>create post</h1>
    </header>

    <div class="menu-container">
        <form action="" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>

            <button type="submit">Create Post</button>
            <button type="reset">Reset</button>
        </form>
    </div>

</body>

</html>