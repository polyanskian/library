<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;

class BookDto
{
    public ?int $id = null;
    public string $name = '';
    public string $author = '';
    public ?string $cover = null;
    public ?string $file = null;
    public ?DateTimeInterface $dateRead = null;
    public bool $isDownload = false;

    public function __construct(array $data = [])
    {
        foreach ($data as $name => $val) {
            if (property_exists($this, $name)) {
                $this->$name = $val;
            }
        }
    }
}
