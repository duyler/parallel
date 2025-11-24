# Examples

This directory contains examples demonstrating various features of the parallel wrapper.

## Running Examples

All examples require PHP with ZTS enabled and ext-parallel installed.

```bash
php examples/01-basic-runtime.php
php examples/02-channels.php
php examples/03-events.php
php examples/04-named-channels.php
php examples/05-error-handling.php
```

## Examples Overview

### 01-basic-runtime.php

Demonstrates basic Runtime usage:
- Creating a Runtime
- Running simple tasks
- Running tasks with arguments
- Getting results from Futures

### 02-channels.php

Shows how to use Channels for communication:
- Creating buffered channels
- Sending values from parallel task
- Receiving values in main thread

### 03-events.php

Demonstrates the Events loop:
- Adding multiple Futures to Events
- Polling for completion
- Processing results as they arrive

### 04-named-channels.php

Shows named channels usage:
- Creating named channels with make()
- Opening channels in parallel tasks
- Bidirectional communication

### 05-error-handling.php

Demonstrates exception handling:
- Catching ForeignException from parallel tasks
- Handling CancellationException
- Dealing with ClosedException

## Notes

- All closures passed to parallel tasks must be defined in a file (not eval/stdin)
- Values must be serializable when passing between threads
- Always close Runtime instances when done
- Remember to handle exceptions appropriately

