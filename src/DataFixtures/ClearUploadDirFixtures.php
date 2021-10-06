<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;

class ClearUploadDirFixtures extends Fixture
{
    /**
     * @var FilesystemOperator
     */
    private $filesystem;

    public function __construct(FilesystemOperator $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function load(ObjectManager $manager): void
    {
        $this->filesystem->deleteDirectory('public-uploads');
    }
}
