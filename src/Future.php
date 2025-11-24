<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Exception\CancellationException;
use Duyler\Parallel\Exception\ForeignException;
use Override;

final class Future extends ParallelWrapper implements FutureInterface
{
    /**
     * @var \parallel\Future
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected object $nativeInstance;

    public function __construct(\parallel\Future $future)
    {
        parent::__construct($future);
    }

    #[Override]
    public function value(): mixed
    {
        try {
            return $this->nativeInstance->value();
        } catch (\parallel\Future\Error\Cancellation $e) {
            throw CancellationException::fromNative($e);
        } catch (\parallel\Future\Error\Foreign $e) {
            throw ForeignException::fromNative($e);
        } catch (\parallel\Future\Error\Killed $e) {
            throw ForeignException::fromNative($e);
        }
    }

    #[Override]
    public function done(): bool
    {
        return $this->nativeInstance->done();
    }

    #[Override]
    public function cancelled(): bool
    {
        return $this->nativeInstance->cancelled();
    }

    #[Override]
    public function cancel(): bool
    {
        return $this->nativeInstance->cancel();
    }
}
