<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use DateTimeImmutable;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateTimeImmutableFormatterStrategy::class)]
class DateTimeImmutableFormatterStrategyTest extends TestCase
{
    private DateTimeImmutableFormatterStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DateTimeImmutableFormatterStrategy(
            new DateTimeFormatterStrategy('Y-m-d')
        );
    }

    public function testExtraction(): void
    {
        self::assertEquals(
            '2020-05-25',
            $this->strategy->extract(new DateTimeImmutable('2020-05-25'))
        );
    }

    public function testHydrationWithDateTimeImmutableObjectShouldReturnSame(): void
    {
        $dateTime = new DateTimeImmutable('2020-05-25');
        self::assertEquals($dateTime, $this->strategy->hydrate($dateTime));
    }

    public function testHydrationShouldReturnImmutableDateTimeObject(): void
    {
        self::assertInstanceOf(
            DateTimeImmutable::class,
            $this->strategy->hydrate('2020-05-25')
        );
    }

    public function testHydrationShouldReturnDateTimeObjectWithSameValue(): void
    {
        self::assertSame(
            '2020-05-25',
            $this->strategy->hydrate('2020-05-25')->format('Y-m-d')
        );
    }

    #[DataProvider('dataProviderForInvalidDateValues')]
    public function testHydrationShouldReturnInvalidDateValuesAsIs(string|null $value): void
    {
        self::assertSame($value, $this->strategy->hydrate($value));
    }

    /** @return array<string, array{0: null|string}> */
    public static function dataProviderForInvalidDateValues(): array
    {
        return [
            'null'         => [null],
            'empty-string' => [''],
            'foo'          => ['foo'],
        ];
    }
}
