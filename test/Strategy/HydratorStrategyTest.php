<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Generator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\HydratorStrategy;
use LaminasTest\Hydrator\TestAsset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use TypeError;

use function count;
use function fopen;
use function get_debug_type;
use function mt_getrandmax;
use function mt_rand;
use function spl_object_hash;
use function sprintf;

#[CoversClass(HydratorStrategy::class)]
final class HydratorStrategyTest extends TestCase
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

        new HydratorStrategy(
            $this->createHydratorMock(),
            $objectClassName
        );
    }

    /** @return non-empty-array<non-empty-string, array{mixed, class-string<Throwable>, string}> */
    public static function providerInvalidObjectClassName(): array
    {
        return [
            'array'                     => [[], TypeError::class, 'type string'],
            'boolean-false'             => [false, TypeError::class, 'type string'],
            'boolean-true'              => [true, TypeError::class, 'type string'],
            'float'                     => [mt_rand() / mt_getrandmax(), TypeError::class, 'type string'],
            'integer'                   => [mt_rand(), TypeError::class, 'type string'],
            'null'                      => [null, TypeError::class, 'type string'],
            'object'                    => [new stdClass(), TypeError::class, 'type string'],
            'resource'                  => [fopen(__FILE__, 'r'), TypeError::class, 'type string'],
            'string-non-existent-class' => [
                'FooBarBaz9000',
                InvalidArgumentException::class,
                'class name needs to be the name of an existing class',
            ],
        ];
    }

    #[DataProvider('providerInvalidValueForExtraction')]
    public function testExtractRejectsInvalidValue(mixed $value): void
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
                get_debug_type($value)
            )
        );

        $strategy->extract($value);
    }

    /** @return Generator<string, array{0: mixed}> */
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

    #[DataProvider('providerInvalidObjectForExtraction')]
    public function testExtractRejectsInvalidObject(mixed $object): void
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
                get_debug_type($object)
            )
        );

        $strategy->extract($object);
    }

    /** @return Generator<string, array{0: mixed}> */
    public static function providerInvalidObjectForExtraction(): Generator
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
        static fn(TestAsset\User $value): array => [
            'value' => spl_object_hash($value),
        ];

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

    #[DataProvider('providerInvalidValueForHydration')]
    public function testHydrateRejectsInvalidValue(mixed $value): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value needs to be an array, got "%s" instead.',
                get_debug_type($value)
            )
        );

        $strategy->hydrate($value);
    }

    /** @return Generator<string, array{0: mixed}> */
    public static function providerInvalidValueForHydration(): Generator
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

    #[DataProvider('providerEmptyOrSameObjects')]
    public function testHydrateShouldReturnEmptyOrSameObjects(mixed $value): void
    {
        $strategy = new HydratorStrategy(
            $this->createHydratorMock(),
            TestAsset\User::class
        );

        $this->assertSame($value, $strategy->hydrate($value));
    }

    /** @return Generator<string, array{0: mixed}> */
    public static function providerEmptyOrSameObjects(): Generator
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

        $hydration = static function ($data): mixed {
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

    private function createHydratorMock(): MockObject&HydratorInterface
    {
        return $this->createMock(HydratorInterface::class);
    }
}
