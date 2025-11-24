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

    public static function runtime(): RuntimeInterface
    {
        return new Runtime();
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

    public static function pool(int $maxRuntimes = 4): RuntimePool
    {
        return new RuntimePool($maxRuntimes);
    }

    public static function workflow(): WorkflowBuilder
    {
        return new WorkflowBuilder();
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
            self::$pool = new RuntimePool();
        }

        return self::$pool;
    }
}
