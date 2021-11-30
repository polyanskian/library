<?php

namespace App\Service\ImageResizer;

interface ImageResizerInterface
{
    public function resize(string $pathInput, string $pathOutput, int $width, int $height, bool $enlarge = false): void;
}
