<?php

declare(strict_types=1);

namespace App\File\Twig;

use App\Entity\File\File;
use App\File\OptimizeImageUrlGenerator;
use App\File\PublicUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileUrlExtension extends AbstractExtension
{
    private PublicUrlGenerator $publicUrlGenerator;

    private OptimizeImageUrlGenerator $optimizeImageUrlGenerator;

    public function __construct(
        PublicUrlGenerator $publicUrlGenerator,
        OptimizeImageUrlGenerator $optimizeImageUrlGenerator
    ) {
        $this->publicUrlGenerator = $publicUrlGenerator;
        $this->optimizeImageUrlGenerator = $optimizeImageUrlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'file_url',
                fn (File $file): string => $this->publicUrlGenerator->getObjectUrl($file, false)
            ),
            new TwigFunction(
                'optimize_file_url',
                fn (File $file, int $w, int $h): string => $this->optimizeImageUrlGenerator->generate($file, $w, $h)
            ),
        ];
    }
}
