<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Duyler\Parallel\Contract\WrapperInterface;
use Override;

abstract class ParallelWrapper implements WrapperInterface
{
    protected object $nativeInstance;

    public function __construct(object $nativeInstance)
    {
        $this->nativeInstance = $nativeInstance;
    }

    #[Override]
    public function getNative(): object
    {
        return $this->nativeInstance;
    }
}
