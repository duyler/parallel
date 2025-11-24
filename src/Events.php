<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Contract\EventsInterface;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Events\Event;
use Duyler\Parallel\Exception\IllegalValueException;
use Duyler\Parallel\Exception\TimeoutException;
use Override;

final class Events extends ParallelWrapper implements EventsInterface
{
    public const int EVENT_READ = 1;
    public const int EVENT_WRITE = 2;
    public const int EVENT_CLOSE = 3;
    public const int EVENT_CANCEL = 4;

    /**
     * @var \parallel\Events
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected object $nativeInstance;

    public function __construct()
    {
        parent::__construct(new \parallel\Events());
    }

    #[Override]
    public function addFuture(string $name, FutureInterface $future): void
    {
        try {
            /** @var \parallel\Future $nativeFuture */
            $nativeFuture = $future->getNative();
            $this->nativeInstance->addFuture($name, $nativeFuture);
        } catch (\parallel\Events\Error\Existence $e) {
            throw IllegalValueException::fromNative($e);
        }
    }

    #[Override]
    public function addChannel(ChannelInterface $channel): void
    {
        try {
            /** @var \parallel\Channel $nativeChannel */
            $nativeChannel = $channel->getNative();
            $this->nativeInstance->addChannel($nativeChannel);
        } catch (\parallel\Events\Error\Existence $e) {
            throw IllegalValueException::fromNative($e);
        }
    }

    #[Override]
    public function remove(string $name): void
    {
        $this->nativeInstance->remove($name);
    }

    #[Override]
    public function setBlocking(bool $blocking): void
    {
        $this->nativeInstance->setBlocking($blocking);
    }

    #[Override]
    public function setTimeout(int $timeout): void
    {
        $this->nativeInstance->setTimeout($timeout);
    }

    #[Override]
    public function poll(): ?Event
    {
        try {
            $nativeEvent = $this->nativeInstance->poll();

            if ($nativeEvent === null) {
                return null;
            }

            return Event::fromNative($nativeEvent);
        } catch (\parallel\Events\Error\Timeout $e) {
            throw TimeoutException::fromNative($e);
        }
    }
}
