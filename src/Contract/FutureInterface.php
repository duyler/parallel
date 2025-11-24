<?php

declare(strict_types=1);

namespace Duyler\Parallel\Contract;

interface FutureInterface extends FutureWrapperInterface
{
    public function value(): mixed;

    public function done(): bool;

    public function cancelled(): bool;

    public function cancel(): bool;
}
