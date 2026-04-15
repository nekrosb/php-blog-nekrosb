<?php
$content = file_get_contents('public/edition-profile.php');
$content = preg_replace(
    "/if \(!User::checkSession\(\)\) \{\s+\\\$_SESSION\['flash_msg'\] \= \"[^\"]+\";\s+header\(\"Location: [^\"]+\"\);\s+exit\(\);\s+\}/m",
    "User::requireLogin();",
    $content
);
file_put_contents('public/edition-profile.php', $content);

foreach (['public/create-post.php', 'public/post-edition.php'] as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $content = preg_replace(
        "/if \(!User::checkSession\(\)\) \{\s+\\\$_SESSION\[\"flash_msg\"\] \= \"You must be logged in to edit a post\";\s+header\(\"Location: \/login\.php\"\);\s+exit\(\);\s+\}/m",
        "User::requireLogin('/login.php', 'You must be logged in to edit a post');",
        $content
    );
    file_put_contents($file, $content);
}

$header = file_get_contents('public/header.php');
$header = str_replace('User::checkSession()', 'User::isLoggedIn()', $header);
file_put_contents('public/header.php', $header);

echo "Done";
