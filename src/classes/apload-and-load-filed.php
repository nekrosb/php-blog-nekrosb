<?php

class File
{
    private static array $arr = ['image/jpeg', 'image/png', 'image/gif'];

    public static function fileCheck(array $file): void
    {
        if ($file['image']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception("file is not uploaded");
        }

        if (!in_array($file['image']['type'], self::$arr)) {
            throw new Exception("file type is not allowed");
        }

        if ($file['image']['size'] > 5 * 1024 * 1024) {
            throw new Exception("file size is too large (max 5MB)");
        }
    }

    public function uploadFile(array $file): string
    {
        $targetDir = __DIR__ . '/../uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $name = uniqid() . '_' . basename($file['image']['name']);
        $filePath = $targetDir . $name;
        if (move_uploaded_file($file['image']['tmp_name'], $filePath)) {

            return $filePath;
        } else {
            throw new Exception("failed to upload file");
        }
    }
}
