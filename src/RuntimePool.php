<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Closure;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;

final class RuntimePool
{
    /**
     * @var array<RuntimeInterface>
     */
    private array $runtimes = [];
    private int $nextIndex = 0;

    public function __construct(
        private readonly int $maxRuntimes = 4,
        private readonly ?string $bootstrap = null,
    ) {}

    public function run(Closure $task, array $argv = []): FutureInterface
    {
        $runtime = $this->getRuntime();
        return $runtime->run($task, $argv);
    }

    public function closeAll(): void
    {
        foreach ($this->runtimes as $runtime) {
            $runtime->close();
        }

        $this->runtimes = [];
        $this->nextIndex = 0;
    }

    public function killAll(): void
    {
        foreach ($this->runtimes as $runtime) {
            $runtime->kill();
        }

        $this->runtimes = [];
        $this->nextIndex = 0;
    }

    public function getSize(): int
    {
        return count($this->runtimes);
    }

    public function getMaxSize(): int
    {
        return $this->maxRuntimes;
    }

    private function getRuntime(): RuntimeInterface
    {
        if (count($this->runtimes) < $this->maxRuntimes) {
            $runtime = new Runtime($this->bootstrap);
            $this->runtimes[] = $runtime;
            return $runtime;
        }

        $runtime = $this->runtimes[$this->nextIndex];
        $this->nextIndex = ($this->nextIndex + 1) % $this->maxRuntimes;

        return $runtime;
    }
}
