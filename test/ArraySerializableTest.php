<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ArraySerializableHydrator;
use PHPUnit\Framework\TestCase;

use function restore_error_handler;
use function set_error_handler;

use const E_USER_DEPRECATED;

class ArraySerializableTest extends TestCase
{
    public function testTriggerUserDeprecatedError(): void
    {
        $test = new class {
            /** @var bool|string */
            public $message = false;
        };

        /** @psalm-suppress UnusedClosureParam */
        set_error_handler(static function ($errno, $errstr) use ($test): bool {
            $test->message = $errstr;
            return true;
        }, E_USER_DEPRECATED);

        $hydrator = new ArraySerializable();
        restore_error_handler();

        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator);
        $this->assertIsString($test->message);
        $this->assertStringContainsString('is deprecated, please use', $test->message);
    }
}
