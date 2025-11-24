<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;

final readonly class WorkflowResult
{
    /**
     * @param array<string, FutureInterface> $futures
     * @param array<string, ChannelInterface> $channels
     */
    public function __construct(
        private array $futures,
        private array $channels,
        private RuntimeInterface $runtime,
    ) {}

    public function getFuture(string $name): ?FutureInterface
    {
        return $this->futures[$name] ?? null;
    }

    public function getChannel(string $name): ?ChannelInterface
    {
        return $this->channels[$name] ?? null;
    }

    /**
     * @return array<string, FutureInterface>
     */
    public function getFutures(): array
    {
        return $this->futures;
    }

    /**
     * @return array<string, ChannelInterface>
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    public function getRuntime(): RuntimeInterface
    {
        return $this->runtime;
    }

    /**
     * @return array<string, mixed>
     */
    public function waitAll(): array
    {
        $results = [];

        foreach ($this->futures as $name => $future) {
            $results[$name] = $future->value();
        }

        return $results;
    }

    public function closeRuntime(): void
    {
        $this->runtime->close();
    }

    public function closeChannels(): void
    {
        foreach ($this->channels as $channel) {
            $channel->close();
        }
    }

    public function closeAll(): void
    {
        $this->closeChannels();
        $this->closeRuntime();
    }
}
