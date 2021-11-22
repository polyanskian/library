<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository, FileUploader $fileUploader): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findByReadingAll(),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);

            $fileUploader->setDirUpload($book->getDirUpload());

            // Cover file upload
            $fileCover = $form->get('cover')->getData();

            if ($fileCover) {
                $coverFileName = $fileUploader->upload($fileCover);
                $book->setCover($coverFileName);
            }

            // Book file upload
            $fileBook = $form->get('file')->getData();

            if ($fileBook) {
                $bookFileName = $fileUploader->upload($fileBook);
                $book->setFile($bookFileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Book $book, Request $request, FileUploader $fileUploader, Filesystem $fs): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileUploader->setDirUpload($book->getDirUpload());

            // Cover file upload
            $fileCover = $form->get('cover')->getData();

            if ($fileCover) {
                if ($book->getCover()) {
                    $pathOldCover = $fileUploader->getPathFile($book->getCover());

                    if ($fs->exists($pathOldCover)) {
                        $fs->remove($pathOldCover);
                    }
                }

                $coverFileName = $fileUploader->upload($fileCover);
                $book->setCover($coverFileName);
            }

            // Book file upload
            $fileBook = $form->get('file')->getData();

            if ($fileBook) {
                if ($book->getFile()) {
                    $pathOldBook = $fileUploader->getPathFile($book->getFile());

                    if ($fs->exists($pathOldBook)) {
                        $fs->remove($pathOldBook);
                    }
                }

                $bookFileName = $fileUploader->upload($fileBook);
                $book->setFile($bookFileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="book_delete", methods={"POST"})
     */
    public function delete(Book $book, Request $request, Filesystem $fs): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $pathDir = "{$this->getParameter('path_dir_upload')}/{$book->getDirUpload()}";
            $fs->remove($pathDir);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete-cover", name="book_delete_cover", methods={"POST"})
     */
    public function deleteCover(Book $book, Request $request, FileUploader $fileUploader): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete_cover-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $cover = $book->getCover();

            if ($cover) {
                $fileUploader->setDirUpload($book->getDirUpload());
                $fileUploader->deleteFile($cover);
                $book->setCover(null);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete-file", name="book_delete_file", methods={"POST"})
     */
    public function deleteFile(Book $book, Request $request, FileUploader $fileUploader): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete_file-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $file = $book->getFile();

            if ($file) {
                $fileUploader->setDirUpload($book->getDirUpload());
                $fileUploader->deleteFile($file);
                $book->setFile(null);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
    }
}
