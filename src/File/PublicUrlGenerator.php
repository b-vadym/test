<?php

declare(strict_types=1);

namespace App\File;

use App\Entity\File\File;
use Aws\S3\S3Client;
use League\Flysystem\PathPrefixer;
use Symfony\Component\HttpFoundation\HeaderUtils;

class PublicUrlGenerator
{
    private S3Client $client;

    private string $bucket;

    private PathPrefixer $pathPrefixer;

    public function __construct(S3Client $client, string $bucket, PathPrefixer $pathPrefixer)
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->pathPrefixer = $pathPrefixer;
    }

    public function getObjectUrl(File $file, bool $forceDownload): string
    {
        return $this->generateUrl(
            $file->getPath(),
            $file->getMimeType(),
            $file->getUuid(),
            $file->getExtension(),
            $forceDownload,
            false
        );
    }

    public function generateUrl(
        string $path,
        string $mimeType,
        string $name,
        string $extension,
        bool $forceDownload,
        bool $presigned = false
    ): string {
        $args = [
            'Bucket' => $this->bucket,
            'Key' => $this->pathPrefixer->prefixPath($path),
            'ResponseContentType' => $mimeType,
        ];

        if ($forceDownload) {
            $args['ResponseContentDisposition'] = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $name . '.' . $extension
            );
        }

        $command = $this->client->getCommand('GetObject', $args);

        if ($presigned) {
            return (string) $this->client->createPresignedRequest($command, '+5 minutes')->getUri();
        }

        return (string) \Aws\serialize($command)->getUri();
    }
}
