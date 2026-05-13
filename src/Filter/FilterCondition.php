<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Filter;

enum FilterCondition: int
{
    case Or = 1;

    case And = 2;
}
