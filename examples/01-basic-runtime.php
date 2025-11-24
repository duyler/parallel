<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\Runtime;

echo "Basic Runtime Example\n";
echo "====================\n\n";

$runtime = new Runtime();

$future1 = $runtime->run(function () {
    return 'Hello from parallel task!';
});

$future2 = $runtime->run(function ($x, $y) {
    return $x + $y;
}, [10, 20]);

echo "Task 1 result: " . $future1->value() . "\n";
echo "Task 2 result: " . $future2->value() . "\n";

$runtime->close();

echo "\nDone!\n";
