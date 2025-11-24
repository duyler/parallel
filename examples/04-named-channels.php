<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duyler\Parallel\Channel;
use Duyler\Parallel\Runtime;

echo "Named Channels Example\n";
echo "======================\n\n";

$channelName = 'example-channel';
$channel1 = Channel::make($channelName, 5);

$runtime = new Runtime();
$future = $runtime->run(function ($name) {
    $ch = \parallel\Channel::open($name);

    for ($i = 1; $i <= 5; $i++) {
        $value = $ch->recv();
        echo "Worker received: {$value}\n";
        $ch->send($value * 2);
    }
}, [$channelName]);

$channel2 = Channel::open($channelName);

for ($i = 1; $i <= 5; $i++) {
    echo "Main sending: {$i}\n";
    $channel2->send($i);

    $result = $channel2->recv();
    echo "Main received: {$result}\n\n";
}

$future->value();
$runtime->close();

echo "Done!\n";
