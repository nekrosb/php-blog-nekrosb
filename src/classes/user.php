<?php
class user
{

    function checkPassword(string $password)
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
}
