<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\RuntimePool;

echo "Runtime Pool Example\n";
echo "====================\n\n";

$pool = new RuntimePool(maxRuntimes: 3);

echo "Pool max size: {$pool->getMaxSize()}\n";
echo "Pool current size: {$pool->getSize()}\n\n";

echo "Running 5 tasks with pool of 3 runtimes:\n";

$futures = [];
for ($i = 1; $i <= 5; $i++) {
    echo "Starting task {$i}...\n";
    $futures[] = $pool->run(function ($num) {
        return $num * $num;
    }, [$i]);
}

echo "\nPool size after scheduling: {$pool->getSize()}\n\n";

echo "Collecting results:\n";
foreach ($futures as $index => $future) {
    $result = $future->value();
    echo "Task " . ($index + 1) . " result: {$result}\n";
}

echo "\nClosing all runtimes...\n";
$pool->closeAll();
echo "Pool size after closing: {$pool->getSize()}\n";

echo "\nDone!\n";
