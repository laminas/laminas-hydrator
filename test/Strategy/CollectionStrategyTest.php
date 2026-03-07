<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Generator;
use Laminas\Hydrator\Exception;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\CollectionStrategy;
use LaminasTest\Hydrator\TestAsset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use TypeError;

use function array_map;
use function count;
use function fopen;
use function get_debug_type;
use function mt_getrandmax;
use function mt_rand;
use function spl_object_hash;
use function sprintf;

#[CoversClass(CollectionStrategy::class)]
final class CollectionStrategyTest extends TestCase
{
    /**
     * @param class-string<Throwable> $expectedExceptionType
     */
    #[DataProvider('providerInvalidObjectClassName')]
    public function testConstructorRejectsInvalidObjectClassName(
        mixed $objectClassName,
        string $expectedExceptionType,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionType);
        $this->expectExceptionMessage($expectedExceptionMessage);

        /** @psalm-suppress MixedArgument */
        new CollectionStrategy(
            $this->createHydratorMock(),
            $objectClassName
        );
    }

    /** @return array<string, array{0:mixed, 1: class-string<Throwable>, 2: string}> */
    public static function providerInvalidObjectClassName(): array
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

    #[DataProvider('providerInvalidValueForExtraction')]
    public function testExtractRejectsInvalidValue(mixed $value): void
    {
        $strategy = new CollectionStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value needs to be an array, got "%s" instead.',
            get_debug_type($value)
        ));

        /** @psalm-suppress MixedArgument */
        $strategy->extract($value);
    }

    /**
     * @return Generator<string, list<mixed>>
     */
    public static function providerInvalidValueForExtraction(): Generator
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

    public function testExtractUsesHydratorToExtractValues(): void
    {
        $value = [
            new TestAsset\User(),
            new TestAsset\User(),
            new TestAsset\User(),
        ];

        /**
          *  $extraction =  * @return string[]
          *  $extraction =  * @psalm-return array{value: string}
          */
        $extraction = static fn(TestAsset\User $value): array => [
            'value' => spl_object_hash($value),
        ];

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

    #[DataProvider('providerInvalidValueForHydration')]
    public function testHydrateRejectsInvalidValue(mixed $value): void
    {
        $strategy = new CollectionStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Value needs to be an array, got "%s" instead.',
            get_debug_type($value)
        ));

        /** @psalm-suppress MixedArgument */
        $strategy->hydrate($value);
    }

    /**
     * @return Generator<string, list<mixed>>
     */
    public static function providerInvalidValueForHydration(): Generator
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

    public function testHydrateUsesHydratorToHydrateValues(): void
    {
        $value = [
            ['name' => 'Suzie Q.'],
            ['name' => 'John Doe'],
        ];

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

    private function createHydratorMock(): HydratorInterface&MockObject
    {
        return $this->createMock(HydratorInterface::class);
    }
}
