<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $pathDirBase;
    private string $dirUpload;
    private Filesystem $fs;

    public function __construct(string $pathDirBase, string $dirUpload, Filesystem $fs)
    {
        $this->pathDirBase = $pathDirBase;
        $this->dirUpload = $dirUpload;
        $this->fs = $fs;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = $this->makeFileName($file);
        $dir = $this->getPathUploadDir();

        $this->moveFile($fileName, $dir, $file);

        return $fileName;
    }

    public function deleteFile(string $fileName): void
    {
        $path = $this->getPathFile($fileName);
        $this->fs->remove($path);
    }

    public function getPathFile(string $fileName): string
    {
        return "{$this->getPathUploadDir()}/$fileName";
    }

    public function getPathUploadDir(): string
    {
        return "$this->pathDirBase/{$this->getDirUpload()}";
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
        /**
         * https://www.php.net/manual/ru/function.pathinfo.php
         * pathinfo() учитывает настройки локали, поэтому для корректной обработки пути с многобайтными символами
         * должна быть установлена соответствующая локаль с помощью функции setlocale().
         *
         * Имя файла с многобайтными символами, возвращает пустым
         * На данный момент проблему решил так
         */
        $locale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL,'en_US.UTF-8');
        $rawFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        setlocale(LC_ALL, $locale);

        $fileName = $rawFileName;
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
