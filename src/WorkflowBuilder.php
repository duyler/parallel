<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Closure;
use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;

final class WorkflowBuilder
{
    private ?string $bootstrap = null;

    /**
     * @var array<string, array{task: Closure, argv: array}>
     */
    private array $tasks = [];

    /**
     * @var array<string, int>
     */
    private array $channels = [];

    private ?RuntimeInterface $runtime = null;

    public function withBootstrap(string $bootstrap): self
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    public function withRuntime(RuntimeInterface $runtime): self
    {
        $this->runtime = $runtime;
        return $this;
    }

    public function addTask(string $name, Closure $task, array $argv = []): self
    {
        $this->tasks[$name] = ['task' => $task, 'argv' => $argv];
        return $this;
    }

    public function addChannel(string $name, int $capacity = 0): self
    {
        $this->channels[$name] = $capacity;
        return $this;
    }

    public function execute(): WorkflowResult
    {
        $runtime = $this->runtime ?? new Runtime($this->bootstrap);
        $channels = $this->createChannels();
        $futures = $this->executeTasks($runtime);

        return new WorkflowResult($futures, $channels, $runtime);
    }

    /**
     * @return array<string, ChannelInterface>
     */
    private function createChannels(): array
    {
        $channels = [];

        foreach ($this->channels as $name => $capacity) {
            $channels[$name] = $capacity > 0
                ? Channel::createBuffered($capacity)
                : Channel::create();
        }

        return $channels;
    }

    /**
     * @return array<string, FutureInterface>
     */
    private function executeTasks(RuntimeInterface $runtime): array
    {
        $futures = [];

        foreach ($this->tasks as $name => $config) {
            $futures[$name] = $runtime->run($config['task'], $config['argv']);
        }

        return $futures;
    }
}
