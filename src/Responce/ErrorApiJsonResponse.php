<?php

namespace App\Responce;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorApiJsonResponse extends JsonResponse
{
    public function __construct($data = null, int $status = Response::HTTP_BAD_REQUEST, array $headers = [], bool $json = false)
    {
        $data = [
            'success' => false,
            'message' => $data,
        ];

        parent::__construct($data, $status, $headers, $json);
    }
}
