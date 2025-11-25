<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\Exception\CancellationException;
use Duyler\Parallel\Exception\ForeignException;
use Duyler\Parallel\Future;
use Duyler\Parallel\Test\RuntimeTestHelper;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FutureTest extends TestCase
{
    use RuntimeTestHelper;
    #[Test]
    public function get_value_from_completed_future(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 'result';
        });

        $this->assertEquals('result', $future->value());
    }

    #[Test]
    public function get_array_value_from_future(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return ['key' => 'value'];
        });

        $this->assertEquals(['key' => 'value'], $future->value());
    }

    #[Test]
    public function get_null_value_from_future(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return null;
        });

        $this->assertNull($future->value());
    }

    #[Test]
    public function done_returns_true_for_completed_task(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $future->value();

        $this->assertTrue($future->done());
    }

    #[Test]
    public function cancel_future(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            sleep(10);
            return 1;
        });

        usleep(10000);
        $result = $future->cancel();

        $this->assertTrue($result);
    }

    #[Test]
    public function cancelled_returns_true_after_cancel(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            sleep(10);
            return 1;
        });

        usleep(10000);
        $future->cancel();

        $this->assertTrue($future->cancelled());
    }

    #[Test]
    public function cancelled_returns_false_for_running_task(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $this->assertFalse($future->cancelled());
    }

    #[Test]
    public function value_throws_exception_when_cancelled(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            sleep(10);
            return 1;
        });

        usleep(10000);
        $future->cancel();

        $this->expectException(CancellationException::class);
        $future->value();
    }

    #[Test]
    public function value_propagates_exception_from_task(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            throw new Exception('Task error');
        });

        $this->expectException(ForeignException::class);
        $future->value();
    }

    #[Test]
    public function future_implements_interface(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $this->assertInstanceOf(FutureInterface::class, $future);
        $this->assertInstanceOf(Future::class, $future);
    }
}
