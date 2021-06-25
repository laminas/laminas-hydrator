<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use DateTimeImmutable;
use Generator;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy;
use PHPUnit\Framework\TestCase;

class DateTimeImmutableFormatterStrategyTest extends TestCase
{
    /** @var DateTimeImmutableFormatterStrategy */
    private $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DateTimeImmutableFormatterStrategy(
            new DateTimeFormatterStrategy('Y-m-d')
        );
    }

    public function testExtraction(): void
    {
        $this->assertEquals(
            '2020-05-25',
            $this->strategy->extract(new DateTimeImmutable('2020-05-25'))
        );
    }

    public function testHydrationWithDateTimeImmutableObjectShouldReturnSame(): void
    {
        $dateTime = new DateTimeImmutable('2020-05-25');
        $this->assertEquals($dateTime, $this->strategy->hydrate($dateTime));
    }

    public function testHydrationShouldReturnImmutableDateTimeObject(): void
    {
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $this->strategy->hydrate('2020-05-25')
        );
    }

    public function testHydrationShouldReturnDateTimeObjectWithSameValue(): void
    {
        $this->assertSame(
            '2020-05-25',
            $this->strategy->hydrate('2020-05-25')->format('Y-m-d')
        );
    }

    /**
     * @param mixed $value
     * @dataProvider dataProviderForInvalidDateValues
     */
    public function testHydrationShouldReturnInvalidDateValuesAsIs($value): void
    {
        $this->assertSame($value, $this->strategy->hydrate($value));
    }

    public function dataProviderForInvalidDateValues(): Generator
    {
        $values = [
            'null'         => null,
            'empty-string' => '',
            'foo'          => 'foo',
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }
    }
}
