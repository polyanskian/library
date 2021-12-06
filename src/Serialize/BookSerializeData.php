<?php

declare(strict_types=1);

namespace App\Serialize;

use DateTimeInterface;
use Exception;
use JMS\Serializer\Annotation as JMS;

class BookSerializeData
{
    /**
     * @JMS\Exclude
     */
    private string $urlUpload;

    /**
     * @JMS\Accessor(setter="setId")
     */
    public ?int $id = null;

    /**
     * @JMS\Accessor(setter="setName")
     */
    public string $name = '';

    /**
     * @JMS\Accessor(setter="setAuthor")
     */
    public string $author = '';

    /**
     * @JMS\AccessType("public_method")
     */
    public ?string $cover = null;

    /**
     * @JMS\AccessType("public_method")
     */
    public ?string $file = null;

    /**
     * @JMS\Type("DateTime<'d.m.Y h:i:s'>")
     * @JMS\Accessor(setter="setDateRead")
     */
    public ?DateTimeInterface $dateRead = null;

    /**
     * @JMS\Accessor(setter="setIsDownload")
     */
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

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        if ($id !== null && !is_int($id)) {
            throw new Exception('Param `Book.id` type not integer');
        }

        $this->id = $id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        if (!is_string($name)) {
            throw new Exception('Param `Book.name` type not string');
        }

        $this->name = $name;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        if (!is_string($author)) {
            throw new Exception('Param `Book.author` type not string');
        }

        $this->author = $author;
    }

    /**
     * @param mixed $cover
     */
    public function setCover($cover): void
    {
        if ($cover !== null && !is_string($cover)) {
            throw new Exception('Param `Book.cover` type not string');
        }

        $this->cover = null;
    }

    public function getCover(): string
    {
        return ($this->cover) ? "$this->urlUpload/$this->cover" : '';
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        if ($file !== null && !is_string($file)) {
            throw new Exception('Param `Book.file` type not string');
        }

        $this->file = null;
    }

    public function getFile(): string
    {
        return ($this->file && $this->isDownload) ? "$this->urlUpload/$this->file" : '';
    }

    /**
     * @param mixed $dateRead
     */
    public function setDateRead($dateRead): void
    {
        if ($dateRead !== null && !($dateRead instanceof DateTimeInterface)) {
            throw new Exception('Param `Book.file` type not string');
        }

        $this->dateRead = $dateRead;
    }

    /**
     * @param mixed $isDownload
     */
    public function setIsDownload($isDownload): void
    {
        if (!is_bool($isDownload)) {
            throw new Exception('Param `Book.is_download` type not boolean');
        }

        $this->isDownload = $isDownload;
    }
}
