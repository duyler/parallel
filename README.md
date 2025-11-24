# Parallel Extension Wrapper

A modern, type-safe wrapper for PHP's parallel extension providing a clean object-oriented API for parallel programming.

## Requirements

- PHP 8.4 or higher with ZTS (Zend Thread Safety) enabled
- ext-parallel installed and enabled

## Installation

```bash
composer require duyler/parallel
```

## Features

- Type-safe interfaces for all components
- Exception handling with custom exception hierarchy
- Clean OOP API following SOLID principles
- Full support for Runtime, Future, Channel, and Events
- Comprehensive test coverage

## Quick Start

### Basic Runtime Usage

```php
use Duyler\Parallel\Runtime;

$runtime = new Runtime();

$future = $runtime->run(function () {
    return 42;
});

$result = $future->value();
echo $result; // 42

$runtime->close();
```

### Using the Facade

```php
use Duyler\Parallel\Parallel;

$future = Parallel::run(function ($x, $y) {
    return $x + $y;
}, [10, 20]);

echo $future->value(); // 30
```

### Using Runtime Pool

```php
use Duyler\Parallel\RuntimePool;

$pool = new RuntimePool(maxRuntimes: 4);

$futures = [];
for ($i = 0; $i < 10; $i++) {
    $futures[] = $pool->run(fn($n) => $n * $n, [$i]);
}

foreach ($futures as $future) {
    echo $future->value() . PHP_EOL;
}

$pool->closeAll();
```

### Using Workflow Builder

```php
use Duyler\Parallel\WorkflowBuilder;

$result = (new WorkflowBuilder())
    ->addChannel('input', 10)
    ->addTask('task1', fn($ch) => processTask1($ch), [$inputChannel])
    ->addTask('task2', fn($ch) => processTask2($ch), [$inputChannel])
    ->execute();

$results = $result->waitAll();
$result->closeAll();
```

### Working with Channels

```php
use Duyler\Parallel\Channel;
use Duyler\Parallel\Runtime;

$channel = Channel::createBuffered(10);

$runtime = new Runtime();
$future = $runtime->run(function ($ch) {
    for ($i = 0; $i < 5; $i++) {
        $ch->send($i * $i);
    }
}, [$channel]);

for ($i = 0; $i < 5; $i++) {
    echo $channel->recv() . PHP_EOL;
}

$runtime->close();
```

### Event Loop

```php
use Duyler\Parallel\Events;
use Duyler\Parallel\Runtime;

$runtime1 = new Runtime();
$runtime2 = new Runtime();

$future1 = $runtime1->run(function () {
    return 'task1';
});

$future2 = $runtime2->run(function () {
    return 'task2';
});

$events = new Events();
$events->addFuture('f1', $future1);
$events->addFuture('f2', $future2);

while ($event = $events->poll()) {
    echo "Task {$event->source} completed with: {$event->value}" . PHP_EOL;
}
```

### Named Channels

```php
use Duyler\Parallel\Channel;

$channel1 = Channel::make('my-channel', 10);
$channel2 = Channel::open('my-channel');

$channel1->send('Hello from channel 1');
echo $channel2->recv(); // Hello from channel 1
```

## API Documentation

### Runtime

Creates and manages a parallel execution context.

```php
$runtime = new Runtime(?string $bootstrap = null);
$future = $runtime->run(Closure $task, array $argv = []): FutureInterface;
$runtime->close(): void;
$runtime->kill(): void;
```

### RuntimePool

Manages a pool of Runtime instances for better performance.

```php
$pool = new RuntimePool(int $maxRuntimes = 4, ?string $bootstrap = null);
$future = $pool->run(Closure $task, array $argv = []): FutureInterface;
$pool->closeAll(): void;
$pool->killAll(): void;
$pool->getSize(): int;
$pool->getMaxSize(): int;
```

### WorkflowBuilder

Builds complex workflows with multiple tasks and channels.

