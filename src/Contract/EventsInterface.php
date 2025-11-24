<?php

declare(strict_types=1);

namespace Duyler\Parallel\Contract;

use Duyler\Parallel\Events\Event;

interface EventsInterface extends EventsWrapperInterface
{
    public function addFuture(string $name, FutureInterface $future): void;

    public function addChannel(ChannelInterface $channel): void;

    public function remove(string $name): void;

    public function setBlocking(bool $blocking): void;

    public function setTimeout(int $timeout): void;

    public function poll(): ?Event;
}
