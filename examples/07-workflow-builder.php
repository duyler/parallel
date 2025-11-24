<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\WorkflowBuilder;

echo "Workflow Builder Example\n";
echo "========================\n\n";

echo "Building complex workflow with tasks...\n\n";

$result = (new WorkflowBuilder())
    ->addTask('calculate_sum', function ($a, $b) {
        echo "Task 1: Calculating {$a} + {$b}\n";
        return $a + $b;
    }, [10, 20])
    ->addTask('calculate_product', function ($x, $y) {
        echo "Task 2: Calculating {$x} * {$y}\n";
        return $x * $y;
    }, [5, 6])
    ->addTask('calculate_power', function ($base, $exp) {
        echo "Task 3: Calculating {$base} ^ {$exp}\n";
        return pow($base, $exp);
    }, [2, 10])
    ->execute();

echo "\nWaiting for all tasks to complete...\n\n";

$results = $result->waitAll();

echo "Results:\n";
foreach ($results as $name => $value) {
    echo "- {$name}: {$value}\n";
}

echo "\nClosing runtime...\n";
$result->closeRuntime();

echo "\nDone!\n";
