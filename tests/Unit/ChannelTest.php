<?php

declare(strict_types=1);

namespace Duyler\Parallel\Test\Unit;

use Duyler\Parallel\Channel;
use Duyler\Parallel\Contract\ChannelInterface;
use Duyler\Parallel\Exception\ClosedException;
use Duyler\Parallel\Exception\IllegalValueException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ChannelTest extends TestCase
{
    #[Test]
    public function create_unbuffered_channel(): void
    {
        $channel = Channel::create();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertInstanceOf(ChannelInterface::class, $channel);
    }

    #[Test]
    public function create_buffered_channel(): void
    {
        $channel = Channel::createBuffered(10);

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertInstanceOf(ChannelInterface::class, $channel);
    }

    #[Test]
    public function create_buffered_channel_with_invalid_capacity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Channel::createBuffered(0);
    }

    #[Test]
    public function send_and_receive_integer(): void
    {
        $channel = Channel::createBuffered(1);

        $channel->send(42);
        $value = $channel->recv();

        $this->assertEquals(42, $value);
    }

    #[Test]
    public function send_and_receive_string(): void
    {
        $channel = Channel::createBuffered(1);

        $channel->send('test');
        $value = $channel->recv();

        $this->assertEquals('test', $value);
    }

    #[Test]
    public function send_and_receive_array(): void
    {
        $channel = Channel::createBuffered(1);

        $data = ['key' => 'value', 'number' => 123];
        $channel->send($data);
        $value = $channel->recv();

        $this->assertEquals($data, $value);
    }

    #[Test]
    public function send_and_receive_null(): void
    {
        $channel = Channel::createBuffered(1);

        $channel->send(null);
        $value = $channel->recv();

        $this->assertNull($value);
    }

    #[Test]
    public function buffered_channel_accepts_multiple_values(): void
    {
        $channel = Channel::createBuffered(3);

        $channel->send(1);
        $channel->send(2);
        $channel->send(3);

        $this->assertEquals(1, $channel->recv());
        $this->assertEquals(2, $channel->recv());
        $this->assertEquals(3, $channel->recv());
    }

    #[Test]
    public function create_named_channel_with_make(): void
    {
        $name = 'test_channel_' . uniqid();

        $channel = Channel::make($name, 10);

        $this->assertInstanceOf(Channel::class, $channel);
    }

    #[Test]
    public function open_existing_named_channel(): void
    {
        $name = 'test_channel_' . uniqid();

        $channel1 = Channel::make($name, 10);
        $channel2 = Channel::open($name);

        $this->assertInstanceOf(Channel::class, $channel2);
    }

    #[Test]
    public function communicate_via_named_channel(): void
    {
        $name = 'test_channel_' . uniqid();

        $channel1 = Channel::make($name, 1);
        $channel2 = Channel::open($name);

        $channel1->send('hello');
        $value = $channel2->recv();

        $this->assertEquals('hello', $value);
    }

    #[Test]
    public function close_channel(): void
    {
        $channel = Channel::createBuffered(1);
        $channel->close();

        $this->expectException(ClosedException::class);
        $channel->send(1);
    }

    #[Test]
    public function recv_throws_exception_on_closed_channel(): void
    {
        $channel = Channel::createBuffered(1);
        $channel->close();

        $this->expectException(ClosedException::class);
        $channel->recv();
    }

    #[Test]
    public function open_non_existing_channel_throws_exception(): void
    {
        $this->expectException(IllegalValueException::class);
        Channel::open('non_existing_channel_' . uniqid());
    }
}
