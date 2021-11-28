<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use JMS\Serializer\Annotation as JMS;

class BookSerializeDto
{
    /**
     * @JMS\Exclude
     */
    private string $urlUpload;

    public ?int $id = null;
    public string $name = '';
    public string $author = '';

    /**
     * @JMS\AccessType("public_method")
     */
    public ?string $cover = null;

    /**
     * @JMS\AccessType("public_method")
     */
    public ?string $file = null;

    public ?DateTimeInterface $dateRead = null;
    public bool $isDownload = false;

    public function __construct(array $data = [], string $urlUpload = '')
    {
        foreach ($data as $name => $val) {
            if (property_exists($this, $name)) {
                $this->$name = $val;
            }
        }

        $this->urlUpload = $urlUpload;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('author', new NotBlank());
    }

    public function getCover(): ?string
    {
        return ($this->cover) ? "$this->urlUpload/$this->cover" : '';
    }

    public function setCover(?string $cover): void
    {
        $this->cover = null;
    }

    public function getFile(): ?string
    {
        return ($this->file && $this->isDownload) ? "$this->urlUpload/$this->file" : '';
    }

    public function setFile(?string $file): void
    {
        $this->file = null;
    }
}
