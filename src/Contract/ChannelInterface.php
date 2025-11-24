<?php

declare(strict_types=1);

namespace Duyler\Parallel\Contract;

interface ChannelInterface extends ChannelWrapperInterface
{
    public function send(mixed $value): void;

    public function recv(): mixed;

    public function close(): void;

    public static function make(string $name, int $capacity = 0): ChannelInterface;

    public static function open(string $name): ChannelInterface;
}
