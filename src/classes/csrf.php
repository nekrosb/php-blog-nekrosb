<?php
class Csrf
{
    public static function generateToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function validateToken(string $redirect): void
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_msg'] = "Invalid CSRF token.";
            header("Location: $redirect");
            exit();
        }
    }
}
