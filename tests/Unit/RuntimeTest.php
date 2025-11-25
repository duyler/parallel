<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Contract\RuntimeInterface;
use Duyler\Parallel\Exception\ClosedException;
use Duyler\Parallel\Runtime;
use Duyler\Parallel\Test\RuntimeTestHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RuntimeTest extends TestCase
{
    use RuntimeTestHelper;
    #[Test]
    public function create_runtime_without_bootstrap(): void
    {
        $runtime = $this->createRuntime();

        $this->assertInstanceOf(Runtime::class, $runtime);
        $this->assertInstanceOf(RuntimeInterface::class, $runtime);
    }

    #[Test]
    public function run_simple_task(): void
    {
        $runtime = $this->createRuntime();

        $future = $runtime->run(function () {
            return 42;
        });

        $this->assertInstanceOf(FutureInterface::class, $future);
        $this->assertEquals(42, $future->value());
    }

    #[Test]
    public function run_task_with_arguments(): void
    {
        $runtime = $this->createRuntime();

        $future = $runtime->run(function ($a, $b) {
            return $a + $b;
        }, [10, 20]);

        $this->assertEquals(30, $future->value());
    }

    #[Test]
    public function run_task_with_multiple_arguments(): void
    {
        $runtime = $this->createRuntime();

        $future = $runtime->run(function ($str, $repeat) {
            return str_repeat($str, $repeat);
        }, ['test', 3]);

        $this->assertEquals('testtesttest', $future->value());
    }

    #[Test]
    public function close_runtime(): void
    {
        $runtime = $this->createRuntime();
        $runtime->close();

        $this->expectException(ClosedException::class);
        $runtime->run(function () {
            return 1;
        });
    }

    #[Test]
    public function kill_runtime(): void
    {
        $runtime = $this->createRuntime();
        $runtime->kill();

        $this->expectException(ClosedException::class);
        $runtime->run(function () {
            return 1;
        });
    }

    #[Test]
    public function get_native_instance(): void
    {
        $runtime = $this->createRuntime();

        $native = $runtime->getNative();

        $this->assertInstanceOf(\parallel\Runtime::class, $native);
    }
}
