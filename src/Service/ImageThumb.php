<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\ImageResizer\ImageResizerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageThumb
{
    private string $pathWeb;
    private string $dirThumb;
    private ImageResizerInterface $resizer;
    private Filesystem $fs;

    public function __construct(
        string $pathWeb,
        string $dirThumb,
        ImageResizerInterface $resizer,
        Filesystem $fs
    ) {
        $this->pathWeb = $pathWeb;
        $this->dirThumb = $dirThumb;
        $this->resizer = $resizer;
        $this->fs = $fs;
    }

    private function getPathThumb(string $pathRelative, array $params): string
    {
        $info = pathinfo($pathRelative);
        $dir = $info['dirname'] ?? '';
        $name = $info['filename'] ?? '';
        $ext = $info['extension'] ?? '';

        $fileName = $this->makeName($name, $params);

        return $this->makePathThumbRelative("$dir/$fileName.$ext");
    }
    
    public function getResize(string $path, int $width = 0, int $height = 0, bool $enlarge = false): string
    {
        $width = abs($width);
        $height = abs($height);

        $pathInput = $this->makePathRelative($path);

        if ($width === 0) {
            $width = $height;
        }

        if ($height === 0) {
            $height = $width;
        }

        if ($width === 0) {
            return $pathInput;
        }

        $pathThumb = $this->getPathThumb($pathInput, [
            'w' => $width,
            'h' => $height,
            'e' => (int) $enlarge
        ]);

        if (!$this->isCache($pathThumb)) {
            $this->resizer->resize("$this->pathWeb/$pathInput", "$this->pathWeb/$pathThumb", $width, $height, $enlarge);
        }

        return $pathThumb;
    }

    private function isCache(string $pathRelative): bool
    {
        return $this->fs->exists("$this->pathWeb/$pathRelative");
    }

    private function makeName(string $name, array $params): string
    {
        ksort($params);

        foreach ($params as $key => $param) {
            $name .= "-{$key}{$param}";
        }

        return $name;
    }

    private function makePathThumbRelative(string $pathRelative): string
    {
        return "$this->dirThumb/$pathRelative";
    }

    private function makePathRelative(string $path): string
    {
        $path = str_replace($this->pathWeb, '', $path);
        return ltrim($path, '/');
    }
}
