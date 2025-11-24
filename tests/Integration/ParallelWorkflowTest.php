<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Integration;

use Duyler\Parallel\Channel;
use Duyler\Parallel\Events;
use Duyler\Parallel\Runtime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParallelWorkflowTest extends TestCase
{
    #[Test]
    public function complete_workflow_with_runtime_and_future(): void
    {
        $runtime = new Runtime();

        $future1 = $runtime->run(function () {
            return 1 + 1;
        });

        $future2 = $runtime->run(function () {
            return 2 * 2;
        });

        $this->assertEquals(2, $future1->value());
        $this->assertEquals(4, $future2->value());

        $runtime->close();
    }

    #[Test]
    public function event_loop_with_multiple_futures(): void
    {
        $runtime1 = new Runtime();
        $runtime2 = new Runtime();

        $future1 = $runtime1->run(function () {
            return 'task1';
        });

        $future2 = $runtime2->run(function () {
            return 'task2';
        });

        $events = new Events();
        $events->addFuture('f1', $future1);
        $events->addFuture('f2', $future2);

        $results = [];
        while (count($results) < 2) {
            $event = $events->poll();
            if ($event !== null) {
                $results[$event->source] = $event->value;
            }
        }

        $this->assertEquals('task1', $results['f1']);
        $this->assertEquals('task2', $results['f2']);
    }

    #[Test]
    public function named_channels_between_tasks(): void
    {
        $channelName = 'test_channel_' . uniqid();
        $channel = Channel::make($channelName, 10);

        $runtime = new Runtime();
        $future = $runtime->run(function ($name) {
            $ch = \parallel\Channel::open($name);
            $value = $ch->recv();
            $ch->send($value * 2);
        }, [$channelName]);

        $channel->send(21);
        $result = $channel->recv();

        $this->assertEquals(42, $result);
    }

    #[Test]
    public function multiple_tasks_with_same_runtime(): void
    {
        $runtime = new Runtime();

        $futures = [];
        for ($i = 1; $i <= 5; $i++) {
            $futures[] = $runtime->run(function ($num) {
                return $num * $num;
            }, [$i]);
        }

        $results = array_map(fn($future) => $future->value(), $futures);

        $this->assertEquals([1, 4, 9, 16, 25], $results);
    }
}
