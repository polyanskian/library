<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ImageThumb;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageThumbExtension extends AbstractExtension
{
    private ImageThumb $imageThumb;

    public function __construct(ImageThumb $imageThumb)
    {
        $this->imageThumb = $imageThumb;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('image_thumb', [$this, 'getThumbResize']),
        ];
    }

    public function getThumbResize(string $path, int $width = 0, int $height = 0, bool $enlarge = false): string
    {
        return $this->imageThumb->getResize($path, $width, $height, $enlarge);
    }
}
