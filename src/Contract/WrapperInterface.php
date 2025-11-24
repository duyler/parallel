<?php

declare(strict_types=1);

namespace Duyler\Parallel\Contract;

interface WrapperInterface
{
    public function getNative(): object;
}

interface RuntimeWrapperInterface extends WrapperInterface {}
interface FutureWrapperInterface extends WrapperInterface {}
interface ChannelWrapperInterface extends WrapperInterface {}
interface EventsWrapperInterface extends WrapperInterface {}
