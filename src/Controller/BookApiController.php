<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Responce\ErrorApiJsonResponse;
use App\Serialize\BookSerializeData;
use App\Service\BookService;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route ("/api/v1/books")
 */
class BookApiController extends AbstractController implements ApiAuthenticatedControllerInterface
{
    private SerializerInterface $serializer;
    private BookService $bookService;
    private BookRepository $bookRepository;

    public function __construct(BookService $bookService, SerializerInterface $serializer, BookRepository $bookRepository)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("", name="api_v1_book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findByReadingAll();

        $dataBooks = array_map(function (Book $book) {
            $urlUpload = $this->getParameter('url.web');
            return new BookSerializeData($book->toArray(), $urlUpload);
        }, $books);

        $json = $this->serializer->serialize($dataBooks, 'json');
        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/add", name="api_v1_book_new", methods={"POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        try {
            /**
             * @var BookSerializeData $dto
             */
            $dto = $this->serializer->deserialize($request->getContent(), BookSerializeData::class, 'json');
        } catch (Exception $e) {
            return new ErrorApiJsonResponse($e->getMessage());
        }

        $book = $this->bookService->createBookEntity()
            ->setName($dto->name)
            ->setAuthor($dto->author)
            ->setDateRead($dto->dateRead)
            ->setIsDownload($dto->isDownload);

        $errors = $validator->validate($book);

        if (count($errors)) {
            return $this->json($errors, 422);
        }

        $this->bookService->addBook($book);

        $urlUpload = $this->getParameter('url.web');
        $json = $this->serializer->serialize(new BookSerializeData($book->toArray(), $urlUpload), 'json');
        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/{id}/edit", name="api_v1_book_edit", methods={"POST"})
     */
    public function edit(Request $request, ValidatorInterface $validator): Response
    {
        try {
            /**
             * @var BookSerializeData $dto
             */
            $dto = $this->serializer->deserialize($request->getContent(), BookSerializeData::class, 'json');
        } catch (Exception $e) {
            return new ErrorApiJsonResponse($e->getMessage());
        }

        $book = $this->findBook($dto->id)
            ->setName($dto->name)
            ->setAuthor($dto->author)
            ->setDateRead($dto->dateRead)
            ->setIsDownload($dto->isDownload);

        $errors = $validator->validate($book);

        if (count($errors)) {
            return $this->json($errors, 422);
        }

        $this->bookService->editBook($book);

        $urlUpload = $this->getParameter('url.web');
        $json = $this->serializer->serialize(new BookSerializeData($book->toArray(), $urlUpload), 'json');
        return JsonResponse::fromJsonString($json);
    }

    private function findBook(?int $id): Book
    {
        $book = ($id) ? $this->bookRepository->find($id) : null;

        if (!$book) {
            throw new NotFoundHttpException();
        }

        return $book;
    }
}
