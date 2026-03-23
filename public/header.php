<?php

?>

<header>
    <nav>

        <ul>
            <?php if (isset($_SESSION["id"])): ?>
                <li><a href="/profile.php" class="login">Profile</a></li>
                <li><a href="/logout.php" class="login">Log Out</a></li>
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