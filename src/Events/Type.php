<?php

declare(strict_types=1);

namespace Duyler\Parallel\Events;

enum Type: int
{
    case Read = 1;
    case Write = 2;
    case Close = 3;
    case Cancel = 4;
    case Kill = 5;
    case Error = 6;
}
