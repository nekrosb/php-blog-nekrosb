<?php
require_once __DIR__ . "/../src/classes/user.php";
?>

<header>
    <nav>

        <ul>
            <?php if (User::checkSession()): ?>
                <li><a href="/edition-profile.php" class="login">Profile</a></li>
                <li>
                    <form role="link" action="/logout.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to log out?');" class="login" role="link">log out</button>
                    </form>
                </li>
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