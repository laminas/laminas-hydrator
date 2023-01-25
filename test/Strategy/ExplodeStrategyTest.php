<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\ExplodeStrategy;
use PHPUnit\Framework\TestCase;
use stdClass;

use function is_numeric;

/**
 * Tests for {@see ExplodeStrategy}
 *
 * @covers \Laminas\Hydrator\Strategy\ExplodeStrategy
 */
class ExplodeStrategyTest extends TestCase
{
    /**
     * @dataProvider getValidHydratedValues
     * @param non-empty-string $delimiter
     * @param string[] $extractValue
     */
    public function testExtract(mixed $expected, string $delimiter, array $extractValue): void
    {
        $strategy = new ExplodeStrategy($delimiter);

        if (is_numeric($expected)) {
            self::assertEquals($expected, $strategy->extract($extractValue));
        } else {
            self::assertSame($expected, $strategy->extract($extractValue));
        }
    }

    public function testGetExceptionWithInvalidArgumentOnExtraction(): void
    {
        $strategy = new ExplodeStrategy();

        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress InvalidArgument */
        $strategy->extract('');
    }

    public function testGetEmptyArrayWhenHydratingNullValue(): void
    {
        $strategy = new ExplodeStrategy();

        self::assertSame([], $strategy->hydrate(null));
    }

    public function testGetExceptionWithEmptyDelimiter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress InvalidArgument */
        new ExplodeStrategy('');
    }

    public function testHydrateWithExplodeLimit(): void
    {
        $strategy = new ExplodeStrategy('-', 2);
        self::assertSame(['foo', 'bar-baz-bat'], $strategy->hydrate('foo-bar-baz-bat'));

        $strategy = new ExplodeStrategy('-', 3);
        self::assertSame(['foo', 'bar', 'baz-bat'], $strategy->hydrate('foo-bar-baz-bat'));
    }

    public function testHydrateWithInvalidScalarType(): void
    {
        $strategy = new ExplodeStrategy();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Laminas\Hydrator\Strategy\ExplodeStrategy::hydrate expects argument 1 to be string,'
            . ' array provided instead'
        );

        /** @psalm-suppress InvalidArgument */
        $strategy->hydrate([]);
    }

    public function testHydrateWithInvalidObjectType(): void
    {
        $strategy = new ExplodeStrategy();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Laminas\Hydrator\Strategy\ExplodeStrategy::hydrate expects argument 1 to be string,'
            . ' stdClass provided instead'
        );

        /** @psalm-suppress InvalidArgument */
        $strategy->hydrate(new stdClass());
    }

    public function testExtractWithInvalidObjectType(): void
    {
        $strategy = new ExplodeStrategy();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Laminas\Hydrator\Strategy\ExplodeStrategy::extract expects argument 1 to be array,'
            . ' stdClass provided instead'
        );

        /** @psalm-suppress InvalidArgument */
        $strategy->extract(new stdClass());
    }

    /**
     * @dataProvider getValidHydratedValues
     * @param non-empty-string $delimiter
     */
    public function testHydration(mixed $value, string $delimiter, array $expected): void
    {
        $strategy = new ExplodeStrategy($delimiter);

        /** @psalm-suppress MixedArgument */
        self::assertSame($expected, $strategy->hydrate($value));
    }

    /**
     * @return array<string, array{0: mixed, 1: non-empty-string, 2: list<string>}>
     */
    public function getValidHydratedValues(): array
    {
        // @codingStandardsIgnoreStart
        return [
            'null-comma'                              => [null, ',', []],
            'empty-comma'                             => ['', ',', ['']],
            'string without delimiter-comma'          => ['foo', ',', ['foo']],
            'string with delimiter-comma'             => ['foo,bar', ',', ['foo', 'bar']],
            'string with delimiter-period'            => ['foo.bar', '.', ['foo', 'bar']],
            'string with mismatched delimiter-comma'  => ['foo.bar', ',', ['foo.bar']],
            'integer-comma'                           => [123, ',', ['123']],
            'integer-numeric delimiter'               => [123, '2', ['1', '3']],
            'integer with mismatched delimiter-comma' => [123.456, ',', ['123.456']],
            'float-period'                            => [123.456, '.', ['123', '456']],
            'string containing null-comma'            => ['foo,bar,dev,null', ',', ['foo', 'bar', 'dev', 'null']],
            'string containing null-semicolon'        => ['foo;bar;dev;null', ';', ['foo', 'bar', 'dev', 'null']],
        ];
        // @codingStandardsIgnoreEnd
    }
}
