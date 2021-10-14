<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\File\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function getByUuid(string $uuid): File
    {
        $file = $this->findOneBy(['uuid' => $uuid]);
        Assert::notNull($file);

        return $file;
    }
}
