<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\File\File;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserFixtures extends Fixture
{
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    private FileFactory $fileFactory;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory, FileFactory $fileFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
        $this->fileFactory = $fileFactory;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'v.bondarenko2991+testuser1@gmail.com',
            ['ROLE_USER'],
            $this->passwordHasherFactory->getPasswordHasher(User::class)->hash('pass1234'),
        );
        $manager->persist($user);
        $file = $this->fileFactory->create(
            $user,
            File::TYPE_AVATAR,
            'avatar.jpg',
            'bd4cfb5b-978c-46d9-9af1-0387ba99cc54',
        );
        $manager->persist($file);

        $manager->flush();
    }
}
