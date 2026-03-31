<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . "/../src/classes/working-with-db.php";
require_once __DIR__ . "/../src/classes/user.php";

if (!User::checkSession()) {
    $_SESSION['flash_msg'] = "You must be logged in to access this page.";
    header("Location: /");
    exit();
}

$db = Database::getInstance();
$userId = (int)$_SESSION['id'];
$user = $db->getUserById($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_msg'] = "Invalid CSRF token.";
        header("Location: edition-profile.php");
        exit();
    }

    if (isset($_POST["name"])) {
        $newNameFromUser = filter_input(INPUT_POST, 'name');
        $newName = trim($newNameFromUser);
        if (empty($newName)) {
            $_SESSION['flash_msg'] = "Name cannot be empty.";
            header("Location: edition-profile.php");
            exit();
        }
        $db->changeName($userId, $newName);
        $_SESSION['flash_msg'] = "Name updated successfully.";
        header("Location: edition-profile.php");
        exit();
    }

    if (isset($_POST["email"])) {
        $newEmailFromUser = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $newEmail = trim($newEmailFromUser);
        if (empty($newEmail)) {
            $_SESSION['flash_msg'] = "Email cannot be empty.";
            header("Location: edition-profile.php");
            exit();
        }
        if ($newEmail === $user["email"]) {
            $_SESSION['flash_msg'] = "New email cannot be the same as the current email.";
            header("Location: edition-profile.php");
            exit();
        }
        try {
            $db->changeEmail($userId, $newEmail);
            $_SESSION['flash_msg'] = "Email updated successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_msg'] = $e->getMessage();
        }
        header("Location: edition-profile.php");
        exit();
    }

    if (isset($_POST["password"], $_POST["old_password"])) {
        $oldPassword = trim($_POST['old_password']);
        $newPassword = trim($_POST['password']);

        if (empty($oldPassword) || empty($newPassword)) {
            $_SESSION['flash_msg'] = "Both old and new passwords are required.";
            header("Location: edition-profile.php");
            exit();
        }

        if (!password_verify($oldPassword, $user['password'])) {
            $_SESSION['flash_msg'] = "Incorrect old password.";
            header("Location: edition-profile.php");
            exit();
        }

        if ($oldPassword === $newPassword) {
            $_SESSION['flash_msg'] = "New password cannot be the same as the old password.";
            header("Location: edition-profile.php");
            exit();
        }

        try {
            $userObj = new User();
            $userObj->checkPassword($newPassword);

            $db->changePassword($userId, $newPassword);
            $_SESSION['flash_msg'] = "Password updated successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_msg'] = $e->getMessage();
        }

        header("Location: edition-profile.php");
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
    <title>Profile Edition</title>
</head>

<body>
    <div class="post-container">
        <div class="post">
            <?php include "flashMsg.php" ?>
            <h1><?php echo htmlspecialchars($user['name']); ?></h1>
            <p><?php echo htmlspecialchars($user['email']); ?></p>

            <form action="edition-profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="new name" required>
                <button type="submit">Save Changes</button>
            </form>

            <form action="edition-profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="new email" required>
                <button type="submit">Save Changes</button>
            </form>

            <form action="edition-profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" placeholder="old password" required>
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" placeholder="new password" required>
                <button type="submit">Save Changes</button>
            </form>

        </div>
    </div>
</body>

</html>