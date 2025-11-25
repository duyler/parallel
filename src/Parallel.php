<?php

declare(strict_types=1);

namespace Duyler\Parallel;

use Closure;
use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Contract\EventsInterface;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;

final class Parallel
{
    private static ?RuntimePool $pool = null;
    private static ?string $defaultBootstrap = null;

    public static function setDefaultBootstrap(?string $bootstrap): void
    {
        self::$defaultBootstrap = $bootstrap;
    }

    public static function runtime(?string $bootstrap = null): RuntimeInterface
    {
        return new Runtime($bootstrap ?? self::$defaultBootstrap);
    }

    public static function channel(?int $capacity = null): ChannelInterface
    {
        if ($capacity === null) {
            return Channel::create();
        }

        return Channel::createBuffered($capacity);
    }

    public static function events(): EventsInterface
    {
        return new Events();
    }

    public static function pool(int $maxRuntimes = 4, ?string $bootstrap = null): RuntimePool
    {
        return new RuntimePool($maxRuntimes, $bootstrap ?? self::$defaultBootstrap);
    }

    public static function workflow(?string $bootstrap = null): WorkflowBuilder
    {
        $builder = new WorkflowBuilder();

        if ($bootstrap !== null || self::$defaultBootstrap !== null) {
            $builder->withBootstrap($bootstrap ?? self::$defaultBootstrap);
        }

        return $builder;
    }

    public static function run(Closure $task, array $argv = []): FutureInterface
    {
        return self::getPool()->run($task, $argv);
    }

    public static function closePool(): void
    {
        if (self::$pool !== null) {
            self::$pool->closeAll();
            self::$pool = null;
        }
    }

    private static function getPool(): RuntimePool
    {
        if (self::$pool === null) {
            self::$pool = new RuntimePool(4, self::$defaultBootstrap);
        }

        return self::$pool;
    }
}
