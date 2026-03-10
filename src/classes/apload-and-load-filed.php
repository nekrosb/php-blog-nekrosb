<?php

class File
{


    public static function fileCheck(array $file): void
    {
        if ($file['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("upload error");
        }

        if ($file['image']['size'] > 5 * 1024 * 1024) {
            throw new Exception("file too large");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['image']['tmp_name']);

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];

        if (!in_array($mime, $allowed)) {
            throw new Exception("invalid file type");
        }

        if (getimagesize($file['image']['tmp_name']) === false) {
            throw new Exception("not a valid image");
        }
    }

    public function uploadFile(array $file): string
    {
        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $name = uniqid() . '_' . basename($file['image']['name']);
        $filePath = $targetDir . $name;
        if (move_uploaded_file($file['image']['tmp_name'], $filePath)) {

            return "uploads/" . $name;
        } else {
            throw new Exception("failed to upload file");
        }
    }
}
