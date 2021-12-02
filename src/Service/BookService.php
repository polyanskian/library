<?php

namespace App\Service;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookService
{
    private string $dirUpload;
    private EntityManagerInterface $entityManager;
    private FileUploader $fileUploader;
    private Filesystem $fs;

    public function __construct(
        string $dirUpload,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        Filesystem $fs
    ) {
        $this->dirUpload = $dirUpload;
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->fs = $fs;
    }

    public function add(Book $book, ?UploadedFile $fileCover = null, ?UploadedFile $fileBook = null): void
    {
        $this->edit($book, $fileCover, $fileBook);
    }

    public function edit(Book $book, ?UploadedFile $fileCover = null, ?UploadedFile $fileBook = null): void
    {
        if ($book->getId() === null) {
            $this->entityManager->persist($book);
        }

        $this->uploadFiles($book, $fileCover, $fileBook);

        $this->entityManager->flush();
    }

    public function remove(Book $book)
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    public function removeData($book)
    {
        $fileUploader = $this->configureFileUploader($book);
        $dir = $fileUploader->getPathUploadDir();

        if ($this->fs->exists($dir)) {
            $this->fs->remove($dir);
        }
    }

    public function deleteCover(Book $book)
    {
        $cover = $book->getCover();

        if ($cover) {
            $fileUploader = $this->configureFileUploader($book);
            $fileUploader->deleteFile($cover);

            $book->setCover(null);
            $this->entityManager->flush();
        }
    }

    public function deleteFile(Book $book)
    {
        $file = $book->getFile();

        if ($file) {
            $fileUploader = $this->configureFileUploader($book);
            $fileUploader->deleteFile($file);

            $book->setFile(null);
            $this->entityManager->flush();
        }
    }

    private function uploadFiles(Book $book, ?UploadedFile $fileCover = null, ?UploadedFile $fileBook = null): void
    {
        if (!$fileCover && !$fileBook) {
            return;
        }

        $fileUploader = $this->configureFileUploader($book);

        if ($fileCover) {
            $oldCover = $book->getCover();

            if ($oldCover) {
                $fileUploader->deleteFile($oldCover);
            }

            $cover = $fileUploader->upload($fileCover);
            $book->setCover($cover);
        }

        if ($fileBook) {
            $oldFile = $book->getFile();

            if ($oldFile) {
                $fileUploader->deleteFile($oldFile);
            }

            $file = $fileUploader->upload($fileBook);
            $book->setFile($file);
        }
    }

    private function configureFileUploader(Book $book): FileUploader
    {
        $id = $book->getId();

        if (!$id) {
            throw new \Exception("Empty book id");
        }

        $this->fileUploader->setDirUpload("$this->dirUpload/book/$id");
        return $this->fileUploader;
    }
}
