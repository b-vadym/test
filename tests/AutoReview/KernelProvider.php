<?php

declare(strict_types=1);

namespace App\Tests\AutoReview;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 */
class KernelProvider extends KernelTestCase
{
    public function getLogDir(): string
    {
        $logDir = $this->createKernel()->getLogDir();

        $this->tearDown();

        return $logDir;
    }

    public function getRouter(): RouterInterface
    {
        $this->bootKernel();
        $router = self::getContainer()->get(RouterInterface::class);

        return $router;
    }
}
