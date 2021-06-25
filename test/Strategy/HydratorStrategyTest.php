<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Generator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\HydratorStrategy;
use LaminasTest\Hydrator\TestAsset;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

use function count;
use function fopen;
use function get_class;
use function gettype;
use function is_object;
use function mt_getrandmax;
use function mt_rand;
use function spl_object_hash;
use function sprintf;

class HydratorStrategyTest extends TestCase
{
    /**
     * @dataProvider providerInvalidObjectClassName
     * @param mixed $objectClassName
     */
    public function testConstructorRejectsInvalidObjectClassName(
        $objectClassName,
        string $expectedExceptionType,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionType);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new HydratorStrategy(
            $this->createHydratorMock(),
            $objectClassName
        );
    }

    public function providerInvalidObjectClassName(): array
    {
        // @codingStandardsIgnoreStart
        return [
            'array'                     => [[], TypeError::class, 'type string'],
            'boolean-false'             => [false, TypeError::class, 'type string'],
            'boolean-true'              => [true, TypeError::class, 'type string'],
            'float'                     => [mt_rand() / mt_getrandmax(), TypeError::class, 'type string'],
            'integer'                   => [mt_rand(), TypeError::class, 'type string'],
            'null'                      => [null, TypeError::class, 'type string'],
            'object'                    => [new stdClass(), TypeError::class, 'type string'],
            'resource'                  => [fopen(__FILE__, 'r'), TypeError::class, 'type string'],
            'string-non-existent-class' => ['FooBarBaz9000', InvalidArgumentException::class, 'class name needs to be the name of an existing class'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider providerInvalidValueForExtraction
     * @param mixed $value
     */
    public function testExtractRejectsInvalidValue($value): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value needs to be an instance of "%s", got "%s" instead.',
                TestAsset\User::class,
                is_object($value) ? get_class($value) : gettype($value)
            )
        );

        $strategy->extract($value);
    }

    public function providerInvalidValueForExtraction(): ?Generator
    {
        $values = [
            'boolean-false'             => false,
            'boolean-true'              => true,
            'float'                     => mt_rand() / mt_getrandmax(),
            'integer'                   => mt_rand(),
            'null'                      => null,
            'object'                    => new stdClass(),
            'resource'                  => fopen(__FILE__, 'r'),
            'string-non-existent-class' => 'FooBarBaz9000',
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }
    }

    /**
     * @dataProvider providerInvalidObjectForExtraction
     * @param mixed $object
     */
    public function testExtractRejectsInvalidObject($object): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value needs to be an instance of "%s", got "%s" instead.',
                TestAsset\User::class,
                is_object($object) ? get_class($object) : gettype($object)
            )
        );

        $strategy->extract($object);
    }

    public function providerInvalidObjectForExtraction(): ?Generator
    {
        $values = [
            'boolean-false'                           => false,
            'boolean-true'                            => true,
            'float'                                   => mt_rand() / mt_getrandmax(),
            'integer'                                 => mt_rand(),
            'null'                                    => null,
            'object-but-not-instance-of-object-class' => new stdClass(),
            'resource'                                => fopen(__FILE__, 'r'),
            'string-non-existent-class'               => 'FooBarBaz9000',
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }
    }

    public function testExtractUsesHydratorToExtractValues(): void
    {
        $value = new TestAsset\User();

        $extraction = /**
        $extraction =  * @return string[]
        $extraction =  * @psalm-return array{value: string}
                       */
        static function (TestAsset\User $value): array {
            return [
                'value' => spl_object_hash($value),
            ];
        };

        $hydrator = $this->createHydratorMock();

        $hydrator->expects($this->once())
            ->method('extract')
            ->willReturnCallback($extraction);

        $strategy = new HydratorStrategy(
            $hydrator,
            TestAsset\User::class
        );

        $this->assertSame($extraction($value), $strategy->extract($value));
    }

    /**
     * @dataProvider providerInvalidValueForHydration
     * @param mixed $value
     */
    public function testHydrateRejectsInvalidValue($value): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value needs to be an array, got "%s" instead.',
                is_object($value) ? get_class($value) : gettype($value)
            )
        );

        $strategy->hydrate($value);
    }

    public function providerInvalidValueForHydration(): ?Generator
    {
        $values = [
            'boolean-false'             => false,
            'boolean-true'              => true,
            'float'                     => mt_rand() / mt_getrandmax(),
            'integer'                   => mt_rand(),
            'object'                    => new stdClass(),
            'resource'                  => fopen(__FILE__, 'r'),
            'string-non-existent-class' => 'FooBarBaz9000',
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }
    }

    /**
     * @dataProvider providerEmptyOrSameObjects
     * @param mixed $value
     */
    public function testHydrateShouldReturnEmptyOrSameObjects($value): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->assertSame($value, $strategy->hydrate($value));
    }

    public function providerEmptyOrSameObjects(): ?Generator
    {
        $values = [
            'null'                => null,
            'empty-string'        => '',
            TestAsset\User::class => new TestAsset\User(),
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }
    }

    public function testHydrateUsesHydratorToHydrateValues(): void
    {
        $value = ['name' => 'John Doe'];

        $hydration = static function ($data) {
            static $hydrator;

            if (null === $hydrator) {
                $hydrator = new ReflectionHydrator();
            }

            return $hydrator->hydrate(
                $data,
                new TestAsset\User()
            );
        };

        $hydrator = $this->createHydratorMock();

        $hydrator->expects($this->exactly(count($value)))
            ->method('hydrate')
            ->willReturnCallback($hydration);

        $strategy = new HydratorStrategy(
            $hydrator,
            TestAsset\User::class
        );

        $this->assertEquals($hydration($value), $strategy->hydrate($value));
    }

    /**
     * @return MockObject|HydratorInterface
     */
    private function createHydratorMock()
    {
        return $this->createMock(HydratorInterface::class);
    }
}
