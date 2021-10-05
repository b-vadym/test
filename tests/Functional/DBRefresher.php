<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class DBRefresher
{
    /**
     * @var bool|null
     */
    private static $snapshotTaken;

    public static function refresh(): void
    {
        if (self::$snapshotTaken) {
            (new ProgramExecutor(self::getBinPath('mysql-snapshot')))
                ->setTimeout(180)
                ->exec('restore', 'mysql-snapshot')
            ;
        } else {
            (new ProgramExecutor(PHP_BINARY, self::getBinPath('console')))
                ->setTimeout(180)
                ->exec('cache:warmup')
                ->exec('doctrine:database:create', '--if-not-exists', '-n')
                ->exec('doctrine:schema:drop', '-f', '-n')
                ->exec('doctrine:schema:create', '-n')
                ->exec('doctrine:fixtures:load', '--append')
            ;

//            if (!file_exists(self::getProjectRootPath() . '/public/js/manifest.json')) {
//                (new ProgramExecutor('yarn'))
//                    ->setTimeout(1500.0)
//                    ->exec('build')
//                ;
//            }

            (new ProgramExecutor(self::getBinPath('mysql-snapshot')))
                ->exec('drop', 'mysql-snapshot')
                ->exec('take', 'mysql-snapshot')
            ;

            self::$snapshotTaken = true;
        }
    }

    public static function dropSnapshot(): void
    {
        if (self::$snapshotTaken) {
            (new ProgramExecutor(self::getBinPath('mysql-snapshot')))
                ->exec('drop', 'mysql-snapshot')
            ;
            self::$snapshotTaken = false;
        }
    }

    private static function getProjectRootPath(): string
    {
        return realpath(__DIR__ . '/../..');
    }

    private static function getBinPath(string $binName): string
    {
        return self::getProjectRootPath() . '/bin/' . $binName;
    }
}
