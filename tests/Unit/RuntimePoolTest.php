<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Contract\FutureInterface;
use Duyler\Parallel\RuntimePool;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RuntimePoolTest extends TestCase
{
    #[Test]
    public function create_pool_with_default_size(): void
    {
        $pool = new RuntimePool();

        $this->assertEquals(4, $pool->getMaxSize());
        $this->assertEquals(0, $pool->getSize());
    }

    #[Test]
    public function create_pool_with_custom_size(): void
    {
        $pool = new RuntimePool(maxRuntimes: 8);

        $this->assertEquals(8, $pool->getMaxSize());
    }

    #[Test]
    public function run_task_creates_runtime(): void
    {
        $pool = new RuntimePool(maxRuntimes: 2);

        $future = $pool->run(function () {
            return 42;
        });

        $this->assertInstanceOf(FutureInterface::class, $future);
        $this->assertEquals(1, $pool->getSize());
        $this->assertEquals(42, $future->value());
    }

    #[Test]
    public function pool_reuses_runtimes_when_full(): void
    {
        $pool = new RuntimePool(maxRuntimes: 2);

        $pool->run(fn() => 1);
        $pool->run(fn() => 2);

        $this->assertEquals(2, $pool->getSize());

        $pool->run(fn() => 3);

        $this->assertEquals(2, $pool->getSize());
    }

    #[Test]
    public function run_multiple_tasks_with_pool(): void
    {
        $pool = new RuntimePool(maxRuntimes: 3);

        $futures = [];
        for ($i = 1; $i <= 5; $i++) {
            $futures[] = $pool->run(function ($num) {
                return $num * $num;
            }, [$i]);
        }

        $results = array_map(fn($future) => $future->value(), $futures);

        $this->assertEquals([1, 4, 9, 16, 25], $results);
        $this->assertEquals(3, $pool->getSize());
    }

    #[Test]
    public function close_all_runtimes(): void
    {
        $pool = new RuntimePool(maxRuntimes: 2);

        $pool->run(fn() => 1);
        $pool->run(fn() => 2);

        $this->assertEquals(2, $pool->getSize());

        $pool->closeAll();

        $this->assertEquals(0, $pool->getSize());
    }

    #[Test]
    public function kill_all_runtimes(): void
    {
        $pool = new RuntimePool(maxRuntimes: 2);

        $pool->run(fn() => 1);

        $this->assertEquals(1, $pool->getSize());

        $pool->killAll();

        $this->assertEquals(0, $pool->getSize());
    }

    #[Test]
    public function pool_with_bootstrap(): void
    {
        $bootstrap = tempnam(sys_get_temp_dir(), 'bootstrap');
        file_put_contents($bootstrap, '<?php // test bootstrap');

        $pool = new RuntimePool(maxRuntimes: 2, bootstrap: $bootstrap);

        $future = $pool->run(fn() => 'test');

        $this->assertEquals('test', $future->value());

        $pool->closeAll();
        unlink($bootstrap);
    }
}
