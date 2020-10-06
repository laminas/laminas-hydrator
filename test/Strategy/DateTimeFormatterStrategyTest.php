<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use DateTime;
use DateTimeImmutable;
use DateTimezone;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see DateTimeFormatterStrategy}
 *
 * @covers \Laminas\Hydrator\Strategy\DateTimeFormatterStrategy
 */
class DateTimeFormatterStrategyTest extends TestCase
{

    public function testHydrate(): void
    {
        $strategy = new DateTimeFormatterStrategy('Y-m-d');
        $this->assertEquals('2014-04-26', $strategy->hydrate('2014-04-26')->format('Y-m-d'));

        $strategy = new DateTimeFormatterStrategy('Y-m-d', new DateTimeZone('Asia/Kathmandu'));

        $date = $strategy->hydrate('2014-04-26');
        $this->assertEquals('Asia/Kathmandu', $date->getTimezone()->getName());
    }

    public function testExtract(): void
    {
        $strategy = new DateTimeFormatterStrategy('d/m/Y');
        $this->assertEquals('26/04/2014', $strategy->extract(new \DateTime('2014-04-26')));
    }

    public function testGetNullWithInvalidDateOnHydration(): void
    {
        $strategy = new DateTimeFormatterStrategy('Y-m-d');
        $this->assertEquals(null, $strategy->hydrate(null));
        $this->assertEquals(null, $strategy->hydrate(''));
    }

    public function testCanExtractIfNotDateTime(): void
    {
        $strategy = new DateTimeFormatterStrategy();
        $date = $strategy->extract(new \stdClass);

        $this->assertInstanceOf(\stdClass::class, $date);
    }

    public function testCanHydrateWithInvalidDateTime(): void
    {
        $strategy = new DateTimeFormatterStrategy('d/m/Y');
        $this->assertSame('foo bar baz', $strategy->hydrate('foo bar baz'));
    }

    public function testCanExtractAnyDateTimeInterface(): void
    {
        $dateMock = $this
            ->getMockBuilder(DateTime::class)
            ->getMock();

        $format = 'Y-m-d';
        $dateMock
            ->expects($this->once())
            ->method('format')
            ->with($format);

        $dateImmutableMock = $this
            ->getMockBuilder(DateTimeImmutable::class)
            ->getMock();

        $dateImmutableMock
            ->expects($this->once())
            ->method('format')
            ->with($format);

        $strategy = new DateTimeFormatterStrategy($format);

        $strategy->extract($dateMock);
        $strategy->extract($dateImmutableMock);
    }

    /**
     * @dataProvider formatsWithSpecialCharactersProvider
     *
     * @param string $format
     * @param string $expectedValue
     *
     * @return void
     */
    public function testAcceptsCreateFromFormatSpecialCharacters($format, $expectedValue): void
    {
        $strategy = new DateTimeFormatterStrategy($format);
        $hydrated = $strategy->hydrate($expectedValue);

        $this->assertInstanceOf(DateTime::class, $hydrated);
        $this->assertEquals($expectedValue, $hydrated->format('Y-m-d'));
    }

    /**
     * @dataProvider formatsWithSpecialCharactersProvider
     *
     * @param string $format
     * @param string $expectedValue
     *
     * @return void
     */
    public function testCanExtractWithCreateFromFormatSpecialCharacters($format, $expectedValue): void
    {
        $date      = DateTime::createFromFormat($format, $expectedValue);
        $strategy  = new DateTimeFormatterStrategy($format);
        $extracted = $strategy->extract($date);

        $this->assertEquals($expectedValue, $extracted);
    }

    public function testCanExtractWithCreateFromFormatEscapedSpecialCharacters(): void
    {
        $date      = DateTime::createFromFormat('Y-m-d', '2018-02-05');
        $strategy  = new DateTimeFormatterStrategy('Y-m-d\\+');
        $extracted = $strategy->extract($date);
        $this->assertEquals('2018-02-05+', $extracted);
    }

    /**
     * @return string[][]
     *
     * @psalm-return array<string, array{0: string, 1: string}>
     */
    public function formatsWithSpecialCharactersProvider(): array
    {
        return [
            '!-prepended' => ['!Y-m-d', '2018-02-05'],
            '|-appended'  => ['Y-m-d|', '2018-02-05'],
            '+-appended'  => ['Y-m-d+', '2018-02-05'],
        ];
    }

    public function testCanHydrateWithDateTimeFallback(): void
    {
        $strategy = new DateTimeFormatterStrategy('Y-m-d', null, true);
        $date = $strategy->hydrate('2018-09-06T12:10:30');

        $this->assertSame('2018-09-06', $date->format('Y-m-d'));

        $strategy = new DateTimeFormatterStrategy('Y-m-d', new DateTimeZone('Europe/Prague'), true);
        $date = $strategy->hydrate('2018-09-06T12:10:30');

        $this->assertSame('Europe/Prague', $date->getTimezone()->getName());
    }

    public function invalidValuesForHydration() : iterable
    {
        return [
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'array'      => [['2018-11-20']],
            'object'     => [(object) ['date' => '2018-11-20']],
        ];
    }

    /**
     * @dataProvider invalidValuesForHydration
     *
     * @param mixed $value
     *
     * @return void
     */
    public function testHydrateRaisesExceptionIfValueIsInvalid($value): void
    {
        $strategy = new DateTimeFormatterStrategy('Y-m-d');

        $this->expectException(InvalidArgumentException::class);

        $strategy->hydrate($value);
    }

    public function validUnhydratableValues() : iterable
    {
        return [
            'empty string' => [''],
            'null'         => [null],
            'date-time'    => [new DateTimeImmutable('now')],
        ];
    }

    /**
     * @dataProvider validUnhydratableValues
     *
     * @param mixed $value
     *
     * @return void
     */
    public function testReturnsValueVerbatimUnderSpecificConditions($value): void
    {
        $strategy = new DateTimeFormatterStrategy('Y-m-d');
        $hydrated = $strategy->hydrate($value);
        $this->assertSame($value, $hydrated);
    }
}
