<?php

namespace App\Controller;

use App\Dto\BookSerializeDto;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/api/v1/books")
 */
class BookApiController extends AbstractController implements ApiAuthenticatedControllerInterface
{
    private SerializerInterface $serializer;
    private BookService $bookService;

    public function __construct(BookService $bookService, SerializerInterface $serializer)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="api_v1_book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findByReadingAll();

        $dataBooks = array_map(function (Book $book) {
            $urlUpload = $this->getParameter('url.upload');
            return new BookSerializeDto($book->toArray(), $urlUpload);
        }, $books);

        $json = $this->serializer->serialize($dataBooks, 'json');
        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/add", name="api_v1_book_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        /**
         * @var $dto BookSerializeDto
         */
        $dto = $this->serializer->deserialize($request->getContent(), BookSerializeDto::class, 'json');

        $book = (new Book())
            ->setName($dto->name)
            ->setAuthor($dto->author)
            ->setDateRead($dto->dateRead)
            ->setIsDownload($dto->isDownload)
        ;

        $this->bookService->add($book);

        $urlUpload = $this->getParameter('url.upload');
        $json = $this->serializer->serialize(new BookSerializeDto($book->toArray(), $urlUpload), 'json');
        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/{id}/edit", name="api_v1_book_new", methods={"POST"})
     */
    public function edit(Request $request, BookRepository $bookRepository): Response
    {
        /**
         * @var $dto BookSerializeDto
         */
        $dto = $this->serializer->deserialize($request->getContent(), BookSerializeDto::class, 'json');

        $book = ($dto->id) ? $bookRepository->find($dto->id) : null;

        if (!$book) {
            throw new NotFoundHttpException();
        }

        $book
            ->setName($dto->name)
            ->setAuthor($dto->author)
            ->setDateRead($dto->dateRead)
            ->setIsDownload($dto->isDownload)
        ;

        $this->bookService->edit($book);

        $urlUpload = $this->getParameter('url.upload');
        $json = $this->serializer->serialize(new BookSerializeDto($book->toArray(), $urlUpload), 'json');
        return JsonResponse::fromJsonString($json);
    }
}
