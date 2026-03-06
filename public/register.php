<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>

<body>

    <?php include "header.php"; ?>

    <div class="menu-container">
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="e-mail">Email:</label>
            <input type="email" id="e-mail" name="e-mail" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
            <button type="button" aria-label="cancel registration">Cancel</button>
        </form>
    </div>


</body>

</html>