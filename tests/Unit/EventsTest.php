<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Channel;
use Duyler\Parallel\Contract\EventsInterface;
use Duyler\Parallel\Events;
use Duyler\Parallel\Events\Event;
use Duyler\Parallel\Events\Type;
use Duyler\Parallel\Test\RuntimeTestHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EventsTest extends TestCase
{
    use RuntimeTestHelper;
    #[Test]
    public function create_events(): void
    {
        $events = new Events();

        $this->assertInstanceOf(Events::class, $events);
        $this->assertInstanceOf(EventsInterface::class, $events);
    }

    #[Test]
    public function add_future_to_events(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $events = new Events();
        $events->addFuture('test', $future);

        $event = $events->poll();

        $this->assertInstanceOf(Event::class, $event);
    }

    #[Test]
    public function add_channel_to_events(): void
    {
        $channel = Channel::createBuffered(1);
        $channel->send(1);

        $events = new Events();
        $events->addChannel($channel);

        $event = $events->poll();

        $this->assertInstanceOf(Event::class, $event);
    }

    #[Test]
    public function set_blocking_mode(): void
    {
        $events = new Events();
        $events->setBlocking(true);

        $this->assertTrue(true);
    }

    #[Test]
    public function set_non_blocking_mode(): void
    {
        $events = new Events();
        $events->setBlocking(false);

        $this->assertTrue(true);
    }

    #[Test]
    public function set_timeout(): void
    {
        $events = new Events();
        $events->setTimeout(1000);

        $this->assertTrue(true);
    }

    #[Test]
    public function poll_returns_event_when_future_ready(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 42;
        });

        $events = new Events();
        $events->addFuture('test', $future);

        $event = $events->poll();

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('test', $event->source);
    }

    #[Test]
    public function poll_returns_null_when_no_events_non_blocking(): void
    {
        $events = new Events();
        $events->setBlocking(false);

        $event = $events->poll();

        $this->assertNull($event);
    }

    #[Test]
    public function poll_returns_read_event_for_channel(): void
    {
        $channel = Channel::createBuffered(1);
        $channel->send(42);

        $events = new Events();
        $events->addChannel($channel);

        $event = $events->poll();

        $this->assertEquals(Type::Read, $event->type);
    }

    #[Test]
    public function remove_future_from_events(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            sleep(10);
            return 1;
        });

        $events = new Events();
        $events->addFuture('test', $future);
        $events->remove('test');

        $events->setBlocking(false);
        $event = $events->poll();

        $this->assertNull($event);
    }

    #[Test]
    public function event_has_correct_type(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $events = new Events();
        $events->addFuture('test', $future);

        $event = $events->poll();

        $this->assertInstanceOf(Type::class, $event->type);
    }

    #[Test]
    public function event_has_correct_source(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 1;
        });

        $events = new Events();
        $events->addFuture('test_name', $future);

        $event = $events->poll();

        $this->assertEquals('test_name', $event->source);
    }

    #[Test]
    public function event_has_correct_value(): void
    {
        $runtime = $this->createRuntime();
        $future = $runtime->run(function () {
            return 42;
        });

        $events = new Events();
        $events->addFuture('test', $future);

        $event = $events->poll();

        $this->assertEquals(42, $event->value);
    }
}
