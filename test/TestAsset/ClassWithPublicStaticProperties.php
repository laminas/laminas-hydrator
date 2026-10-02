<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class ClassWithPublicStaticProperties
{
    /** @var string */
    public static $foo = 'foo';

    /** @var string */
    public static $bar = 'bar';

    /** @var string */
    public static $baz = 'baz';
}
