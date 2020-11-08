<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Exception;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\CollectionStrategy;
use Laminas\Hydrator\Strategy\StrategyInterface;
use LaminasTest\Hydrator\TestAsset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use TypeError;

use function array_map;
use function count;
use function fopen;
use function get_class;
use function gettype;
use function is_object;
use function mt_getrandmax;
use function mt_rand;
use function spl_object_hash;
use function sprintf;

/**
 * Tests for {@see CollectionStrategy}
 *
 * @covers \Laminas\Hydrator\Strategy\CollectionStrategy
 */
class CollectionStrategyTest extends TestCase
{
    public function testImplementsStrategyInterface(): void
    {
        $reflection = new ReflectionClass(CollectionStrategy::class);

        $this->assertTrue($reflection->implementsInterface(StrategyInterface::class), sprintf(
            'Failed to assert that "%s" implements "%s"',
            CollectionStrategy::class,
            StrategyInterface::class
        ));
    }

    /**
     * @dataProvider providerInvalidObjectClassName
     *
     * @param mixed $objectClassName
     *
     * @return void
     */
    public function testConstructorRejectsInvalidObjectClassName(
        $objectClassName,
        string $expectedExceptionType,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionType);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new CollectionStrategy(
            $this->createHydratorMock(),
            $objectClassName
        );
    }

    public function providerInvalidObjectClassName() : array
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
            'string-non-existent-class' => ['FooBarBaz9000', Exception\InvalidArgumentException::class, 'class name needs to be the name of an existing class'],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider providerInvalidValueForExtraction
     *
     * @param mixed $value
     *
     * @return void
     */
    public function testExtractRejectsInvalidValue($value): void
    {
        $strategy = new CollectionStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value needs to be an array, got "%s" instead.',
            is_object($value) ? get_class($value) : gettype($value)
        ));

        $strategy->extract($value);
    }

    /**
     * @return \Generator
     */
    public function providerInvalidValueForExtraction()
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
     * @dataProvider providerInvalidObjectForExtraction
     *
     * @param mixed $object
     *
     * @return void
     */
    public function testExtractRejectsInvalidObject($object): void
    {
        $value = [$object];

        $strategy = new CollectionStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value needs to be an instance of "%s", got "%s" instead.',
            TestAsset\User::class,
            is_object($object) ? get_class($object) : gettype($object)
        ));

        $strategy->extract($value);
    }

    /**
     * @return \Generator
     */
    public function providerInvalidObjectForExtraction()
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
        $value = [
            new TestAsset\User(),
            new TestAsset\User(),
            new TestAsset\User(),
        ];

        $extraction = /**
         * @return string[]
         *
         * @psalm-return array{value: string}
         */
        function (TestAsset\User $value): array {
            return [
                'value' => spl_object_hash($value)
            ];
        };

        $hydrator = $this->createHydratorMock();

        $hydrator
            ->expects($this->exactly(count($value)))
            ->method('extract')
            ->willReturnCallback($extraction);

        $strategy = new CollectionStrategy(
            $hydrator,
            TestAsset\User::class
        );

        $expected = array_map($extraction, $value);

        $this->assertSame($expected, $strategy->extract($value));
    }

    /**
     * @dataProvider providerInvalidValueForHydration
     *
     * @param mixed $value
     *
     * @return void
     */
    public function testHydrateRejectsInvalidValue($value): void
    {
        $strategy = new CollectionStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value needs to be an array, got "%s" instead.',
            is_object($value) ? get_class($value) : gettype($value)
        ));

        $strategy->hydrate($value);
    }

    /**
     * @return \Generator
     */
    public function providerInvalidValueForHydration()
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

    public function testHydrateUsesHydratorToHydrateValues(): void
    {
        $value = [
            ['name' => 'Suzie Q.'],
            ['name' => 'John Doe'],
        ];

        $hydration = function ($data) {
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

        $hydrator
            ->expects($this->exactly(count($value)))
            ->method('hydrate')
            ->willReturnCallback($hydration);

        $strategy = new CollectionStrategy(
            $hydrator,
            TestAsset\User::class
        );

        $expected = array_map($hydration, $value);

        $this->assertEquals($expected, $strategy->hydrate($value));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HydratorInterface
     */
    private function createHydratorMock()
    {
        return $this->createMock(HydratorInterface::class);
    }
}
