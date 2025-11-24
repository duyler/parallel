<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Exception\ClosedException;
use Duyler\Parallel\Exception\IllegalValueException;
use InvalidArgumentException;
use Override;

final class Channel extends ParallelWrapper implements ChannelInterface
{
    /**
     * @var \parallel\Channel
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected object $nativeInstance;

    private function __construct(\parallel\Channel $channel)
    {
        parent::__construct($channel);
    }

    public static function create(): self
    {
        return new self(new \parallel\Channel());
    }

    public static function createBuffered(int $capacity): self
    {
        if ($capacity < 1) {
            throw new InvalidArgumentException('Capacity must be positive');
        }

        return new self(new \parallel\Channel($capacity));
    }

    #[Override]
    public function send(mixed $value): void
    {
        try {
            $this->nativeInstance->send($value);
        } catch (\parallel\Channel\Error\Closed $e) {
            throw ClosedException::fromNative($e);
        } catch (\parallel\Channel\Error\IllegalValue $e) {
            throw IllegalValueException::fromNative($e);
        }
    }

    #[Override]
    public function recv(): mixed
    {
        try {
            return $this->nativeInstance->recv();
        } catch (\parallel\Channel\Error\Closed $e) {
            throw ClosedException::fromNative($e);
        }
    }

    #[Override]
    public function close(): void
    {
        $this->nativeInstance->close();
    }

    #[Override]
    public static function make(string $name, int $capacity = 0): ChannelInterface
    {
        try {
            $channel = \parallel\Channel::make($name, $capacity);
            return new Channel($channel);
        } catch (\parallel\Channel\Error\Exists $e) {
            throw IllegalValueException::fromNative($e);
        }
    }

    #[Override]
    public static function open(string $name): ChannelInterface
    {
        try {
            $channel = \parallel\Channel::open($name);
            return new Channel($channel);
        } catch (\parallel\Channel\Error\Existence $e) {
            throw IllegalValueException::fromNative($e);
        }
    }

    public static function fromNative(\parallel\Channel $channel): self
    {
        return new self($channel);
    }
}
