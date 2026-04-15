<?php
$files = glob("public/*.php");
foreach ($files as $file) {
    if (is_dir($file)) continue;
    $content = file_get_contents($file);
    
    // Replace generation
    $genOld = "if (empty(\$_SESSION['csrf_token'])) {\n    \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n}";
    $genNew = "require_once __DIR__ . '/../src/classes/csrf.php';\nCsrf::generateToken();";
    $content = str_replace($genOld, $genNew, $content);
    
    // Using Regex to abstract different Location headers
    $regexVal = "/if \(\!isset\(\\\$_POST\['csrf_token'\]\) \|\| \\\$_POST\['csrf_token'\] \!\=\= \\\$_SESSION\['csrf_token'\]\) \{\s+\\\$_SESSION\['flash_msg'\] \= \"Invalid CSRF token\.\";\s+header\(\"Location\: ([^\"]+)\"\);\s+exit\(\);\s+\}/m";
    $content = preg_replace_callback($regexVal, function($matches) {
        $loc = str_replace('.php', '.php', $matches[1]); // Ensure it matches
        return "Csrf::validateToken('$loc');";
    }, $content);
    
    file_put_contents($file, $content);
}
echo "Done";
