<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use PHPUnit\Framework\TestCase;

use function restore_error_handler;
use function set_error_handler;

use const E_USER_DEPRECATED;

class ObjectPropertyTest extends TestCase
{
    public function testTriggerUserDeprecatedError(): void
    {
        $test = (object) ['message' => false];

        /** @psalm-suppress UnusedClosureParam */
        set_error_handler(function ($errno, $errstr) use ($test) {
            $test->message = $errstr;
            return true;
        }, E_USER_DEPRECATED);

        $hydrator = new ObjectProperty();
        restore_error_handler();

        $this->assertInstanceOf(ObjectPropertyHydrator::class, $hydrator);
        $this->assertIsString($test->message);
        $this->assertStringContainsString('is deprecated, please use', $test->message);
    }
}
