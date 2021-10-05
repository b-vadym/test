<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Runner\AfterLastTestHook;

class ClearDbDumpListener implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        DBRefresher::dropSnapshot();
    }
}
