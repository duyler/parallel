<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\Channel;
use Duyler\Parallel\Runtime;

echo "Channel Communication Example\n";
echo "=============================\n\n";

$channel = Channel::createBuffered(10);

$runtime = new Runtime();
$future = $runtime->run(function ($ch) {
    for ($i = 1; $i <= 5; $i++) {
        $ch->send($i * $i);
        echo "Sent: " . ($i * $i) . "\n";
    }
}, [$channel]);

for ($i = 0; $i < 5; $i++) {
    $value = $channel->recv();
    echo "Received: {$value}\n";
}

$future->value();
$runtime->close();

echo "\nDone!\n";
