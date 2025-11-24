<?php

declare(strict_types=1);

namespace Duyler\Parallel\Contract;

use Closure;

interface RuntimeInterface extends RuntimeWrapperInterface
{
    public function run(Closure $task, array $argv = []): FutureInterface;

    public function close(): void;

    public function kill(): void;
}
