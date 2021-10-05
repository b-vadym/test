<?php

declare(strict_types=1);

namespace App\Tests\AutoReview;

use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\AfterTestErrorHook;
use PHPUnit\Runner\AfterTestFailureHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
class NotUsedRoutesExtension implements AfterLastTestHook, BeforeFirstTestHook, AfterTestFailureHook, AfterTestErrorHook
{
    /**
     * @var string
     */
    private $logFileName;

    /**
     * @var KernelProvider
     */
    private $kernelProvider;

    /**
     * @var string[]
     * @psalm-var list<string>
     */
    private $suppressRouteNames;

    /**
     * @var bool
     */
    private $hasFailedTest;

    /**
     * @param string[] $suppressRouteNames
     * @psalm-param list<string> $suppressRouteNames
     */
    public function __construct(string $logFileName, array $suppressRouteNames)
    {
        $this->logFileName = $logFileName;
        /** @psalm-suppress InternalMethod */
        $this->kernelProvider = new KernelProvider();
        $this->suppressRouteNames = $suppressRouteNames;
        $this->hasFailedTest = false;
    }

    public function executeBeforeFirstTest(): void
    {
        if (!$this->shouldDetectUnusedRoutes()) {
            return;
        }

        $logFile = $this->resolveRouterLogFile();

        if (file_exists($logFile)) {
            unlink($logFile);
        }
    }

    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $this->hasFailedTest = true;
    }

    public function executeAfterTestError(string $test, string $message, float $time): void
    {
        $this->hasFailedTest = true;
    }

    public function executeAfterLastTest(): void
    {
        if ($this->hasFailedTest || !$this->shouldDetectUnusedRoutes()) {
            return;
        }

        $usedRoutes = $this->collectUsedRoutes($this->resolveRouterLogFile());
        $availableRoutes = $this->getAllRoutes();

        $rawUnusedRoutes = array_diff($availableRoutes, $usedRoutes);
        $unusedRoutes = array_diff($rawUnusedRoutes, $this->suppressRouteNames);

        $usedIgnoredRoutes = array_intersect($usedRoutes, $this->suppressRouteNames);
        echo sprintf("\nUsed ignored routes %d. \n %s", \count($usedIgnoredRoutes), implode("\n", $usedIgnoredRoutes));

        echo sprintf("\n\nNot used routes: %d\n", \count($unusedRoutes));

        if (\count($unusedRoutes)) {
            echo sprintf(
                "The following routes not matched:\n * %s\n",
                implode("\n * ", $unusedRoutes)
            );

            exit(255);
        }
    }

    private function resolveRouterLogFile(): string
    {
        return $this->kernelProvider->getLogDir() . '/' . $this->logFileName;
    }

    /**
     * @psalm-return list<string>
     */
    private function collectUsedRoutes(string $logFilePath): array
    {
        $usedRoutes = [];

        $resource = fopen($logFilePath, 'rb');
        Assert::resource($resource);

        while (($line = $this->readLine($resource)) !== false) {
            if (preg_match('/request.INFO: Matched route "(.*)"\./', $line, $matches) === 1) {
                $usedRoutes[$matches[1]] = $matches[1];
            }
        }

        return array_values($usedRoutes);
    }

    /**
     * @param resource $resource
     * @return false|string
     */
    private function readLine($resource)
    {
        $line = fgets($resource);

        if ($line === false) {
            return false;
        }

        return rtrim($line, "\n");
    }

    /**
     * @psalm-return list<string>
     */
    private function getAllRoutes(): array
    {
        $router = $this->kernelProvider->getRouter();
        $routeCollection = $router->getRouteCollection();

        return array_keys($routeCollection->all());
    }

    private function shouldDetectUnusedRoutes(): bool
    {
        /**
         * @psalm-suppress PossiblyUndefinedStringArrayOffset
         * @psalm-suppress MixedArgument
         */
        return \in_array('--detect-unused-routes', $_SERVER['argv'], true);
    }
}
