<?php

namespace App\Controller;

use App\Repository\BookRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/api/v1")
 */
class BookApiController extends AbstractController implements ApiAuthenticatedControllerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/books", name="api_v1_book_index")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findByReadingAll();
        $json = $this->serializer->serialize($books, 'json');
        return JsonResponse::fromJsonString($json);
    }
}
