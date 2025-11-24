<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\WorkflowBuilder;
use Duyler\Parallel\WorkflowResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WorkflowBuilderTest extends TestCase
{
    #[Test]
    public function create_builder(): void
    {
        $builder = new WorkflowBuilder();

        $this->assertInstanceOf(WorkflowBuilder::class, $builder);
    }

    #[Test]
    public function build_workflow_with_single_task(): void
    {
        $result = (new WorkflowBuilder())
            ->addTask('task1', fn() => 42)
            ->execute();

        $this->assertInstanceOf(WorkflowResult::class, $result);
        $this->assertEquals(42, $result->getFuture('task1')?->value());
    }

    #[Test]
    public function build_workflow_with_multiple_tasks(): void
    {
        $result = (new WorkflowBuilder())
            ->addTask('sum', fn($a, $b) => $a + $b, [10, 20])
            ->addTask('mul', fn($x, $y) => $x * $y, [5, 6])
            ->execute();

        $this->assertEquals(30, $result->getFuture('sum')?->value());
        $this->assertEquals(30, $result->getFuture('mul')?->value());
    }

    #[Test]
    public function build_workflow_with_channels(): void
    {
        $result = (new WorkflowBuilder())
            ->addChannel('input', 10)
            ->addChannel('output', 10)
            ->execute();

        $inputChannel = $result->getChannel('input');
        $outputChannel = $result->getChannel('output');

        $this->assertNotNull($inputChannel);
        $this->assertNotNull($outputChannel);

        $inputChannel->send('test');
        $this->assertEquals('test', $inputChannel->recv());
    }

    #[Test]
    public function build_workflow_with_bootstrap(): void
    {
        $bootstrap = tempnam(sys_get_temp_dir(), 'bootstrap');
        file_put_contents($bootstrap, '<?php // test bootstrap');

        $result = (new WorkflowBuilder())
            ->withBootstrap($bootstrap)
            ->addTask('task1', fn() => 'test')
            ->execute();

        $this->assertEquals('test', $result->getFuture('task1')?->value());

        $result->closeAll();
        unlink($bootstrap);
    }

    #[Test]
    public function wait_all_returns_all_results(): void
    {
        $result = (new WorkflowBuilder())
            ->addTask('task1', fn() => 1)
            ->addTask('task2', fn() => 2)
            ->addTask('task3', fn() => 3)
            ->execute();

        $results = $result->waitAll();

        $this->assertEquals([
            'task1' => 1,
            'task2' => 2,
            'task3' => 3,
        ], $results);
    }

    #[Test]
    public function get_all_futures(): void
    {
        $result = (new WorkflowBuilder())
            ->addTask('task1', fn() => 1)
            ->addTask('task2', fn() => 2)
            ->execute();

        $futures = $result->getFutures();

        $this->assertCount(2, $futures);
        $this->assertArrayHasKey('task1', $futures);
        $this->assertArrayHasKey('task2', $futures);
    }

    #[Test]
    public function get_all_channels(): void
    {
        $result = (new WorkflowBuilder())
            ->addChannel('ch1', 5)
            ->addChannel('ch2', 10)
            ->execute();

        $channels = $result->getChannels();

        $this->assertCount(2, $channels);
        $this->assertArrayHasKey('ch1', $channels);
        $this->assertArrayHasKey('ch2', $channels);
    }

    #[Test]
    public function close_all_resources(): void
    {
        $result = (new WorkflowBuilder())
            ->addTask('task1', fn() => 1)
            ->addChannel('ch1', 5)
            ->execute();

        $result->getFuture('task1')?->value();
        $result->closeAll();

        $this->assertTrue(true);
    }
}
