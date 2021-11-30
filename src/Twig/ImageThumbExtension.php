<?php

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

    public function getThumbResize($path, $width = 0, $height = 0, $enlarge = false): string
    {
        return $this->imageThumb->getResize($path, $width, $height, $enlarge);
    }
}
