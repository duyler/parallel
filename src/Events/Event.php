<?php

declare(strict_types=1);

namespace Duyler\Parallel\Events;

use Duyler\Parallel\Channel;
use Duyler\Parallel\Future;

final readonly class Event
{
    public function __construct(
        public Type $type,
        public string $source,
        public object $object,
        public mixed $value = null,
    ) {}

    public static function fromNative(\parallel\Events\Event $native): self
    {
        return new self(
            type: Type::from($native->type),
            source: $native->source,
            object: self::wrapObject($native->object),
            value: $native->value ?? null,
        );
    }

    private static function wrapObject(object $object): object
    {
        if ($object instanceof \parallel\Future) {
            return new Future($object);
        }

        if ($object instanceof \parallel\Channel) {
            return Channel::fromNative($object);
        }

        return $object;
    }
}
