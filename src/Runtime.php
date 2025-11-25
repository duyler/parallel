<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Closure;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;
use Duyler\Parallel\Exception\BootstrapException;
use Duyler\Parallel\Exception\ClosedException;
use Override;

final class Runtime extends ParallelWrapper implements RuntimeInterface
{
    /**
     * @var \parallel\Runtime
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected object $nativeInstance;

    public function __construct(?string $bootstrap = null)
    {
        try {
            $nativeRuntime = $bootstrap === null
                ? new \parallel\Runtime()
                : new \parallel\Runtime($bootstrap);

            parent::__construct($nativeRuntime);
        } catch (\parallel\Runtime\Error\Bootstrap $e) {
            throw BootstrapException::fromNative($e);
        }
    }

    #[Override]
    public function run(Closure $task, array $argv = []): FutureInterface
    {
        try {
            $future = $this->nativeInstance->run($task, $argv);
            return new Future($future);
        } catch (\parallel\Runtime\Error\Closed $e) {
            throw ClosedException::fromNative($e);
        }
    }

    #[Override]
    public function close(): void
    {
        $this->nativeInstance->close();
    }

    #[Override]
    public function kill(): void
    {
        $this->nativeInstance->kill();
    }
}
