<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>login</title>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="menu-container">
        <form action="" method="POST">
            <label for="email">email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Log In</button>
        </form>
    </div>

</body>

</html>