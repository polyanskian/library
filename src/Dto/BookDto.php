<?php

declare(strict_types=1);

namespace App\Dto;

use JMS\Serializer\Annotation as JMS;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

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

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('author', new NotBlank());
    }
}
