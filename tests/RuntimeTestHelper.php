<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test;

use Duyler\Parallel\Runtime;
use Duyler\Parallel\RuntimePool;

trait RuntimeTestHelper
{
    protected function createRuntime(): Runtime
    {
        return new Runtime(__DIR__ . '/bootstrap.php');
    }

    protected function createRuntimePool(int $maxRuntimes = 4): RuntimePool
    {
        return new RuntimePool($maxRuntimes, __DIR__ . '/bootstrap.php');
    }

    protected function getBootstrapPath(): string
    {
        return __DIR__ . '/bootstrap.php';
    }
}