```php
$builder = new WorkflowBuilder();
$builder->withBootstrap(string $bootstrap): WorkflowBuilder;
$builder->withRuntime(RuntimeInterface $runtime): WorkflowBuilder;
$builder->addTask(string $name, Closure $task, array $argv = []): WorkflowBuilder;
$builder->addChannel(string $name, int $capacity = 0): WorkflowBuilder;
$result = $builder->execute(): WorkflowResult;

// WorkflowResult methods
$result->getFuture(string $name): ?FutureInterface;
$result->getChannel(string $name): ?ChannelInterface;
$result->getFutures(): array;
$result->getChannels(): array;
$result->waitAll(): array;
$result->closeAll(): void;
```

### Future

Represents the result of a parallel task.

```php
$value = $future->value(): mixed;
$isDone = $future->done(): bool;
$isCancelled = $future->cancelled(): bool;
$cancelled = $future->cancel(): bool;
```

### Channel

Bidirectional communication channel between tasks.

```php
$channel = Channel::create(): ChannelInterface;
$channel = Channel::createBuffered(int $capacity): ChannelInterface;
$channel = Channel::make(string $name, int $capacity = 0): ChannelInterface;
$channel = Channel::open(string $name): ChannelInterface;

$channel->send(mixed $value): void;
$value = $channel->recv(): mixed;
$channel->close(): void;
```

### Events

Event loop for monitoring multiple Futures and Channels.

```php
$events = new Events();
$events->addFuture(string $name, FutureInterface $future): void;
$events->addChannel(ChannelInterface $channel): void;
$events->remove(string $name): void;
$events->setBlocking(bool $blocking): void;
$events->setTimeout(int $timeout): void;
$event = $events->poll(): ?Event;
```

### Event

Result from Events::poll().

```php
$event->type: Type; // Read, Write, Close, Cancel, Kill, Error
$event->source: string;
$event->object: object; // Future or Channel
$event->value: mixed;
```

## Exception Handling

All exceptions from ext-parallel are wrapped in custom exception classes:

- `ParallelException` - Base exception class
- `CancellationException` - Task was cancelled
- `ClosedException` - Runtime or Channel was closed
- `ForeignException` - Exception thrown in parallel task
- `IllegalValueException` - Invalid or non-serializable value
- `TimeoutException` - Operation timed out
- `BootstrapException` - Bootstrap file error

```php
use Duyler\Parallel\Exception\ForeignException;
use Duyler\Parallel\Runtime;

try {
    $runtime = new Runtime();
    $future = $runtime->run(function () {
        throw new \Exception('Error in task');
    });
    $future->value();
} catch (ForeignException $e) {
    echo "Task failed: " . $e->getMessage();
}
```

## Type Safety

All components implement interfaces for better type safety and testability:

- `RuntimeInterface`
- `FutureInterface`
- `ChannelInterface`
- `EventsInterface`
- `WrapperInterface`

```php
use Duyler\Parallel\Contract\RuntimeInterface;

function processTask(RuntimeInterface $runtime): void {
    $future = $runtime->run(function () {
        return 'result';
    });
    echo $future->value();
}
```

## Testing

Run tests using PHPUnit:

```bash
vendor/bin/phpunit
```

Run static analysis:

```bash
vendor/bin/psalm
```

Run code style fixer:

```bash
vendor/bin/php-cs-fixer fix
```

## Important Notes

1. PHP must be compiled with ZTS (Zend Thread Safety) support
2. ext-parallel must be installed and enabled
3. Closures passed to parallel tasks cannot use yield, declare classes, or declare named functions
4. Values passed between tasks must be serializable
5. Always close Runtime instances when done to free resources

## Checking ZTS Support

```bash
php -v | grep ZTS
```

If ZTS is not shown, you need to recompile PHP with the `--enable-zts` flag.

## License

MIT License. See LICENSE file for details.

## Links

- Documentation: https://duyler.org/en/docs/parallel/
- ext-parallel on PECL: https://pecl.php.net/package/parallel
- ext-parallel on GitHub: https://github.com/krakjoe/parallel
