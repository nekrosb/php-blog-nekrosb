<?php
session_start();
require __DIR__ . "/../src/classes/upload-and-load-file.php";
require __DIR__ . "/../src/classes/working-with-db.php";
require __DIR__ . "/../src/classes/user.php";
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailF = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $email = trim($emailF);
    $passwordF = filter_input(INPUT_POST, 'password');
    $password = trim($passwordF);

    if (empty($email) || empty($password)) {
        $_SESSION["flash_error"] = "Email and password are required";
        header("location: /login.php");
        exit();
    }


    try {

        $id = $db->takeUser($email, $password);
        $_SESSION["id"] = $id;
    } catch (Exception $e) {
        $_SESSION["flash_error"] = $e->getMessage();
        header("location: /login.php");
        exit();
    }

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
    <title>login</title>
</head>

<body>

    <?php include "header.php"; ?>

    <div class="menu-container">
        <?PHP include "flashMsg.php" ?>
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