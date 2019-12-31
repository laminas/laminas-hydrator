<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ArraySerializableHydrator;
use PHPUnit\Framework\TestCase;

class ArraySerializableTest extends TestCase
{
    public function testTriggerUserDeprecatedError()
    {
        $test = (object) ['message' => false];

        set_error_handler(function ($errno, $errstr) use ($test) {
            $test->message = $errstr;
            return true;
        }, E_USER_DEPRECATED);

        $hydrator = new ArraySerializable();
        restore_error_handler();

        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator);
        $this->assertIsString($test->message);
        $this->assertContains('is deprecated, please use', $test->message);
    }
}
