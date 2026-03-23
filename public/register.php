<?php
session_start();

require_once __DIR__ . "/../src/classes/working-whith-db.php";
require_once __DIR__ . "/../src/classes/user.php";
$db = Database::getInstance();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernamef = filter_input(INPUT_POST, "username");
    $emailf = filter_input(INPUT_POST, "e-mail", FILTER_VALIDATE_EMAIL);
    $passwordf = filter_input(INPUT_POST, "password");
    $username = trim($usernamef);
    $email = trim($emailf);
    $password = trim($passwordf);

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION["flash_error"] = "All fields are required.";
        header("Location: register.php");
        exit();
    }

    try {
        $db->checkMailExistsForRegistration($email);
        $user = new User();
        $user->checkPassword($password);
        $db->createUser($username, $email, $password);
        $_SESSION["flash"] = "Registration successful. Please log in.";
        header("Location: login.php");
        exit();
    } catch (Exception $e) {
        $_SESSION["flash_error"] = $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>


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
        <?php include "flashMsg.php" ?>

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