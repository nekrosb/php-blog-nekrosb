<?php
class User
{

    public function checkPassword(string $password)
    {
        if (mb_strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Password must contain at least one uppercase letter");
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Password must contain at least one lowercase letter");
        }

        if (!preg_match('/\d/', $password)) {
            throw new Exception("Password must contain at least one digit");
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            throw new Exception("Password must contain at least one special character");
        }
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['id']);
    }

    public static function requireLogin(string $redirectPath = '/', string $flashMsg = "You must be logged in to access this page."): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['flash_msg'] = $flashMsg;
            header("Location: $redirectPath");
            exit();
        }
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function canEdit(int $authorId): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }
        return (int)$_SESSION['id'] === $authorId || self::isAdmin();
    }
}
