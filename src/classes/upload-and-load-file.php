<?php

class File
{
    private finfo $finfo;
    private array $allowedMimeTypes;
    private int $maxFileSize;
    private string $uploadDir;
    private array $extensions;

    public function __construct()
    {
        $this->finfo = new finfo(FILEINFO_MIME_TYPE);


        $this->allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];


        $this->maxFileSize = 5 * 1024 * 1024;


        $this->uploadDir = __DIR__ . '/../../public/uploads/';


        $this->extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
        ];


        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function fileCheck(array $file): void
    {
        if ($file['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error");
        }

        if ($file['image']['size'] > $this->maxFileSize) {
            throw new Exception("File too large");
        }

        $mime = $this->finfo->file($file['image']['tmp_name']);

        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new Exception("Invalid file type");
        }

        if (getimagesize($file['image']['tmp_name']) === false) {
            throw new Exception("Not a valid image");
        }
    }

    public function uploadFile(array $file): string
    {
        $mime = $this->finfo->file($file['image']['tmp_name']);

        if (!isset($this->extensions[$mime])) {
            throw new Exception("Invalid file type");
        }

        $ext = $this->extensions[$mime];
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $filePath = $this->uploadDir . $name;

        if (move_uploaded_file($file['image']['tmp_name'], $filePath)) {
            return "uploads/" . $name;
        } else {
            throw new Exception("Failed to upload file");
        }
    }
}
