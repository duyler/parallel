<?php

declare(strict_types=1);

namespace Duyler\Parallel\Exception;

use Exception;
use Throwable;

abstract class ParallelException extends Exception
{
    /**
     * @psalm-suppress UnsafeInstantiation
     */
    public static function fromNative(Throwable $e): static
    {
        return new static($e->getMessage(), (int) $e->getCode(), $e);
    }
}
