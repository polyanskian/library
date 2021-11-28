<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookService
{
    private EntityManagerInterface $entityManager;
    private BookRepository $repository;
    private FileUploader $fileUploader;
    private Filesystem $fs;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $repository,
        FileUploader $fileUploader,
        Filesystem $fs
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->fileUploader = $fileUploader;
        $this->fs = $fs;
    }

    public function add(Book $book, ?UploadedFile $fileCover = null, ?UploadedFile $fileBook = null): void
    {
        $this->edit($book, $fileCover, $fileBook);
    }

    public function edit(Book $book, ?UploadedFile $fileCover = null, ?UploadedFile $fileBook = null): void
    {
        if ($this->isBookExists($book)) {
            throw new \Exception("Book is exists `name={$book->getName()}, author={$book->getAuthor()}`");
        }

        if ($book->getId() === null) {
            $this->entityManager->persist($book);
        }

        $this->uploadFiles($book, $fileCover, $fileBook);

        $this->entityManager->flush();
    }

    public function remove(Book $book)
    {
        $fileUploader = $this->configureFileUploader($book);
        $dir = $fileUploader->getPathUploadDir();

        if ($this->fs->exists($dir)) {
            $this->fs->remove($dir);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();
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

        $this->fileUploader->setDirUpload("book/$id");
        return $this->fileUploader;
    }

    private function isBookExists(Book $book): bool
    {
        if ($book->getId()) {
            return (bool) $this->repository->findExistsBookNotId($book->getName(), $book->getAuthor(), $book->getId());
        }

        return (bool) $this->repository->findExistsBook($book->getName(), $book->getAuthor());
    }
}
