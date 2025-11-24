<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\Events;
use Duyler\Parallel\Runtime;

echo "Events Loop Example\n";
echo "===================\n\n";

$runtime1 = new Runtime();
$runtime2 = new Runtime();
$runtime3 = new Runtime();

$future1 = $runtime1->run(function () {
    usleep(100000);
    return 'Task 1 completed';
});

$future2 = $runtime2->run(function () {
    usleep(200000);
    return 'Task 2 completed';
});

$future3 = $runtime3->run(function () {
    usleep(150000);
    return 'Task 3 completed';
});

$events = new Events();
$events->addFuture('task1', $future1);
$events->addFuture('task2', $future2);
$events->addFuture('task3', $future3);

$completed = 0;
while ($completed < 3) {
    $event = $events->poll();
    if ($event !== null) {
        echo "Event from {$event->source}: {$event->value}\n";
        $completed++;
    }
}

echo "\nAll tasks completed!\n";
