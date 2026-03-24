<?php
require_once __DIR__ . "/../src/classes/user.php";
?>

<header>
    <nav>

        <ul>
            <?php if (User::checkSession()): ?>
                <li><a href="/profile.php" class="login">Profile</a></li>
                <li><a href="/logout.php" onclick="return confirm('Are you sure you want to log out?');" class="login">Log Out</a></li>
                <li><a href="/create-post.php" class="btn-create-post" aria-label="create new post">+</a></li>
            <?php else: ?>
                <li>
                    <a href="/login.php" class="login">Log In</a>
                </li>
                <li><a href="/register.php" class="register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>