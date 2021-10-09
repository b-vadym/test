<?php

declare(strict_types=1);

namespace App\File\Twig;

use App\Entity\File\File;
use App\File\PublicUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileUrlExtension extends AbstractExtension
{
    private PublicUrlGenerator $publicUrlGenerator;

    public function __construct(PublicUrlGenerator $publicUrlGenerator)
    {
        $this->publicUrlGenerator = $publicUrlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'file_url',
                fn (File $file): string => $this->publicUrlGenerator->getObjectUrl($file, false)
            ),
        ];
    }
}
