<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\Process\Process;

/**
 * Used to execute external programs.
 */
class ProgramExecutor
{
    /**
     * @var array
     */
    private $commonArgs;

    /**
     * @var float|null
     */
    private $timeout;

    /**
     * @param string ...$commonArgs
     */
    public function __construct(string ...$commonArgs)
    {
        $this->commonArgs = $commonArgs;
        $this->timeout = 60;
    }

    /**
     * @return $this
     */
    public function setTimeout(float $value)
    {
        $this->timeout = $value;

        return $this;
    }

    /**
     * @param string ...$args
     * @return $this
     */
    public function exec(string ...$args): self
    {
        $process = new Process(array_merge($this->commonArgs, $args), null, null, null, $this->timeout);
        $process->mustRun();

        return $this;
    }
}
