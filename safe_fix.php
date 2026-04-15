<?php
$fixes = [
    'public/login.php' => '/login.php',
    'public/register.php' => 'register.php',
    'public/comments.php' => 'comments.php?id=" . $postId',
    'public/post-edition.php' => '/post-edition.php?id=" . $id',
    'public/edition-profile.php' => 'edition-profile.php',
    'public/create-post.php' => '/create-post.php',
    'public/logout.php' => '/',
    'public/index.php' => null, // Just token generation maybe
];

foreach ($fixes as $file => $loc) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // Replace Generation
    $genOld = "if (empty(\$_SESSION['csrf_token'])) {\n    \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n}";
    $genNew = "require_once __DIR__ . '/../src/classes/csrf.php';\nCsrf::generateToken();";
    $content = str_replace($genOld, $genNew, $content);
    
    $genOldAlt = "if (empty(\$_SESSION['csrf_token'])) {\r\n    \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\r\n}";
    $content = str_replace($genOldAlt, $genNew, $content);

    // Replace Validation
    if ($loc !== null) {
        $valOld1 = "if (!isset(\$_POST['csrf_token']) || \$_POST['csrf_token'] !== \$_SESSION['csrf_token']) {\n        \$_SESSION['flash_msg'] = \"Invalid CSRF token.\";\n        header(\"Location: $loc\");\n        exit();\n    }";
        $valOld2 = "if (!isset(\$_POST['csrf_token']) || \$_POST['csrf_token'] !== \$_SESSION['csrf_token']) {\n        \$_SESSION['flash_message'] = \"Invalid CSRF token.\";\n        header(\"Location: $loc\");\n        exit();\n    }";
        
        $valOld3 = "if (!isset(\$_POST['csrf_token']) || \$_POST['csrf_token'] !== \$_SESSION['csrf_token']) {\n        header(\"Location: $loc\");\n        exit();\n    }";

        $valNew = "Csrf::validateToken(\"$loc\");";
        if (strpos($loc, '"') !== false) {
             $valNew = "Csrf::validateToken(\"$loc);";
             // e.g. Csrf::validateToken("comments.php?id=" . $postId);
        }
        
        if ($loc === 'comments.php?id=" . $postId') {
             $valNew = "Csrf::validateToken(\"comments.php?id=\" . \$postId);";
        }
        if ($loc === '/post-edition.php?id=" . $id') {
             $valNew = "Csrf::validateToken(\"/post-edition.php?id=\" . \$id);";
        }

        $content = str_replace($valOld1, $valNew, $content);
        $content = str_replace($valOld2, $valNew, $content);
        $content = str_replace($valOld3, $valNew, $content);
        
        // Also check \r\n versions
        $valOld1R = str_replace("\n", "\r\n", $valOld1);
        $valOld2R = str_replace("\n", "\r\n", $valOld2);
        $valOld3R = str_replace("\n", "\r\n", $valOld3);
        $content = str_replace($valOld1R, $valNew, $content);
        $content = str_replace($valOld2R, $valNew, $content);
        $content = str_replace($valOld3R, $valNew, $content);
    }
    
    file_put_contents($file, $content);
}
echo "Done replacing.";
