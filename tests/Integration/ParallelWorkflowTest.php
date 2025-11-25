<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Integration;

use Duyler\Parallel\Channel;
use Duyler\Parallel\Events;
use Duyler\Parallel\Runtime;
use Duyler\Parallel\Test\RuntimeTestHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParallelWorkflowTest extends TestCase
{
    use RuntimeTestHelper;
    #[Test]
    public function complete_workflow_with_runtime_and_future(): void
    {
        $runtime = $this->createRuntime();

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
        $inputChannel = 'input_' . uniqid();
        $outputChannel = 'output_' . uniqid();

        Channel::make($inputChannel, 10);
        Channel::make($outputChannel, 10);

        $runtime = $this->createRuntime();
        $future = $runtime->run(function ($input, $output) {
            $chIn = \parallel\Channel::open($input);
            $chOut = \parallel\Channel::open($output);

            $value = $chIn->recv();
            $chOut->send($value * 2);
        }, [$inputChannel, $outputChannel]);

        $input = Channel::open($inputChannel);
        $output = Channel::open($outputChannel);

        $input->send(21);
        $result = $output->recv();

        $future->value();

        $this->assertEquals(42, $result);
    }

    #[Test]
    public function multiple_tasks_with_same_runtime(): void
    {
        $runtime = $this->createRuntime();

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
