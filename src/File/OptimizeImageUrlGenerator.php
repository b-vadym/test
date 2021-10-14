<?php

declare(strict_types=1);

namespace App\File;

use App\Entity\File\File;
use Imgproxy\UrlBuilder;

class OptimizeImageUrlGenerator
{
    private UrlBuilder $urlBuilder;

    private PublicUrlGenerator $publicUrlGenerator;

    public function __construct(UrlBuilder $urlBuilder, PublicUrlGenerator $publicUrlGenerator)
    {
        $this->urlBuilder = $urlBuilder;
        $this->publicUrlGenerator = $publicUrlGenerator;
    }

    public function generate(File $file, int $w, int $h): string
    {
        $url = $this->publicUrlGenerator->getObjectUrl($file, false);

        return $this->urlBuilder->build($url, $w, $h)->toString();
    }
}
