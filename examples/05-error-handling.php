<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\Exception\CancellationException;
use Duyler\Parallel\Exception\ClosedException;
use Duyler\Parallel\Exception\ForeignException;
use Duyler\Parallel\Runtime;

echo "Error Handling Example\n";
echo "======================\n\n";

echo "1. Handling Foreign Exception:\n";
try {
    $runtime = new Runtime();
    $future = $runtime->run(function () {
        throw new \Exception('Error in parallel task');
    });
    $future->value();
} catch (ForeignException $e) {
    echo "Caught: " . $e->getMessage() . "\n\n";
}

echo "2. Handling Cancellation:\n";
try {
    $runtime = new Runtime();
    $future = $runtime->run(function () {
        sleep(10);
        return 'never reached';
    });

    usleep(10000);
    $future->cancel();
    $future->value();
} catch (CancellationException $e) {
    echo "Caught: Task was cancelled\n\n";
}

echo "3. Handling Closed Runtime:\n";
try {
    $runtime = new Runtime();
    $runtime->close();
    $runtime->run(function () {
        return 'never executed';
    });
} catch (ClosedException $e) {
    echo "Caught: " . $e->getMessage() . "\n\n";
}

echo "Done!\n";
