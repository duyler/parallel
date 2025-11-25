<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Contract\EventsInterface;
use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;
use Duyler\Parallel\Parallel;
use Duyler\Parallel\Test\RuntimeTestHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParallelTest extends TestCase
{
    use RuntimeTestHelper;

    protected function setUp(): void
    {
        Parallel::setDefaultBootstrap($this->getBootstrapPath());
    }

    protected function tearDown(): void
    {
        Parallel::closePool();
        Parallel::setDefaultBootstrap(null);
    }

    #[Test]
    public function runtime_creates_new_runtime(): void
    {
        $runtime = Parallel::runtime();

        $this->assertInstanceOf(RuntimeInterface::class, $runtime);
    }

    #[Test]
    public function channel_creates_new_unbuffered_channel(): void
    {
        $channel = Parallel::channel();

        $this->assertInstanceOf(ChannelInterface::class, $channel);
    }

    #[Test]
    public function channel_creates_buffered_channel(): void
    {
        $channel = Parallel::channel(10);

        $this->assertInstanceOf(ChannelInterface::class, $channel);
    }

    #[Test]
    public function events_creates_new_events(): void
    {
        $events = Parallel::events();

        $this->assertInstanceOf(EventsInterface::class, $events);
    }

    #[Test]
    public function run_executes_task(): void
    {
        $future = Parallel::run(function () {
            return 100;
        });

        $this->assertInstanceOf(FutureInterface::class, $future);
        $this->assertEquals(100, $future->value());
    }

    #[Test]
    public function run_executes_task_with_arguments(): void
    {
        $future = Parallel::run(function ($x, $y) {
            return $x * $y;
        }, [5, 6]);

        $this->assertEquals(30, $future->value());
    }
}
