<?php

namespace App\Service\ImageResizer;

use Gumlet\ImageResize;
use Symfony\Component\Filesystem\Filesystem;

class ImageResizer implements ImageResizerInterface
{
    private Filesystem $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function resize(string $pathInput, string $pathOutput, int $width, int $height, bool $enlarge = false): void
    {
        $image = new ImageResize($pathInput);
        $image->resizeToBestFit($width, $height, $enlarge);
        $this->fs->mkdir(dirname($pathOutput));
        $image->save($pathOutput);
    }
}
