<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $pathWeb;
    private string $dirUpload;
    private Filesystem $fs;

    public function __construct(string $pathWeb, string $dirUpload, Filesystem $fs)
    {
        $this->pathWeb = $pathWeb;
        $this->dirUpload = $dirUpload;
        $this->fs = $fs;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = $this->makeFileName($file);
        $dir = $this->getPathUploadDir();

        $this->moveFile($fileName, $dir, $file);

        return "{$this->getDirUpload()}/$fileName";
    }

    public function deleteFile(string $fileName): void
    {
        if (!$fileName) {
            throw new \Exception("Empty file name");
        }

        $fileName = basename($fileName);
        $path = $this->getPathFile($fileName);

        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
        }
    }

    public function getPathFile(string $fileName): string
    {
        return "{$this->getPathUploadDir()}/$fileName";
    }

    public function getPathUploadDir(): string
    {
        return "$this->pathWeb/{$this->getDirUpload()}";
    }

    public function getDirUpload(): string
    {
        return $this->dirUpload;
    }

    public function setDirUpload(string $dir): void
    {
        $this->dirUpload = $dir;
    }

    protected function moveFile(string $fileName, string $dir, UploadedFile $file): void
    {
        try {
            $this->fs->mkdir($dir);
        } catch (\Exception $e) {
            $msg = sprintf('Error create directory `%s`', $e->getMessage());
            throw new \Exception($msg, $e->getCode(), $e);
        }

        try {
            $file->move($dir, $fileName);
        } catch (FileException $e) {
            $msg = sprintf('Error upload file `%s`', $e->getMessage());
            throw new \Exception($msg, $e->getCode(), $e);
        }
    }

    protected function makeFileName(UploadedFile $file): string
    {
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->guessExtension();

        $fileName = $this->getVerifiedFileName($fileName, $ext);
        return "$fileName.$ext";
    }

    protected function getVerifiedFileName(string $fileName, string $ext): string
    {
        $dir = $this->getPathUploadDir();

        $maxIndex = 1000;
        $index = 0;

        do {
            if ($index > 0) {
                $fileName = "$fileName-$index";
            }

            $index++;
            $path = "$dir/$fileName.$ext";
        } while ($this->fs->exists($path) && $index < $maxIndex);

        return $fileName;
    }
}
