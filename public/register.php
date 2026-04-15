<?php
session_start();
require_once __DIR__ . '/../src/classes/csrf.php';
Csrf::generateToken();

require_once __DIR__ . "/../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";
$db = Database::getInstance();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validateToken("register.php");

    $usernameField = filter_input(INPUT_POST, "username");
    $emailField = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $passwordField = filter_input(INPUT_POST, "password");
    $username = trim($usernameField);
    $email = trim($emailField);
    $password = trim($passwordField);

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION["flash_msg"] = "All fields are required.";
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
        $_SESSION["flash_msg"] = $e->getMessage();
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
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
            <button type="button" aria-label="cancel registration">Cancel</button>
        </form>
    </div>


</body>

</html>