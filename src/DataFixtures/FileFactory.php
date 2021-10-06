<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\File\File;
use App\Entity\User;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use Symfony\Component\Mime\MimeTypes;
use Webmozart\Assert\Assert;

class FileFactory
{
    private FilesystemOperator $filesystem;

    private MimeTypes $mimeTypes;

    public function __construct(FilesystemOperator $filesystem, MimeTypes $mimeTypes)
    {
        $this->filesystem = $filesystem;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @psalm-param value-of<File::AVAILABLE_TYPES> $type
     */
    public function create(User $owner, string $type, string $filename, string $uuid): File
    {
        $filePath = $this->getFilePath($filename);

        return $this->createFile($owner, $type, $filePath, $uuid);
    }

    /**
     * @psalm-param value-of<File::AVAILABLE_TYPES> $type
     */
    private function createFile(User $owner, string $type, string $filePath, string $uuid): File
    {
        $mimeType = $this->mimeTypes->guessMimeType($filePath);
        Assert::notNull($mimeType);
        $extension = $this->mimeTypes->getExtensions($mimeType)[0];
        $path = "uploads/{$uuid}.{$extension}";
        $this->copyFile($filePath, $path);

        return new File(
            $uuid,
            $owner,
            $path,
            $mimeType,
            $type,
            $extension
        );
    }

    private function copyFile(string $filePath, string $newName): void
    {
        $stream = fopen($filePath, 'rb+');
        Assert::resource($stream);
        $this->filesystem->writeStream($newName, $stream, [
            'visibility' => Visibility::PUBLIC,
        ]);
        fclose($stream);
    }

    private function getFilePath(string $fileName): string
    {
        return __DIR__ . '/files/' . $fileName;
    }
}
