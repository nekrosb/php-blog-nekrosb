class file {
private static arr = ['image/jpeg', 'image/png', 'image/gif'];

public static function fileCheck(array $file): void {
if ($file['image']['error'] === apload_err_non) {
throw new Exception("file is not aploaded");
}

if (!in_array($file['image']['type'], self::arr)) {
throw new Exception("file type is not allowed");
}

if ($file['image']['size'] > 5 * 1024 * 1024) {
throw new Exception("file size is too large (max 5MB)");
}
}

public function aploadFile(array $file): void {
$targetDir = __DIR__ . '/../uploads/';
if (!is_dir($targetDir)) {
mkdir($targetDir, 0755, true);
}
$name = uniqid() . '_' . basename($file['image']['name']);
$filePath = $targetDir . $name;
if (move_uploaded_file($file['image']['tmp_name'], $filePath)) {
echo "file aploaded successfully: " . $name;
} else {
throw new exception("failed to apload file");
}
}

}