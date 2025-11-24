<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;
use Duyler\Parallel\Exception\ClosedException;
use Duyler\Parallel\Runtime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RuntimeTest extends TestCase
{
    #[Test]
    public function create_runtime_without_bootstrap(): void
    {
        $runtime = new Runtime();

        $this->assertInstanceOf(Runtime::class, $runtime);
        $this->assertInstanceOf(RuntimeInterface::class, $runtime);
    }

    #[Test]
    public function run_simple_task(): void
    {
        $runtime = new Runtime();

        $future = $runtime->run(function () {
            return 42;
        });

        $this->assertInstanceOf(FutureInterface::class, $future);
        $this->assertEquals(42, $future->value());
    }

    #[Test]
    public function run_task_with_arguments(): void
    {
        $runtime = new Runtime();

        $future = $runtime->run(function ($a, $b) {
            return $a + $b;
        }, [10, 20]);

        $this->assertEquals(30, $future->value());
    }

    #[Test]
    public function run_task_with_multiple_arguments(): void
    {
        $runtime = new Runtime();

        $future = $runtime->run(function ($str, $repeat) {
            return str_repeat($str, $repeat);
        }, ['test', 3]);

        $this->assertEquals('testtesttest', $future->value());
    }

    #[Test]
    public function close_runtime(): void
    {
        $runtime = new Runtime();
        $runtime->close();

        $this->expectException(ClosedException::class);
        $runtime->run(function () {
            return 1;
        });
    }

    #[Test]
    public function kill_runtime(): void
    {
        $runtime = new Runtime();
        $runtime->kill();

        $this->expectException(ClosedException::class);
        $runtime->run(function () {
            return 1;
        });
    }

    #[Test]
    public function get_native_instance(): void
    {
        $runtime = new Runtime();

        $native = $runtime->getNative();

        $this->assertInstanceOf(\parallel\Runtime::class, $native);
    }
}
