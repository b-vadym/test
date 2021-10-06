<?php

declare(strict_types=1);

namespace App\Entity\File;

use App\Entity\User;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(repositoryClass: FileRepository::class)]
/**
 * @ORM\Entity(repositoryClass="App\Repository\File\FileRepository")
 */
class File
{
    public const TYPE_AVATAR = 'type_avatar';
    public const AVAILABLE_TYPES = [
        self::TYPE_AVATAR,
    ];

    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
    ];

    public const VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/quicktime',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $uuid;

    #[ORM\Column(type: 'text')]
    private string $path;

    #[ORM\Column(type: 'string', length: 255)]
    private string $mimeType;

    /**
     * @psalm-var value-of<self::AVAILABLE_TYPES>
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $owner;

    #[ORM\Column(type: 'string', length: 255)]
    private string $extension;

    /**
     * @psalm-param value-of<self::AVAILABLE_TYPES> $type
     */
    public function __construct(
        string $uuid,
        User $owner,
        string $path,
        string $mimeType,
        string $type,
        string $extension,
    ) {
        $this->uuid = $uuid;
        $this->mimeType = $mimeType;
        $this->type = $type;
        $this->path = $path;
        $this->owner = $owner;
        $this->extension = $extension;
    }

    public function getId(): int
    {
        Assert::notNull($this->id);

        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @psalm-return value-of<self::AVAILABLE_TYPES>
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
