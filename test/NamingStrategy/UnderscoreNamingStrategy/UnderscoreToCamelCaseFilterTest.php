<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\UnderscoreToCamelCaseFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function extension_loaded;

#[CoversClass(UnderscoreToCamelCaseFilter::class)]
final class UnderscoreToCamelCaseFilterTest extends TestCase
{
    #[DataProvider('nonUnicodeProvider')]
    public function testFilterCamelCasesNonUnicodeStrings(string $string, string $expected): void
    {
        $filter = new UnderscoreToCamelCaseFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property        = $reflectionClass->getProperty('pcreUnicodeSupport');
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);

        $this->assertNotSame($string, $filtered);
        $this->assertSame($expected, $filtered);
    }

    /**
     * @return string[][]
     * @psalm-return array<string, array{0: string, 1: string}>
     */
    public static function nonUnicodeProvider(): array
    {
        return [
            'one word'                       => [
                'Studly',
                'studly',
            ],
            'multiple words'                 => [
                'studly_cases_me',
                'studlyCasesMe',
            ],
            'alphanumeric in single word'    => [
                'one_2_three',
                'one2Three',
            ],
            'alphanumeric in separate words' => [
                'one2_three',
                'one2Three',
            ],
        ];
    }

    #[DataProvider('unicodeProvider')]
    public function testFilterCamelCasesUnicodeStrings(string $string, string $expected): void
    {
        if (! extension_loaded('mbstring')) {
            $this->markTestSkipped('Extension mbstring not available');
        }

        $filter   = new UnderscoreToCamelCaseFilter();
        $filtered = $filter->filter($string);

        $this->assertNotSame($string, $filtered);
        $this->assertSame($expected, $filtered);
    }

    /**
     * @return string[][]
     * @psalm-return array<string, array{0: string, 1: string}>
     */
    public static function unicodeProvider(): array
    {
        return [
            'uppercase first letter'            => [
                'Camel',
                'camel',
            ],
            'multiple words'                    => [
                'studly_cases_me',
                'studlyCasesMe',
            ],
            'alphanumeric in single word'       => [
                'one_2_three',
                'one2Three',
            ],
            'alphanumeric in separate words'    => [
                'one2_three',
                'one2Three',
            ],
            'unicode character'                 => [
                'test_Šuma',
                'testŠuma',
            ],
            'unicode character [Laminas-10517]' => [
                'test_šuma',
                'testŠuma',
            ],
        ];
    }

    #[DataProvider('unicodeWithoutMbStringsProvider')]
    public function testFilterCamelCasesUnicodeStringsWithoutMbStrings(
        string $string,
        string $expected
    ): void {
        $filter = new UnderscoreToCamelCaseFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property        = $reflectionClass->getProperty('mbStringSupport');
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);
        $this->assertSame($expected, $filtered);
    }

    /**
     * @return string[][]
     * @psalm-return array<string, array{0: string, 1: string}>
     */
    public static function unicodeWithoutMbStringsProvider(): array
    {
        return [
            'multiple words'                 => [
                'studly_cases_me',
                'studlyCasesMe',
            ],
            'alphanumeric in single word'    => [
                'one_2_three',
                'one2Three',
            ],
            'alphanumeric in separate words' => [
                'one2_three',
                'one2Three',
            ],
            'uppercase unicode character'    => [
                'test_Šuma',
                'testŠuma',
            ],
            'lowercase unicode character'    => [
                'test_šuma',
                'test_šuma',
            ],
        ];
    }
}
