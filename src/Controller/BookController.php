<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\BookDto;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\FlashService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    private BookService $bookService;
    private FlashService $notifier;

    public function __construct(BookService $bookService, FlashService $notifier)
    {
        $this->bookService = $bookService;
        $this->notifier = $notifier;
    }

    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository, FilesystemAdapter $cache): Response
    {
        $cacheKey = $this->getParameter('cache.key.book_index');

        $books = $cache->get($cacheKey, function (ItemInterface $item) use ($bookRepository) {
            $item->expiresAfter(3600);
            $books = $bookRepository->findByReadingAll();

            return array_map(function (Book $book) {
                return new BookDto($book->toArray());
            }, $books);
        });

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dto = new BookDto();
        $form = $this->createForm(BookType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileCover = $form->get('cover')->getData();
            $fileBook = $form->get('file')->getData();

            $book = (new Book())
                ->setName($dto->name)
                ->setAuthor($dto->author)
                ->setDateRead($dto->dateRead)
                ->setIsDownload($dto->isDownload)
            ;

            $this->bookService->add($book, $fileCover, $fileBook);

            $this->notifier->ok('Книга добавлена');
            return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/new.html.twig', [
            'book' => $dto,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $dto = new BookDto($book->toArray());
        $form = $this->createForm(BookType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileCover = $form->get('cover')->getData();
            $fileBook = $form->get('file')->getData();

            $book
                ->setName($dto->name)
                ->setAuthor($dto->author)
                ->setDateRead($dto->dateRead)
                ->setIsDownload($dto->isDownload)
            ;

            $this->bookService->edit($book, $fileCover, $fileBook);

            $this->notifier->ok('Изменения сохранены');
            return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/edit.html.twig', [
            'book' => $dto,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $this->bookService->remove($book);
            $this->notifier->ok("Книга удалена `{$book->getName()}`");
        }

        return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete-cover", name="book_delete_cover", methods={"POST"})
     */
    public function deleteCover(Request $request, Book $book): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete_cover-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $this->bookService->deleteCover($book);
            $this->notifier->ok('Обложка удалена');
        }

        return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete-file", name="book_delete_file", methods={"POST"})
     */
    public function deleteFile(Request $request, Book $book): Response
    {
        $token = $request->request->get('_token');
        $tokenKey = "delete_file-{$book->getId()}";

        if ($this->isCsrfTokenValid($tokenKey, $token)) {
            $this->notifier->ok('Файл книги удален');
            $this->bookService->deleteFile($book);
        }

        return $this->redirectToRoute('book_edit', ['id' => $book->getId()], Response::HTTP_SEE_OTHER);
    }
}
